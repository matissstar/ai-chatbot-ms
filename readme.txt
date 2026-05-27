=== Bootflow Shop Assist for WooCommerce ===
Contributors: bootflow
Tags: woocommerce, chatbot, product search, shop assistant, live chat
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Smart product search chatbot for WooCommerce — keyword & fuzzy matching, voice input, product comparison, and analytics.

== Description ==

Bootflow Shop Assist is a lightweight, privacy-focused chatbot for WooCommerce stores. It helps your customers find products instantly using smart keyword and fuzzy search — all without external API calls or data leaving your server.

**Key Features:**

* **Smart Product Search** — Keyword and fuzzy matching across product titles, descriptions, categories, tags, SKUs, and custom fields.
* **Product Comparison** — Side-by-side comparison of multiple products with attributes, prices, and stock status.
* **Voice Input** — Built-in browser-based speech recognition (Web Speech API, no external services).
* **Analytics Dashboard** — Track search queries, conversion rates, top products, and zero-result searches.
* **Custom Responses** — Define keyword-triggered custom responses for FAQs, promotions, or store policies.
* **Starter Questions** — Pre-configured quick-action buttons to guide customers.
* **Delivery & Contact Info** — Automatic delivery zone and contact information from WooCommerce settings.
* **Multi-language** — Built-in translations for 8 languages (LV, EN, DE, RU, LT, ET, ES, FR).
* **White Label** — Customize chatbot name, icon, welcome message, and colors.
* **Theme Customization** — 6 built-in color palettes or fully custom colors via color picker.
* **Import/Export** — Full settings backup and restore.
* **GDPR Ready** — Optional GDPR notice display before chat.
* **Handoff to Messenger** — Optional buttons to redirect to WhatsApp, Telegram, Messenger, Instagram, TikTok, Viber, or email.

**Privacy First:**

* No external API calls — all search is performed locally on your server.
* No tracking, no cookies, no third-party scripts.
* No data leaves your WordPress installation.

**PRO Add-on Available:**

Upgrade to [Bootflow Shop Assist PRO](https://bootflow.io/pro) for AI-powered responses (OpenAI, Claude, Grok, Gemini), Google Speech-to-Text, and priority support.

== Installation ==

1. Upload the `ai-chatbot-ms` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Shop Assist** in the admin menu to configure settings.
4. The chatbot will appear automatically on your store's frontend.

**Requirements:**

* WordPress 5.8 or higher
* WooCommerce 5.0 or higher
* PHP 7.4 or higher

== Third-Party Libraries ==

This plugin includes the following third-party library:

* **Chart.js** (v4.4.7) — Used for the analytics dashboard charts.
  * License: MIT (GPL-compatible)
  * Source: [https://github.com/chartjs/Chart.js](https://github.com/chartjs/Chart.js)
  * Included locally in `assets/js/chart.min.js` — no external CDN calls are made.

The unminified source of the plugin's own JavaScript (`chatbot.js`) is included alongside the minified version (`chatbot.min.js`).

== Frequently Asked Questions ==

= Does this plugin make external API calls? =

No. The FREE version performs all search locally on your server. No data is sent to external services.

= Does it work without WooCommerce? =

The plugin requires WooCommerce for product search features. It will detect WooCommerce pages and posts automatically.

= Can I customize the chatbot appearance? =

Yes. You can change colors (6 palettes or custom), the chatbot name, icon, welcome message, and more from the settings page.

= What languages are supported? =

The chatbot interface supports Latvian, English, German, Russian, Lithuanian, Estonian, Spanish, and French out of the box.

= How does voice input work? =

Voice input uses the browser's built-in Web Speech API. No audio data is sent to external servers. Note: browser support varies.

= Can I add custom responses? =

Yes. Go to **Shop Assist → Custom Responses** to define keyword-triggered answers for common questions.

== Screenshots ==

1. Chatbot on the frontend with product search results.
2. Product comparison view.
3. Admin settings page — appearance and language.
4. Analytics dashboard with search trends.
5. Custom responses editor.

== Changelog ==

= 2.0.0 =
* Complete rewrite with modular architecture.
* Added product comparison feature.
* Added analytics dashboard with Chart.js.
* Added custom responses system.
* Added starter questions.
* Added 6 color palettes and custom color picker.
* Added import/export settings.
* Added GDPR notice option.
* Added multi-language support (8 languages).
* Added white-label options.
* Added handoff to messaging apps.
* Added auto-export products to JSON on post save/delete.
* Removed all external dependencies — fully self-contained.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
Major update with new features. Review settings after upgrading.
