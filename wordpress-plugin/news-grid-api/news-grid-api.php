<?php
/**
 * Plugin Name: News Grid from API
 * Plugin URI: https://lenterasaijaan.com/
 * Description: Menampilkan list berita dari API Laravel dengan tampilan grid horizontal dan halaman detail.
 * Version: 1.0.0
 * Author: Antigravity AI
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// KONFIGURASI
// ============================================================
define('NEWS_GRID_API_BASE_URL', 'https://aquamarine-gnat-818015.hostingersite.com');

// ============================================================
// HOOKS
// ============================================================
add_shortcode('news_grid_api', 'news_grid_api_render');
add_action('wp_enqueue_scripts', 'news_grid_api_enqueue_assets');
add_action('init', 'news_grid_api_rewrite_rules');
add_filter('query_vars', 'news_grid_api_query_vars');
add_filter('template_include', 'news_grid_api_template');

// Flush rewrite saat aktivasi plugin
register_activation_hook(__FILE__, 'news_grid_api_activate');
function news_grid_api_activate() {
    news_grid_api_rewrite_rules();
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

// ============================================================
// REWRITE RULES (untuk URL /detail_berita/123)
// ============================================================
function news_grid_api_rewrite_rules() {
    add_rewrite_rule(
        '^detail_berita/([0-9]+)/?$',
        'index.php?news_detail_id=$matches[1]',
        'top'
    );
}

function news_grid_api_query_vars($vars) {
    $vars[] = 'news_detail_id';
    return $vars;
}

// ============================================================
// ENQUEUE CSS & JS
// ============================================================
function news_grid_api_enqueue_assets() {
    wp_enqueue_style(
        'news-grid-api-style',
        plugin_dir_url(__FILE__) . 'assets/style.css',
        [],
        '1.0.0'
    );
}

// ============================================================
// FUNGSI FETCH DATA DARI API
// ============================================================
function news_grid_api_fetch_list($limit = 10) {
    $home_url = home_url();
    $cache_key = 'news_grid_api_list_' . md5($home_url . '_' . $limit);

    // HAPUS CACHE LAMA (hapus baris ini setelah berhasil)
    delete_transient($cache_key);

    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $api_url = NEWS_GRID_API_BASE_URL . '/api/berita?' . http_build_query([
        'url'   => $home_url,
        'limit' => $limit,
    ]);

    $response = wp_remote_get($api_url, ['timeout' => 15]);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['status']) && $data['status'] === 'success' && !empty($data['data'])) {
        set_transient($cache_key, $data['data'], HOUR_IN_SECONDS);
        return $data['data'];
    }

    return [];
}

function news_grid_api_fetch_single($id) {
    $cache_key = 'news_grid_api_single_' . intval($id);
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $api_url = NEWS_GRID_API_BASE_URL . '/api/berita/' . intval($id);
    $response = wp_remote_get($api_url, ['timeout' => 15]);

    if (is_wp_error($response)) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['status']) && $data['status'] === 'success' && !empty($data['data'])) {
        set_transient($cache_key, $data['data'], HOUR_IN_SECONDS);
        return $data['data'];
    }

    return null;
}

// ============================================================
// SHORTCODE: [news_grid_api limit="3"]
// ============================================================
function news_grid_api_render($atts) {
    $atts = shortcode_atts([
        'limit' => 3,
    ], $atts, 'news_grid_api');

    $news_list = news_grid_api_fetch_list(intval($atts['limit']));

    if (empty($news_list)) {
        return '<div class="ngapi-empty">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <p>Tidak ada berita ditemukan.</p>
        </div>';
    }

    $output = '<div class="ngapi-grid">';

    foreach ($news_list as $news) {
        $detail_url = home_url('/detail_berita/' . $news['id'] . '/');
        $img_url    = !empty($news['featured_image']) ? $news['featured_image'] : '';
        $date_str   = !empty($news['tanggal_publikasi'])
            ? date_i18n('d M Y', strtotime($news['tanggal_publikasi']))
            : date_i18n('d M Y', strtotime($news['created_at']));
        $kategori   = !empty($news['kategori']) ? $news['kategori'] : '';
        // Ambil excerpt dari konten (strip HTML, potong 120 karakter)
        $excerpt    = wp_trim_words(wp_strip_all_tags($news['konten']), 18, '…');

        $img_html = '';
        if ($img_url) {
            $img_html = '<div class="ngapi-card-img">
                <img src="' . esc_url($img_url) . '" alt="' . esc_attr($news['judul']) . '" loading="lazy">
            </div>';
        } else {
            $img_html = '<div class="ngapi-card-img ngapi-card-img--placeholder">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>';
        }

        $kategori_html = '';
        if ($kategori) {
            $kategori_html = '<span class="ngapi-card-badge">' . esc_html($kategori) . '</span>';
        }

        $output .= '
        <a href="' . esc_url($detail_url) . '" class="ngapi-card">
            ' . $img_html . '
            <div class="ngapi-card-body">
                ' . $kategori_html . '
                <h3 class="ngapi-card-title">' . esc_html($news['judul']) . '</h3>
                <p class="ngapi-card-excerpt">' . esc_html($excerpt) . '</p>
                <div class="ngapi-card-meta">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>' . esc_html($date_str) . '</span>
                </div>
            </div>
        </a>';
    }

    $output .= '</div>';

    return $output;
}

// ============================================================
// TEMPLATE: HALAMAN DETAIL BERITA
// ============================================================
function news_grid_api_template($template) {
    $news_id = get_query_var('news_detail_id');

    if (!$news_id) {
        return $template;
    }

    $news = news_grid_api_fetch_single($news_id);

    if (!$news) {
        // Tampilkan 404
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        return get_404_template();
    }

    // Simpan data berita di global agar bisa diakses template
    $GLOBALS['ngapi_current_news'] = $news;

    // Gunakan template standalone (tanpa header/sidebar/footer WordPress)
    return plugin_dir_path(__FILE__) . 'templates/detail-news.php';
}
