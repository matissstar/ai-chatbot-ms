<?php
/**
 * Plugin Name: Bootflow Shop Assist for WooCommerce
 * Description: Smart product search chatbot for WooCommerce — keyword & fuzzy matching, voice input, product comparison, and analytics.
 * Version: 2.0.0
 * Author: Bootflow.io
 * Author URI: https://bootflow.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-chatbot-ms
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.0
 */

if (!defined('ABSPATH')) exit;

define('AI_CHATBOT_MS_VERSION', '2.0.0');
define('AI_CHATBOT_MS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_MS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_CHATBOT_MS_PLUGIN_FILE', __FILE__);

// Legacy constant aliases for backwards compatibility
if (!defined('AI_CHATBOOT_MS_VERSION'))    define('AI_CHATBOOT_MS_VERSION', AI_CHATBOT_MS_VERSION);
if (!defined('AI_CHATBOOT_MS_PLUGIN_DIR')) define('AI_CHATBOOT_MS_PLUGIN_DIR', AI_CHATBOT_MS_PLUGIN_DIR);
if (!defined('AI_CHATBOOT_MS_PLUGIN_URL')) define('AI_CHATBOOT_MS_PLUGIN_URL', AI_CHATBOT_MS_PLUGIN_URL);

// Declare WooCommerce HPOS and Blocks compatibility
add_action('before_woocommerce_init', function () {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
});

