# Super AI Content Generator

> Supercharge your WordPress site with AI-powered content generation. Write a title, click a button, and let the AI do the rest.

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue?logo=wordpress)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple?logo=php)
![License](https://img.shields.io/badge/License-GPLv2%2B-green)
![Version](https://img.shields.io/badge/Version-1.4.0-orange)

---

## Overview

**Super AI Content Generator** is a WordPress plugin that integrates the [Groq API](https://console.groq.com) directly into your post editor. It supports both the **Gutenberg block editor** and the **Classic editor**, and is compatible with page builders like **WP Bakery**.

Just write a post title, click the **"Generate content with AI"** button, and the plugin will generate full HTML content based on a prompt you configure per post type.

---

## Features

- ⚡ **Powered by Groq** — ultra-fast AI inference using models like Llama 3.3 70B
- ✍️ **Works in Gutenberg and Classic editor** — including WP Bakery compatibility
- 🗂️ **Per post type prompts** — configure a different prompt for posts, pages, and custom post types
- 🌍 **Fully translatable** — `.pot` and `es_ES` translation files included
- 🔒 **Secure** — nonce verification, capability checks, and input sanitization
- 🎛️ **Model selector** — choose between Llama, Mixtral, or Gemma models

---

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- A free [Groq API key](https://console.groq.com/keys)

---

## Installation

1. Download or clone this repository into your `wp-content/plugins/` directory:
   ```bash
   cd wp-content/plugins
   git clone https://github.com/jomavazquez/ai-content-generator.git
   ```
2. Activate the plugin from **Plugins → Installed Plugins** in your WordPress admin.
3. Go to **Settings → AI Content Generator** and enter your Groq API key.
4. Configure a prompt for each post type you want to use.

---

## Configuration

### API Key & Model

Go to **Settings → AI Content Generator**:

| Field | Description |
|---|---|
| **Groq API Key** | Your free API key from [console.groq.com/keys](https://console.groq.com/keys) |
| **AI Model** | Choose from Llama 3.3 70B (recommended), Llama 3.1 8B, Mixtral 8x7B, or Gemma 2 9B |

### Prompts

Configure a prompt for each post type. Use `%s` as a placeholder for the post title:

```
Write a complete blog article about: %s. Focus on real estate topics for a Spanish-speaking audience.
```

Leave the field empty to hide the generation button for that post type.

---

## Usage

1. Open any post or page in the editor.
2. Write a title.
3. Click the **"✨ Generate content with AI"** button:
   - In **Gutenberg**: find the button in the **Document** panel on the right sidebar.
   - In **Classic editor / WP Bakery**: find the button just below the title field.
4. The plugin sends your title and prompt to Groq and inserts the generated HTML content directly into the editor.

> If the post already has content, you will be asked to confirm before replacing it.

---

## Supported Models

All models are free on Groq:

| Model | Description |
|---|---|
| `llama-3.3-70b-versatile` | Best quality, recommended |
| `llama-3.1-8b-instant` | Fast and lightweight |
| `mixtral-8x7b-32768` | Good quality, large context |
| `gemma2-9b-it` | Compact model by Google |

---

## File Structure

```
ai-content-generator/
├── ai-content-generator.php   # Main plugin file
├── includes/
│   ├── class-settings.php     # Settings registration and admin menu
│   ├── class-assets.php       # Script/style enqueuing and editor detection
│   └── class-ajax.php         # AJAX handler and Groq API call
├── admin/
│   └── views/
│       └── settings-page.php  # Settings page template
├── assets/
│   ├── css/
│   │   └── admin.css          # Admin styles
│   └── js/
│       ├── classic-editor.js   # Classic editor / WP Bakery integration
│       └── gutenberg-editor.js # Gutenberg block editor integration
└── languages/
    ├── content-generator.pot          # Translation template
    └── content-generator-es_ES.po     # Spanish translation
```

---

## Translations

The plugin is fully translatable. A Spanish (`es_ES`) translation is included.

To compile the `.po` file into a `.mo` file:

```bash
wp i18n make-mo languages/content-generator-es_ES.po
```

To add a new language, copy the `.pot` file, translate the `msgstr` fields, and save it as `content-generator-{locale}.po` in the `languages/` folder.

---

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## Author

**José María Vázquez**
[josemariavazquez.com](https://www.josemariavazquez.com/)