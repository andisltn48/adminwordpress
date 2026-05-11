<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\SyncFailedLogController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect()->route("dashboard");
});

Route::get("/dashboard", function () {
    $beritaStats = [
        "total" => \App\Models\Berita::count(),
        "published" => \App\Models\Berita::where(
            "status",
            "Published",
        )->count(),
        "draft" => \App\Models\Berita::where("status", "Draft")->count(),
    ];

    $websiteStats = [
        "total" => \App\Models\Website::count(),
        "aktif" => \App\Models\Website::where("status", 1)->count(),
        "tidak_aktif" => \App\Models\Website::where("status", 0)->count(),
    ];

    $failedStats = [
        "sync_failed" => \App\Models\SyncFailedLog::count(),
        "queue_failed" => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
    ];

    return view("dashboard", compact("beritaStats", "websiteStats", "failedStats"));
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );

    Route::get("/websites/export", [WebsiteController::class, "export"])->name(
        "websites.export",
    );
    Route::get("/histories/export", [
        \App\Http\Controllers\NewsHistoryController::class,
        "export",
    ])->name("histories.export");
    Route::get("/histories", [
        \App\Http\Controllers\NewsHistoryController::class,
        "index",
    ])->name("histories.index");
    Route::resource("websites", WebsiteController::class);
    Route::resource("beritas", BeritaController::class);
    Route::post("beritas/upload-image", [
        BeritaController::class,
        "uploadImage",
    ])->name("beritas.upload_image");

    Route::get("/sync-failed-logs", [
        SyncFailedLogController::class,
        "index",
    ])->name("sync-failed-logs.index");
    Route::post("/sync-failed-logs/{id}/retry", [
        SyncFailedLogController::class,
        "retry",
    ])->name("sync-failed-logs.retry");

    Route::get("/failed-jobs", [
        \App\Http\Controllers\FailedJobController::class,
        "index",
    ])->name("failed-jobs.index");
    Route::post("/failed-jobs/retry-all", [
        \App\Http\Controllers\FailedJobController::class,
        "retryAll",
    ])->name("failed-jobs.retry-all");
    Route::post("/failed-jobs/delete-all", [
        \App\Http\Controllers\FailedJobController::class,
        "deleteAll",
    ])->name("failed-jobs.delete-all");
    Route::post("/failed-jobs/{uuid}/retry", [
        \App\Http\Controllers\FailedJobController::class,
        "retry",
    ])->name("failed-jobs.retry");
    Route::delete("/failed-jobs/{id}", [
        \App\Http\Controllers\FailedJobController::class,
        "destroy",
    ])->name("failed-jobs.destroy");

    Route::get("/beritas/failedlogs", [
        BeritaController::class,
        "rerunFailedJobs",
    ])->name("beritas.rerun_failed");
});

Route::get("/api/berita", [
    \App\Http\Controllers\Api\BeritaApiController::class,
    "getDetail",
]);
Route::get("/api/berita/{id}", [
    \App\Http\Controllers\Api\BeritaApiController::class,
    "getDetailById",
]);
Route::get("/manual-sync-to-wp", [
    BeritaController::class,
    "manualSyncToWP",
])->name("manual-sync-to-wp");
require __DIR__ . "/auth.php";