// Helper: check if current page is frontend
function ai_chatbot_ms_is_frontend() {
    if (is_admin()) return false;
    if (isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'], true)) return false;
    return true;
}
// Legacy alias
if (!function_exists('ai_chatboot_ms_is_frontend')) {
    function ai_chatboot_ms_is_frontend() { return ai_chatbot_ms_is_frontend(); }
}

// Helper: generate CSS variable overrides from admin palette/custom color settings
function ai_chatbot_ms_get_theme_css() {
    $palettes = [
        'indigo'  => ['primary' => '#6366f1', 'hover' => '#4f46e5', 'light' => 'rgba(99,102,241,0.12)',  'grad_end' => '#8b5cf6'],
        'blue'    => ['primary' => '#3b82f6', 'hover' => '#2563eb', 'light' => 'rgba(59,130,246,0.12)',  'grad_end' => '#60a5fa'],
        'emerald' => ['primary' => '#10b981', 'hover' => '#059669', 'light' => 'rgba(16,185,129,0.12)',  'grad_end' => '#34d399'],
        'rose'    => ['primary' => '#f43f5e', 'hover' => '#e11d48', 'light' => 'rgba(244,63,94,0.12)',   'grad_end' => '#fb7185'],
        'amber'   => ['primary' => '#f59e0b', 'hover' => '#d97706', 'light' => 'rgba(245,158,11,0.12)',  'grad_end' => '#fbbf24'],
        'slate'   => ['primary' => '#475569', 'hover' => '#334155', 'light' => 'rgba(71,85,105,0.12)',   'grad_end' => '#64748b'],
    ];

    $palette = get_option('ai_chatboot_ms_color_palette', 'indigo');
    $font    = get_option('ai_chatboot_ms_font', 'Inter');
    $vars    = [];

    if ($palette === 'custom') {
        $primary = sanitize_hex_color(get_option('ai_chatboot_ms_custom_primary', ''));
        $text    = sanitize_hex_color(get_option('ai_chatboot_ms_custom_text', ''));
        $bg      = sanitize_hex_color(get_option('ai_chatboot_ms_custom_bg', ''));
        if ($primary) {
            $r = hexdec(substr($primary, 1, 2));
            $g = hexdec(substr($primary, 3, 2));
            $b = hexdec(substr($primary, 5, 2));
            $hover = sprintf('#%02x%02x%02x', max(0, $r - 20), max(0, $g - 20), max(0, $b - 20));
            $grad_end = sprintf('#%02x%02x%02x', min(255, $r + 30), min(255, $g + 20), max(0, $b - 10));
            $vars[] = "--msai-primary: {$primary}";
            $vars[] = "--msai-primary-hover: {$hover}";
            $vars[] = "--msai-primary-light: rgba({$r},{$g},{$b},0.12)";
            $vars[] = "--msai-gradient-user: linear-gradient(135deg, {$primary}, {$grad_end})";
            $vars[] = "--msai-gradient-fab: linear-gradient(135deg, {$primary}, {$grad_end})";
        }
        if ($text) {
            $vars[] = "--msai-text: {$text}";
        }
        if ($bg) {
            $r = hexdec(substr($bg, 1, 2));
            $g = hexdec(substr($bg, 3, 2));
            $b = hexdec(substr($bg, 5, 2));
            $vars[] = "--msai-glass-bg: rgba({$r},{$g},{$b},0.72)";
            $vars[] = "--msai-surface: rgba({$r},{$g},{$b},0.85)";
            $vars[] = "--msai-surface-hover: rgba({$r},{$g},{$b},0.95)";
        }
    } elseif ($palette !== 'indigo' && isset($palettes[$palette])) {
        $p = $palettes[$palette];
        $r = hexdec(substr($p['primary'], 1, 2));
        $g = hexdec(substr($p['primary'], 3, 2));
        $b = hexdec(substr($p['primary'], 5, 2));
        $vars[] = "--msai-primary: {$p['primary']}";
        $vars[] = "--msai-primary-hover: {$p['hover']}";
        $vars[] = "--msai-primary-light: {$p['light']}";
        $vars[] = "--msai-gradient-user: linear-gradient(135deg, {$p['primary']}, {$p['grad_end']})";
        $vars[] = "--msai-gradient-fab: linear-gradient(135deg, {$p['primary']}, {$p['grad_end']})";
    }

    // Font: use system font stack in FREE, PRO can override via filter
    $font_family = apply_filters('ai_chatbot_ms_font_family', "'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif");
    $font_safe = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $font);
    if ($font !== 'Inter') {
        $vars[] = "--msai-font: '{$font_safe}', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
    }

    $font_size = absint(get_option('ai_chatboot_ms_font_size', '14'));
    if ($font_size && $font_size !== 14) {
        $vars[] = "--msai-font-size: {$font_size}px";
    }

    $font_style = get_option('ai_chatboot_ms_font_style', 'normal');
    if ($font_style === 'bold') {
        $vars[] = "--msai-font-weight: 700";
    } elseif ($font_style === 'italic') {
        $vars[] = "--msai-font-style-override: italic";
    }

    // Button colors
    $btn_details = sanitize_hex_color(get_option('ai_chatboot_ms_color_details', ''));
    $btn_compare = sanitize_hex_color(get_option('ai_chatboot_ms_color_compare', ''));
    $btn_cart    = sanitize_hex_color(get_option('ai_chatboot_ms_color_cart', ''));
    if ($btn_details) {
        $r = hexdec(substr($btn_details, 1, 2)); $g = hexdec(substr($btn_details, 3, 2)); $b = hexdec(substr($btn_details, 5, 2));
        $vars[] = "--msai-btn-details-bg: rgba({$r},{$g},{$b},0.10)";
        $vars[] = "--msai-btn-details-text: {$btn_details}";
    }
    if ($btn_compare) {
        $r = hexdec(substr($btn_compare, 1, 2)); $g = hexdec(substr($btn_compare, 3, 2)); $b = hexdec(substr($btn_compare, 5, 2));
        $vars[] = "--msai-btn-compare-bg: rgba({$r},{$g},{$b},0.10)";
        $vars[] = "--msai-btn-compare-text: {$btn_compare}";
    }
    if ($btn_cart) {
        $vars[] = "--msai-btn-cart-bg: {$btn_cart}";
        $vars[] = "--msai-btn-cart-text: #ffffff";
    }

    if (empty($vars)) return '';
    return ':root { ' . implode('; ', $vars) . '; }';
}
// Legacy alias
if (!function_exists('ai_chatboot_ms_get_theme_css')) {
    function ai_chatboot_ms_get_theme_css() { return ai_chatbot_ms_get_theme_css(); }
}

