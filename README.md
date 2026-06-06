# MU PDF Viewer Shortcode

A lightweight WordPress must-use (MU) plugin that embeds PDFs responsively on any page or post via a simple shortcode. Solves the well-known Gutenberg `core/file` block limitation where PDFs render as a download link instead of an inline viewer on mobile devices.

## Why This Exists

The default WordPress Gutenberg file block uses an `<object>` tag to embed PDFs. Mobile browsers (iOS Safari, Android Chrome) do not support `<object>`-based PDF rendering and silently fall back to showing a download link. This plugin replaces that approach with a Google Docs viewer `<iframe>`, which works reliably across all devices and browsers.

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- The PDF must be **publicly accessible on the internet** (standard WordPress media uploads are public by default)

## Installation

1. Download or clone this repository.
2. Copy `mu-pdf-viewer.php` into your WordPress `wp-content/mu-plugins/` directory.
   - If the `mu-plugins` folder does not exist, create it.
3. That's it. MU plugins activate automatically — no activation step required.

```
wp-content/
└── mu-plugins/
    └── mu-pdf-viewer.php
```

## Usage

### Using a Media Library Attachment ID

Upload your PDF to the WordPress Media Library, then use its attachment ID:

```
[pdf_viewer id="42"]
```

### Using a Direct URL

```
[pdf_viewer url="https://example.com/files/report.pdf"]
```

### With Custom Height

Any valid CSS height value is accepted:

```
[pdf_viewer id="42" height="800px"]
[pdf_viewer id="42" height="80vh"]
```

### With a Custom Accessible Title

The `title` attribute sets the `<iframe>` title for screen readers:

```
[pdf_viewer id="42" title="Annual Report 2024"]
```

## Shortcode Attributes

| Attribute | Type   | Default       | Description                                                         |
|-----------|--------|---------------|---------------------------------------------------------------------|
| `id`      | int    | —             | WordPress media library attachment ID (preferred)                   |
| `url`     | string | —             | Direct URL to any publicly accessible PDF                           |
| `height`  | string | `720px`       | iframe height — any valid CSS value (`px`, `vh`, `%`, `em`, `rem`) |
| `title`   | string | `PDF Viewer`  | Accessible `<iframe>` title for screen readers                      |

Either `id` or `url` is required. If both are provided, `id` takes priority.

## How It Works

The shortcode generates a responsive `<iframe>` pointing to `https://docs.google.com/gview?embedded=true&url={pdf_url}`. Google's viewer renders the PDF as an HTML-based viewer, offloading all rendering to Google's servers and avoiding the canvas memory constraints that cause blank/white displays in mobile browsers.

A **Download PDF** fallback link is always rendered beneath the viewer so users can access the file directly if the iframe fails to load (e.g., in restrictive network environments).

## Limitations

| Limitation | Notes |
|------------|-------|
| Requires public PDF URL | PDFs behind authentication or on localhost will not load |
| Depends on Google Docs availability | If Google's viewer is unavailable, the iframe will be blank |
| No self-hosted rendering | For fully self-hosted rendering, consider hosting PDF.js `/web/viewer.html` |

## Changelog

### 1.0.0
- Initial release with Google Docs viewer iframe
- Full mobile support (iOS Safari, Android Chrome)
- Added `title` attribute for accessibility
- Added CSS size sanitisation for `height` attribute
- Added `sandbox` attribute on iframe for improved security
- Added `loading="lazy"` for performance
- Inline styles scoped to `.mu-pdf-viewer-wrapper` to avoid theme conflicts

## License

This plugin is licensed under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license, consistent with WordPress core licensing requirements.

## Author

**Mohd Akram** — [GitHub (iamakram22)](https://github.com/iamakram22)