<?php

namespace App\Jobs;

use App\Models\Berita;
use App\Models\Website;
use App\Models\NewsHistory;
use App\Models\SyncFailedLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    protected $berita;
    protected $website;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Berita $berita, Website $website, $userId = null)
    {
        $this->berita = $berita;
        $this->website = $website;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $berita = $this->berita;
        $website = $this->website;
        $imagePath = $berita->featured_image;

        $user = $website->username;
        $appPass = $website->password;
        $baseUrl = rtrim($website->url, '/');

        try {
            // 1. UPLOAD GAMBAR DULU (Jika ada)
            $featuredMediaId = null;
            if ($imagePath) {
                $fullPath = storage_path('app/public/' . $imagePath);
                if (file_exists($fullPath)) {
                    $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
                    
                    // Gunakan attach (Multipart) dan index.php untuk menghindari redirect
                    $imageResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                        ->withBasicAuth($user, $appPass)
                        ->withoutVerifying()
                        ->attach(
                            'file', 
                            file_get_contents($fullPath), 
                            basename($fullPath),
                            ['Content-Type' => $mimeType]
                        )
                        ->post($baseUrl . '/index.php?rest_route=/wp/v2/media');
                        Log::info('WP Media Response:', [
                            'status' => $imageResponse->status(),
                            'body'   => $imageResponse->body(),
                            'json'   => $imageResponse->json(),
                        ]);
                    if ($imageResponse->successful()) {
                        $responseData = $this->getCleanJson($imageResponse);

                        if (isset($responseData['id'])) {
                            $featuredMediaId = $responseData['id'];
                        } else {
                            Log::warning("WP Media Upload Success but JSON Invalid. Raw Body: " . $imageResponse->body());
                            SyncFailedLog::updateOrCreate(
                                ['berita_id' => $berita->id, 'website_id' => $website->id],
                                [
                                    'error_message' => 'Upload berhasil tapi respons JSON rusak (mungkin ada PHP Notice di server WP).',
                                    'response_body' => $imageResponse->body(),
                                    'status' => 'failed_image',
                                ]
                            );
                        }
                    } else {
                        SyncFailedLog::updateOrCreate(
                            ['berita_id' => $berita->id, 'website_id' => $website->id],
                            [
                                'error_message' => 'Gagal upload gambar: ' . ($imageResponse->json('message') ?? $imageResponse->body()),
                                'response_body' => $imageResponse->body(),
                                'status' => 'failed_image',
                            ]
                        );
                    }
                }
            }

            // 2. AMBIL CATEGORY ID DARI WORDPRESS
            $categoryId = null;
            if ($berita->kategori) {
                $catResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->withBasicAuth($user, $appPass)
                    ->withoutVerifying()
                    ->get($baseUrl . '/index.php?rest_route=/wp/v2/categories', [
                        'slug' => $berita->kategori
                    ]);

                $catData = $this->getCleanJson($catResponse);
                if ($catResponse->successful() && !empty($catData)) {
                    $categoryId = $catData[0]['id'] ?? null;
                } else {
                    $createCatResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                        ->withBasicAuth($user, $appPass)
                        ->withoutVerifying()
                        ->post($baseUrl . '/index.php?rest_route=/wp/v2/categories', [
                            'name' => ucfirst(str_replace('-', ' ', $berita->kategori)),
                            'slug' => $berita->kategori
                        ]);

                    if ($createCatResponse->successful()) {
                        $createCatData = $this->getCleanJson($createCatResponse);
                        $categoryId = $createCatData['id'] ?? null;
                    } else {
                        $errorResponse = $this->getCleanJson($createCatResponse);
                        if (isset($errorResponse['code']) && $errorResponse['code'] === 'term_exists') {
                            $categoryId = $errorResponse['data']['term_id'] ?? null;
                        }
                    }
                }
            }

            // 3. UPLOAD POSTINGAN
            $postData = [
                'title'   => $berita->judul,
                'content' => $berita->konten,
                'categories' => $categoryId ? [$categoryId] : [],
                'status'  => 'publish',
                'featured_media' => $featuredMediaId,
            ];

            if ($berita->tanggal_publikasi) {
                $postData['date_gmt'] = $berita->tanggal_publikasi->format('Y-m-d\TH:i:s');
            }

            $postResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->withBasicAuth($user, $appPass)
                ->withoutVerifying()
                ->post($baseUrl . '/index.php?rest_route=/wp/v2/posts', $postData);

            if ($postResponse->successful()) {
                $postDataResult = $this->getCleanJson($postResponse);
                $wpPostId = $postDataResult['id'] ?? null;

                if (!$wpPostId) {
                    throw new \Exception("Gagal mendapatkan ID postingan dari respons WordPress.");
                }

                $wpDetailUrl = $baseUrl . '/?p=' . $wpPostId;

                // Update Pivot Table
                $berita->websites()->updateExistingPivot($website->id, [
                    'wp_post_id' => $wpPostId,
                    'detail_url' => $wpDetailUrl,
                ]);

                // Log History (Success)
                NewsHistory::create([
                    'berita_id' => $berita->id,
                    'website_id' => $website->id,
                    'user_id' => $this->userId,
                    'judul' => $berita->judul,
                    'status' => $berita->status,
                    'detail_url' => $wpDetailUrl,
                ]);

                // Remove from failed log if exists
                SyncFailedLog::where('berita_id', $berita->id)
                    ->where('website_id', $website->id)
                    ->delete();

                return;
            }

            // Handle Failure
            $errorMessage = "Gagal Sinkron: " . $postResponse->body();
            SyncFailedLog::updateOrCreate(
                ['berita_id' => $berita->id, 'website_id' => $website->id],
                [
                    'error_message' => $errorMessage,
                    'response_body' => $postResponse->body(),
                    'status' => 'failed',
                ]
            );

        } catch (\Exception $e) {
            Log::error("SyncNewsJob Error: " . $e->getMessage());
            SyncFailedLog::updateOrCreate(
                ['berita_id' => $berita->id, 'website_id' => $website->id],
                [
                    'error_message' => 'Exception: ' . $e->getMessage(),
                    'response_body' => $e->getTraceAsString(),
                    'status' => 'error',
                ]
            );
            throw $e; // Re-throw to let Laravel handle it as a failed job
        }
    }

    /**
     * Clean and decode JSON response that might contain garbage text.
     */
    private function getCleanJson($response)
    {
        $rawBody = $response->body();
        $cleanBody = $rawBody;

        if (!str_starts_with(trim($rawBody), '{') && !str_starts_with(trim($rawBody), '[')) {
            $firstBrace = strpos($rawBody, '{');
            $lastBrace = strrpos($rawBody, '}');
            $firstSquare = strpos($rawBody, '[');
            $lastSquare = strrpos($rawBody, ']');

            $start = false;
            $end = false;

            if ($firstBrace !== false && ($firstSquare === false || $firstBrace < $firstSquare)) {
                $start = $firstBrace;
                $end = $lastBrace;
            } elseif ($firstSquare !== false) {
                $start = $firstSquare;
                $end = $lastSquare;
            }

            if ($start !== false && $end !== false) {
                $cleanBody = substr($rawBody, $start, $end - $start + 1);
            }
        }

        return json_decode($cleanBody, true);
    }
}
