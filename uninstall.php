<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// --- General settings ---
delete_option('ai_chatboot_ms_language');
delete_option('ai_chatboot_ms_excluded_tags');
delete_option('ai_chatboot_ms_show_default_starters');
delete_option('ai_chatboot_ms_auto_contact');
delete_option('ai_chatboot_ms_gdpr_notice');
delete_option('ai_chatboot_ms_db_version');

// --- Colors & font ---
delete_option('ai_chatboot_ms_color_palette');
delete_option('ai_chatboot_ms_custom_primary');
delete_option('ai_chatboot_ms_custom_text');
delete_option('ai_chatboot_ms_custom_bg');
delete_option('ai_chatboot_ms_color_details');
delete_option('ai_chatboot_ms_color_compare');
delete_option('ai_chatboot_ms_color_cart');
delete_option('ai_chatboot_ms_font');
delete_option('ai_chatboot_ms_font_size');
delete_option('ai_chatboot_ms_font_style');

// --- Voice settings ---
delete_option('ai_chatboot_ms_voice_mode');
delete_option('ai_chatboot_ms_voice_silence');

// --- Handoff settings ---
delete_option('ai_chatboot_ms_handoff_enabled');
delete_option('ai_chatboot_ms_handoff_context');
delete_option('ai_chatboot_ms_handoff_methods');

// --- White-label ---
delete_option('ai_chatboot_ms_wl_name');
delete_option('ai_chatboot_ms_wl_icon');
delete_option('ai_chatboot_ms_wl_welcome');
delete_option('ai_chatboot_ms_wl_admin_name');
delete_option('ai_chatboot_ms_wl_powered_by');

// --- Products export ---
delete_option('ai_chatboot_ms_products_json_path');
delete_option('ai_chatboot_ms_products_json_url');
delete_option('ai_chatboot_ms_products_export_time');

// --- Custom responses & starter questions ---
delete_option('ai_chatboot_ms_custom_responses');
delete_option('ai_chatboot_ms_starter_questions');

// --- Transients ---
delete_transient('ai_chatboot_ms_needs_export');
delete_transient('ai_chatboot_ms_activated');

// --- Delete products JSON file ---
$json_path = get_option('ai_chatboot_ms_products_json_path');
if (empty($json_path)) {
    $upload_dir = wp_upload_dir();
    $json_path = $upload_dir['basedir'] . '/ai-chatboot-products.json';
}
if ($json_path && file_exists($json_path)) {
    @unlink($json_path);
}

// --- Drop analytics table ---
global $wpdb;
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is prefix-based, not user input
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ai_chatbot_logs`");

// --- Clear all scheduled cron hooks ---
wp_clear_scheduled_hook('ai_chatboot_ms_cleanup_logs');
wp_clear_scheduled_hook('ai_chatboot_ms_check_export');
wp_clear_scheduled_hook('ai_chatboot_ms_export_products');

/**
 * Hook: ai_chatbot_ms_uninstall
 * PRO uses this to clean up its own options and data.
 */
do_action('ai_chatbot_ms_uninstall');
?>