// Include classes
require_once AI_CHATBOT_MS_PLUGIN_DIR . 'includes/translations.php';
require_once AI_CHATBOT_MS_PLUGIN_DIR . 'includes/class-chatbot.php';
require_once AI_CHATBOT_MS_PLUGIN_DIR . 'includes/class-admin.php';

// Create/update analytics table on activation
function ai_chatbot_ms_create_tables() {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_chatbot_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(32) NOT NULL DEFAULT '',
        user_id BIGINT UNSIGNED DEFAULT NULL,
        query VARCHAR(500) NOT NULL DEFAULT '',
        query_type VARCHAR(20) NOT NULL DEFAULT 'search',
        results_count INT NOT NULL DEFAULT 0,
        top_product_id BIGINT UNSIGNED DEFAULT NULL,
        added_to_cart TINYINT(1) NOT NULL DEFAULT 0,
        purchased TINYINT(1) NOT NULL DEFAULT 0,
        ai_used TINYINT(1) NOT NULL DEFAULT 0,
        language VARCHAR(5) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_session (session_id),
        KEY idx_created (created_at),
        KEY idx_query_type (query_type)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    update_option('ai_chatboot_ms_db_version', '1.1.0');
}
// Legacy alias
if (!function_exists('ai_chatboot_ms_create_tables')) {
    function ai_chatboot_ms_create_tables() { ai_chatbot_ms_create_tables(); }
}

register_activation_hook(__FILE__, 'ai_chatbot_ms_create_tables');

// Set redirect transient on activation
register_activation_hook(__FILE__, function() {
    set_transient('ai_chatboot_ms_activated', true, 30);
});

// Schedule daily cleanup cron
register_activation_hook(__FILE__, function() {
    if (!wp_next_scheduled('ai_chatboot_ms_cleanup_logs')) {
        wp_schedule_event(time(), 'daily', 'ai_chatboot_ms_cleanup_logs');
    }
    if (!wp_next_scheduled('ai_chatboot_ms_export_products')) {
        wp_schedule_single_event(time() + 10, 'ai_chatboot_ms_export_products');
    }
    if (!wp_next_scheduled('ai_chatboot_ms_check_export')) {
        wp_schedule_event(time() + 300, 'ai_chatboot_ms_5min', 'ai_chatboot_ms_check_export');
    }
});

// Register custom 5-minute interval
add_filter('cron_schedules', function($schedules) {
    $schedules['ai_chatboot_ms_5min'] = [
        'interval' => 300,
        'display'  => 'Every 5 minutes (AI Chatbot)',
    ];
    return $schedules;
});

// Debounced export check
add_action('ai_chatboot_ms_check_export', function() {
    global $ai_chatboot_ms_chatbot;
    if (get_transient('ai_chatboot_ms_needs_export')) {
        delete_transient('ai_chatboot_ms_needs_export');
        if ($ai_chatboot_ms_chatbot && method_exists($ai_chatboot_ms_chatbot, 'export_products_to_json')) {
            $ai_chatboot_ms_chatbot->export_products_to_json();
        }
    }
});

// Remove cron on deactivation
register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('ai_chatboot_ms_cleanup_logs');
    wp_clear_scheduled_hook('ai_chatboot_ms_check_export');
    wp_clear_scheduled_hook('ai_chatboot_ms_export_products');
});

// Cleanup old logs
add_action('ai_chatboot_ms_cleanup_logs', function() {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_chatbot_logs';
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $table is prefix-based
    $wpdb->query($wpdb->prepare("DELETE FROM `{$table}` WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)", 90));
});

