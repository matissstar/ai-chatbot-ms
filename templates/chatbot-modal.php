<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$t = ai_chatboot_ms_get_strings();
$wl_name = get_option('ai_chatboot_ms_wl_name', '');
$modal_title = !empty($wl_name) ? $wl_name : $t['modal_title'];
?>
<div id="ai-chatboot-ms-chatbot">
  <div class="msai-modal-header">
    <div class="msai-modal-title"><?php echo esc_html($modal_title); ?></div>
    <div class="msai-modal-controls">
      <button class="msai-modal-btn msai-clear-btn" id="msai-clear" title="<?php echo esc_attr($t['btn_clear_title']); ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg></button>
      <button class="msai-modal-btn msai-minimize-btn" id="msai-minimize" title="<?php echo esc_attr($t['btn_minimize_title']); ?>"><svg class="msai-icon-shrink" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="14" y1="10" x2="21" y2="3"/><line x1="3" y1="21" x2="10" y2="14"/></svg><svg class="msai-icon-expand" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg></button>
      <button class="msai-modal-btn msai-close-btn" id="msai-close" title="<?php echo esc_attr($t['btn_close_title']); ?>">×</button>
    </div>
  </div>
  <div class="msai-box">
    <div class="msai-log" id="msai-log"></div>
    <form class="msai-form" id="msai-form">
      <input class="msai-inp" id="msai-q" placeholder="<?php echo esc_attr($t['input_placeholder']); ?>">
      <button class="msai-btn msai-smart-btn msai-smart-voice" type="button" id="msai-smart-btn"><?php echo esc_html($t['btn_voice']); ?></button>
    </form>
    <?php if (get_option('ai_chatboot_ms_wl_powered_by', '0') === '1'): ?>
    <div class="msai-powered-by"><?php echo esc_html($t['powered_by'] ?? 'Powered by'); ?> <a href="https://bootflow.io" target="_blank" rel="noopener">Bootflow Shop Assist</a></div>
    <?php endif; ?>
  </div>
</div>
