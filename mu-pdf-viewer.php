<?php
/**
 * Plugin Name:  MU PDF Viewer Shortcode
 * Description:  Embeds PDFs responsively on any page or post via a simple shortcode.
 *               Uses Google Docs viewer for universal mobile support.
 * Author:       Mohd Akram
 * Author URI:   https://github.com/iamakram22
 * Version:      1.0.0
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package MU_PDF_Viewer
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers the [pdf_viewer] shortcode.
 *
 * Attributes:
 *   id     (int)    – WordPress media library attachment ID.
 *   url    (string) – Direct URL to any publicly accessible PDF.
 *   height (string) – iframe height. Accepts any valid CSS value. Default: 600px.
 *   title  (string) – Accessible title for the iframe. Default: 'PDF Viewer'.
 *
 * Examples:
 *   [pdf_viewer id="42"]
 *   [pdf_viewer url="https://example.com/file.pdf" height="800px"]
 *   [pdf_viewer id="42" title="Annual Report 2024"]
 */
add_shortcode('pdf_viewer', 'mu_pdf_viewer_shortcode');

/**
 * Shortcode callback.
 *
 * @param array $atts Shortcode attributes.
 * @return string      HTML output.
 */
function mu_pdf_viewer_shortcode($atts)
{

    $atts = shortcode_atts(
        array(
            'id' => '',
            'url' => '',
            'height' => '720px',
            'title' => __('PDF Viewer', 'mu-pdf-viewer'),
        ),
        $atts,
        'pdf_viewer'
    );

    // Resolve PDF URL from attachment ID or direct URL attribute.
    $pdf_url = '';

    if (!empty($atts['id']) && is_numeric($atts['id'])) {
        $attachment_id = absint($atts['id']);
        $pdf_url = wp_get_attachment_url($attachment_id);
    } elseif (!empty($atts['url'])) {
        $pdf_url = esc_url_raw($atts['url']);
    }

    if (!$pdf_url) {
        return '<p class="mu-pdf-viewer-error">' . esc_html__('PDF not found. Please check the shortcode attributes.', 'mu-pdf-viewer') . '</p>';
    }

    // Sanitise height: allow only CSS size values (e.g. 600px, 80vh, 100%).
    $height = mu_pdf_viewer_sanitize_css_size($atts['height'], '600px');

    // Build Google Docs viewer URL.
    // Requires the PDF to be publicly accessible on the internet.
    $viewer_url = add_query_arg(
        array(
            'embedded' => 'true',
            'url' => rawurlencode($pdf_url),
        ),
        'https://docs.google.com/gview'
    );

    $iframe_title = sanitize_text_field($atts['title']);
    $unique_id = 'mu-pdf-' . wp_unique_id();

    ob_start();
    ?>
    <div id="<?php echo esc_attr($unique_id); ?>" class="mu-pdf-viewer-wrapper" role="region"
        aria-label="<?php echo esc_attr($iframe_title); ?>">
        <iframe src="<?php echo esc_url($viewer_url); ?>" title="<?php echo esc_attr($iframe_title); ?>"
            class="mu-pdf-viewer-frame" style="height:<?php echo esc_attr($height); ?>;" frameborder="0" allowfullscreen
            loading="lazy" sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe>

        <p class="mu-pdf-viewer-download">
            <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Download PDF', 'mu-pdf-viewer'); ?>
            </a>
        </p>
    </div>

    <style>
        .mu-pdf-viewer-wrapper {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .mu-pdf-viewer-frame {
            display: block;
            width: 100%;
            min-height: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .mu-pdf-viewer-download {
            margin-top: 8px;
            text-align: center;
            font-size: 13px;
        }

        .mu-pdf-viewer-error {
            color: #c0392b;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .mu-pdf-viewer-frame {
                min-height: 250px;
                border-radius: 0;
            }
        }
    </style>
    <?php

    return ob_get_clean();
}

/**
 * Sanitises a CSS size value.
 *
 * Allows values such as 600px, 80vh, 100%, 50em.
 * Falls back to $default if the value contains disallowed characters.
 *
 * @param string $value   The value to sanitise.
 * @param string $default Fallback value.
 * @return string
 */
function mu_pdf_viewer_sanitize_css_size($value, $default = '600px')
{
    $value = trim($value);

    // Allow digits, dots, %, and CSS unit suffixes only.
    if (preg_match('/^[\d.]+(px|em|rem|vh|vw|%)$/', $value)) {
        return $value;
    }

    return $default;
}