// Check DB version on plugins_loaded
add_action('plugins_loaded', function() {
    if (get_option('ai_chatboot_ms_db_version') !== '1.1.0') {
        ai_chatbot_ms_create_tables();
    }
}, 1);

// Initialize the plugin
add_action('plugins_loaded', function() {
    load_plugin_textdomain('ai-chatbot-ms', false, dirname(plugin_basename(__FILE__)) . '/languages');

    global $ai_chatboot_ms_chatbot;
    $ai_chatboot_ms_chatbot = new AI_Chatboot_MS_Chatbot();
    if (is_admin()) {
        new AI_Chatboot_MS_Admin();
    }

    /**
     * Hook: ai_chatbot_ms_loaded
     * Fires after the FREE plugin is fully initialized.
     * PRO add-on hooks here to extend functionality.
     */
    do_action('ai_chatbot_ms_loaded');
});

// Enqueue scripts and styles — NO external CDN calls
add_action('wp_enqueue_scripts', function() {
    if (ai_chatbot_ms_is_frontend()) {
        // Font: allow PRO to enqueue Google Fonts via filter
        $font_url = apply_filters('ai_chatbot_ms_font_url', '');
        if ($font_url) {
            wp_enqueue_style('ai-chatbot-ms-google-font', $font_url, [], null);
            $css_deps = ['ai-chatbot-ms-google-font'];
        } else {
            $css_deps = [];
        }

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style('ai-chatbot-ms-style', AI_CHATBOT_MS_PLUGIN_URL . 'assets/css/chatbot' . $suffix . '.css', $css_deps, AI_CHATBOT_MS_VERSION);
        // Legacy handle alias
        wp_enqueue_style('ai-chatboot-ms-style', AI_CHATBOT_MS_PLUGIN_URL . 'assets/css/chatbot' . $suffix . '.css', $css_deps, AI_CHATBOT_MS_VERSION);

        $inline_css = ai_chatbot_ms_get_theme_css();
        if ($inline_css) {
            wp_add_inline_style('ai-chatbot-ms-style', $inline_css);
        }

        wp_enqueue_script('ai-chatbot-ms-script', AI_CHATBOT_MS_PLUGIN_URL . 'assets/js/chatbot' . $suffix . '.js', ['jquery'], AI_CHATBOT_MS_VERSION, false);

        // Build localize data — PRO can extend via filter
        $localize_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_chatboot_ms_nonce'),
            'i18n' => ai_chatboot_ms_get_strings(),
            'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout/',
            'starter_questions' => ai_chatboot_ms_get_starter_questions(),
            'voice_mode' => get_option('ai_chatboot_ms_voice_mode', 'delayed'),
            'voice_silence' => intval(get_option('ai_chatboot_ms_voice_silence', 4)),
            'show_default_starters' => get_option('ai_chatboot_ms_show_default_starters', '1'),
            'has_google_speech' => '0', // FREE: no Google STT, PRO overrides this
            'gdpr_notice' => get_option('ai_chatboot_ms_gdpr_notice', ''),
            'wl_icon' => get_option('ai_chatboot_ms_wl_icon', ''),
            'wl_welcome' => get_option('ai_chatboot_ms_wl_welcome', ''),
            'wl_powered_by' => get_option('ai_chatboot_ms_wl_powered_by', '0'),
        ];

        /**
         * Filter: ai_chatbot_ms_localize_data
         * PRO uses this to add has_google_speech and other data.
         */
        $localize_data = apply_filters('ai_chatbot_ms_localize_data', $localize_data);

        wp_localize_script('ai-chatbot-ms-script', 'ai_chatboot_ms_ajax', $localize_data);
    }
}, 5);

// Floating button and modal injection
add_action('wp_footer', function() {
    if (ai_chatbot_ms_is_frontend()) {
        ai_chatbot_ms_inject_html();
    }
}, 999);

