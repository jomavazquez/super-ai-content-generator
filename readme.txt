=== Super AI Content Generator ===
Contributors: jomavazquez
Tags: ai, content, generator, groq, artificial intelligence
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Supercharge your WordPress site with AI-powered content generation. Write a title, click a button, and let the AI do the rest.

== Description ==

**AI Content Generator** integrates the [Groq API](https://console.groq.com) directly into your post editor to generate full HTML content in seconds.

It supports both the **Gutenberg block editor** and the **Classic editor**, and is compatible with page builders like **WP Bakery**.

Just write a post title, click the **"Generate content with AI"** button, and the plugin will generate content based on a prompt you configure per post type.

= Features =

* ⚡ **Powered by Groq** — ultra-fast AI inference using models like Llama 3.3 70B
* ✍️ **Works in Gutenberg and Classic editor** — including WP Bakery compatibility
* 🗂️ **Per post type prompts** — configure a different prompt for posts, pages, and custom post types
* 🌍 **Fully translatable** — `.pot` and `es_ES` translation files included
* 🔒 **Secure** — nonce verification, capability checks, and input sanitization
* 🎛️ **Model selector** — choose between Llama, Mixtral, or Gemma models

= Supported Models =

All models are free on Groq:

* **Llama 3.3 70B** — best quality, recommended
* **Llama 3.1 8B** — fast and lightweight
* **Mixtral 8x7B** — good quality, large context
* **Gemma 2 9B** — compact model by Google

= Privacy =

This plugin sends the post title and your configured prompt to the Groq API (api.groq.com) to generate content. No data is stored externally. Please review [Groq's privacy policy](https://groq.com/privacy-policy/) for details.

== Installation ==

1. Upload the `ai-content-generator` folder to the `/wp-content/plugins/` directory, or install it directly from the WordPress plugin directory.
2. Activate the plugin from **Plugins → Installed Plugins**.
3. Go to **Settings → AI Content Generator** and enter your Groq API key.
4. Configure a prompt for each post type you want to use.

= Getting a Groq API Key =

1. Go to [console.groq.com/keys](https://console.groq.com/keys)
2. Create a free account
3. Generate an API key and paste it in the plugin settings

== Frequently Asked Questions ==

= Is Groq free? =

Yes, Groq offers a free tier with generous rate limits. All models available in this plugin are free to use.

= Does it work with Gutenberg? =

Yes. In Gutenberg, the button appears in the **Document** panel on the right sidebar, under the **"✨ AI Content Generator"** section.

= Does it work with the Classic editor? =

Yes. The button appears just below the title field.

= Does it work with WP Bakery? =

Yes. The plugin detects WP Bakery and syncs the generated content with its editor automatically.

= Can I use it with custom post types? =

Yes. The plugin automatically detects all public custom post types and lets you configure a separate prompt for each one.

= What is %s in the prompt? =

`%s` is a placeholder that gets replaced with the post title when the content is generated. For example:

`Write a blog article about: %s`

becomes:

`Write a blog article about: How to buy a house in Madrid`

= What happens if I leave a prompt empty? =

The generation button will be hidden for that post type.

= Is the generated content inserted directly into the editor? =

Yes. The content is inserted directly into the editor and replaces any existing content (after a confirmation prompt).

== Screenshots ==

1. Settings page — configure your API key, model, and prompts per post type.
2. Classic editor — the generation button appears below the post title.
3. Gutenberg — the generation panel appears in the Document sidebar.

== Changelog ==

= 1.4.0 =
* Added per post type prompt configuration
* Added WP Bakery compatibility
* Added MutationObserver for more reliable button injection
* Improved Gutenberg editor detection via get_current_screen()
* Added translatable prompt suffix

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.4.0 =
This version adds per post type prompts and WP Bakery compatibility. Please review your prompt settings after upgrading.