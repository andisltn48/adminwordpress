<?php
/**
 * Template Standalone untuk Halaman Detail Berita
 * Tidak menggunakan template WordPress (tanpa header, sidebar, footer)
 */

if (!defined('ABSPATH')) {
    exit;
}

$news = $GLOBALS['ngapi_current_news'];
$date_str = date_i18n('d F Y, H:i', strtotime($news['created_at']));
$back_url = home_url('/');
$site_name = get_bloginfo('name');
$css_url = plugin_dir_url(dirname(__FILE__)) . 'assets/style.css';

// Ambil berita lainnya (sama seperti di home)
$other_news = news_grid_api_fetch_list(10);
// Filter agar berita yang sedang dilihat tidak muncul di sidebar
$other_news = array_filter($other_news, function($item) use ($news) {
    return $item['id'] != $news['id'];
});
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($news['judul']); ?> - <?php echo esc_html($site_name); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url($css_url); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body class="ngapi-detail-page">

    <div class="ngapi-detail-layout">
        <!-- Konten Utama -->
        <article class="ngapi-detail">
            <a href="<?php echo esc_url($back_url); ?>" class="ngapi-detail-back">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>

            <header class="ngapi-detail-header">
                <h1 class="ngapi-detail-title"><?php echo esc_html($news['judul']); ?></h1>
                <div class="ngapi-detail-meta">
                    <?php if (!empty($news['kategori'])): ?>
                    <span class="ngapi-card-badge">
                        <?php echo esc_html($news['kategori']); ?>
                    </span>
                    <?php endif; ?>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span><?php
                        if (!empty($news['tanggal_publikasi'])) {
                            echo esc_html(date_i18n('d F Y', strtotime($news['tanggal_publikasi'])));
                        } else {
                            echo esc_html($date_str);
                        }
                    ?></span>
                </div>
            </header>

            <?php if (!empty($news['featured_image'])): ?>
            <div class="ngapi-detail-hero">
                <img src="<?php echo esc_url($news['featured_image']); ?>" alt="<?php echo esc_attr($news['judul']); ?>">
            </div>
            <?php endif; ?>

            <div class="ngapi-detail-content">
                <?php echo wp_kses_post($news['konten']); ?>
            </div>
        </article>

        <!-- Sidebar: Berita Lainnya -->
        <aside class="ngapi-sidebar">
            <h3 class="ngapi-sidebar-title">Berita Lainnya</h3>
            <div class="ngapi-sidebar-list">
                <?php if (!empty($other_news)): ?>
                    <?php foreach ($other_news as $item):
                        $item_url = home_url('/detail_berita/' . $item['id'] . '/');
                        $item_date = !empty($item['tanggal_publikasi'])
                            ? date_i18n('d M Y', strtotime($item['tanggal_publikasi']))
                            : date_i18n('d M Y', strtotime($item['created_at']));
                        $item_img = !empty($item['featured_image']) ? $item['featured_image'] : '';
                    ?>
                    <a href="<?php echo esc_url($item_url); ?>" class="ngapi-sidebar-item">
                        <?php if ($item_img): ?>
                        <div class="ngapi-sidebar-item-img">
                            <img src="<?php echo esc_url($item_img); ?>" alt="<?php echo esc_attr($item['judul']); ?>" loading="lazy">
                        </div>
                        <?php else: ?>
                        <div class="ngapi-sidebar-item-img ngapi-sidebar-item-img--placeholder">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <?php endif; ?>
                        <div class="ngapi-sidebar-item-body">
                            <h4 class="ngapi-sidebar-item-title"><?php echo esc_html($item['judul']); ?></h4>
                            <span class="ngapi-sidebar-item-date"><?php echo esc_html($item_date); ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#94a3b8; font-size:0.85rem;">Tidak ada berita lainnya.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