add_action('wp_body_open', function() {
    if (ai_chatbot_ms_is_frontend()) {
        ai_chatbot_ms_inject_html();
    }
}, 999);

add_action('wp_enqueue_scripts', function() {
    if (ai_chatbot_ms_is_frontend()) {
        $wl_icon = get_option('ai_chatboot_ms_wl_icon', '');
        $btn_icon = !empty($wl_icon) ? esc_js($wl_icon) : '💬';
        $inline_js = 'document.addEventListener("DOMContentLoaded", function() {
            if (!document.getElementById("ai-chatboot-ms-floating-btn")) {
                var btn = document.createElement("div");
                btn.id = "ai-chatboot-ms-floating-btn";
                btn.innerHTML = "' . $btn_icon . '";
                btn.style.cssText = "position:fixed;bottom:20px;right:20px;width:60px;height:60px;background:#0366d6;color:white;border-radius:50%;align-items:center;justify-content:center;cursor:pointer;z-index:999999!important;font-size:24px;box-shadow:0 4px 8px rgba(0,0,0,0.2);transition:all 0.3s ease";
                document.body.appendChild(btn);
            }
        });';
        wp_add_inline_script('ai-chatbot-ms-script', $inline_js, 'before');
    }
}, 6);

function ai_chatbot_ms_inject_html() {
    static $injected = false;
    if ($injected) return;
    $injected = true;
    
    $wl_icon = get_option('ai_chatboot_ms_wl_icon', '');
    $btn_icon = !empty($wl_icon) ? $wl_icon : '💬';

    ob_start();
    include AI_CHATBOT_MS_PLUGIN_DIR . 'templates/chatbot-modal.php';
    $modal_html = ob_get_clean();
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template handles its own escaping
    echo $modal_html;
    echo '<div id="ai-chatboot-ms-floating-btn" style="position:fixed;bottom:20px;right:20px;width:60px;height:60px;background:#0366d6;color:white;border-radius:50%;align-items:center;justify-content:center;cursor:pointer;z-index:999999!important;font-size:24px;box-shadow:0 4px 8px rgba(0,0,0,0.2)">' . esc_html($btn_icon) . '</div>';
}
// Legacy alias
if (!function_exists('ai_chatboot_ms_inject_html')) {
    function ai_chatboot_ms_inject_html() { ai_chatbot_ms_inject_html(); }
}

// Output buffering injection for problematic themes
add_action('template_redirect', function() {
    if (ai_chatbot_ms_is_frontend()) {
        ob_start(function($html) {
            if (stripos($html, 'ai-chatboot-ms-floating-btn') === false && stripos($html, '</body>') !== false) {
                ob_start();
                include AI_CHATBOT_MS_PLUGIN_DIR . 'templates/chatbot-modal.php';
                $modal = ob_get_clean();
                
                $wl_icon_ob = get_option('ai_chatboot_ms_wl_icon', '');
                $btn_icon_ob = !empty($wl_icon_ob) ? esc_html($wl_icon_ob) : '💬';
                
                $injection = $modal . '<div id="ai-chatboot-ms-floating-btn" style="position:fixed;bottom:20px;right:20px;width:60px;height:60px;background:#0366d6;color:white;border-radius:50%;align-items:center;justify-content:center;cursor:pointer;z-index:999999!important;font-size:24px;box-shadow:0 4px 8px rgba(0,0,0,0.2);transition:all 0.3s ease" onclick="document.getElementById(\'ai-chatboot-ms-chatbot\').style.display=\'block\'">' . $btn_icon_ob . '</div>';
                
                $html = str_ireplace('</body>', $injection . '</body>', $html);
            }
            return $html;
        });
    }
}, 1);

add_action('wp_print_footer_scripts', function() {
    if (ai_chatbot_ms_is_frontend()) {
        ai_chatbot_ms_inject_html();
    }
}, 999);
