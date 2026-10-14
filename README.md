# WPCalibrate Timeline Builder

[![WordPress Compatibility](https://img.shields.io/badge/WordPress-6.2%20to%207.1.3-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Release](https://img.shields.io/badge/Release-v1.0.0-orange.svg)](https://github.com/zeeshanraza-official/wpcalibrate-timeline-builder/releases)

A lightweight, high-performance, and responsive WordPress timeline and roadmap builder. Easily design, customize, and display company histories, product release roadmaps, project milestones, event schedules, and step-by-step processes using vertical or horizontal layouts.

---

## Table of Contents

- [About](#about)
- [Key Features](#features)
- [Documentation & How to Use](#documentation--how-to-use)
  - [System Requirements](#system-requirements)
  - [Installation](#installation)
  - [Creating Your First Timeline](#creating-your-first-timeline)
  - [Embedding Options](#embedding-options)
    - [Gutenberg Block](#gutenberg-block)
    - [Elementor Widget](#elementor-widget)
    - [Shortcode Usage](#shortcode-usage)
  - [Customization & Styling](#customization--styling)
  - [Automatic Dashboard Updates](#automatic-dashboard-updates)
- [Changelog](#changelog)
- [Architecture & Standards](#architecture--standards)
- [Author & Support](#author--support)

---

## About

**WPCalibrate Timeline Builder** is built from the ground up to provide WordPress administrators, site owners, and developers with a robust, bloat-free timeline solution. Unlike bulky page-builder add-on packs, WPCalibrate Timeline Builder focuses strictly on speed, clean semantic HTML5, deterministic data structures, and intuitive customization.

### Why Choose WPCalibrate Timeline Builder?
- **Zero jQuery Bloat**: Built using vanilla ES6+ JavaScript and modern CSS custom properties.
- **Conditional Asset Loading**: Scripts and stylesheets only enqueue on pages where a timeline is actively rendered, ensuring zero impact on your core page load speed.
- **Accessible (WCAG 2.1 AA Compliant)**: Full keyboard navigation (Arrow keys, Home, End), screen-reader-friendly semantic structure (`<ol>`, `<li>`, `aria-*` tags), and high-contrast color defaults.
- **Extensible & Secure**: Strict PHP 8.2+ type safety, custom post types (`wpcal_timeline`), nonces on all AJAX operations, and sanitized inputs/escaped outputs throughout.

---

## Features

### 1. Flexible Layouts
- **Vertical Timeline**:
  - Alternating layout (left and right zigzag cards).
  - One-sided left-aligned layout.
  - One-sided right-aligned layout.
- **Horizontal Timeline Slider**:
  - Smooth horizontal scrolling carousel with card cards and connected nodes.
  - Interactive navigation controls (side chevron buttons or top/bottom alignment).
  - Touch-swipe support on mobile and tablet devices.
  - Keyboard navigation for accessibility.

### 2. Five Built-in Design Presets
- **Modern**: Rounded cards, subtle border highlights, gradient markers.
- **Clean**: Crisp contrast, subtle shadows, and streamlined padding.
- **Minimalist**: Thin outline borders, neutral grayscale palette, unobtrusive connectors.
- **Elegant**: Sophisticated serif typography, refined borders, and soft shadows.
- **Bold**: High-impact cards with solid borders and vibrant accents.

### 3. Comprehensive Styling Controls
- Custom primary and secondary accent colors.
- Card background, text, and border customization.
- Milestone node/marker fill colors and outline colors.
- Spine/connecting line color and thickness.
- Dedicated slider button controls (size, icon color, background color, hover states, and placement).

### 4. Interactive Timeline Editor
- Native WordPress admin interface mirroring the familiar WP look and feel.
- Drag-and-drop item reordering.
- Date and time pickers with custom formatting.
- Image uploads powered directly by the native WordPress Media Library.
- Milestone status badges (e.g., Completed, In Progress, Upcoming, Postponed).
- Live shortcode display with instant copy button.
- One-click duplicate timeline action.

### 5. Multi-Builder Integration
- **Gutenberg**: Native block (`wpcalibrate/timeline`) with live inspector controls and real-time canvas preview.
- **Elementor**: Dedicated Elementor widget (`wpcal_timeline_widget`) registered in the WPCalibrate category with full control over timeline selection and layout overrides.
- **Shortcode**: Universal `[wpcal_timeline id="..."]` with parameter overrides for page builders, templates, and theme hooks.

---

## Documentation & How to Use

### System Requirements
- **PHP**: 8.2 or higher (fully compatible with PHP 8.3).
- **WordPress**: 6.2 or higher (tested up to 7.1.3).
- **Web Server**: Apache, Nginx, or LiteSpeed with standard PHP extensions.

---

### Installation

#### Method 1: WordPress Admin Upload (Recommended)
1. Download the latest `wpcalibrate-timeline-builder-1.0.0.zip` from the [GitHub Releases](https://github.com/zeeshanraza-official/wpcalibrate-timeline-builder/releases).
2. Go to your WordPress Dashboard > **Plugins** > **Add New** > **Upload Plugin**.
3. Choose the ZIP file and click **Install Now**.
4. Click **Activate Plugin**.

#### Method 2: Manual Installation via FTP/SFTP
1. Extract the `wpcalibrate-timeline-builder-1.0.0.zip` archive.
2. Upload the unzipped `wpcalibrate-timeline-builder` folder to `/wp-content/plugins/` on your server.
3. In WordPress Admin, go to **Plugins** > **Installed Plugins** and activate **WPCalibrate Timeline Builder**.

---

### Creating Your First Timeline

1. Navigate to **WPCalibrate Timeline** in your WordPress admin menu.
2. Click **Add New Timeline**.
3. Enter a Title (e.g., *Our Company Story* or *Product Roadmap 2026*).
4. In the **Timeline Items** section, click **+ Add Milestone**:
   - Set the milestone **Date / Time**.
   - Enter a **Title** and **Description**.
   - (Optional) Choose an **Icon**, upload an **Image**, or assign a **Status Badge**.
5. In the **Timeline Settings** sidebar:
   - Select your **Layout** (Vertical or Horizontal).
   - Select your preferred **Preset**.
   - Adjust colors and typography in the **Colors & Styling** tab.
6. Click **Publish** or **Update**.

---

### Embedding Options

#### Gutenberg Block
1. In the Block Editor, click the **+ (Add block)** button.
2. Search for **Timeline** or **WPCalibrate Timeline**.
3. Select your timeline from the dropdown in the block sidebar settings.
4. Customize overrides if needed and publish the page.

#### Elementor Widget
1. Open any page in the Elementor Editor.
2. In the widget panel, search for **WPCalibrate Timeline**.
3. Drag the widget onto your section.
4. Select your timeline ID from the dropdown list.
5. Apply any additional Elementor styling or spacing controls.

#### Shortcode Usage
Use the basic shortcode to display a timeline using its saved settings:
```html
[wpcal_timeline id="12"]
```

You can also override layout and preset options directly in the shortcode:
```html
[wpcal_timeline id="12" layout="horizontal" preset="modern"]
```

#### Supported Shortcode Attributes
| Attribute | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `id` | `int` | *(Required)* | The Post ID of the timeline. |
| `layout` | `string` | Saved in DB | Override layout: `vertical` or `horizontal`. |
| `alignment`| `string` | Saved in DB | Vertical alignment: `alternating`, `left`, or `right`. |
| `preset` | `string` | Saved in DB | Visual preset: `modern`, `clean`, `minimalist`, `elegant`, `bold`. |
| `order` | `string` | `ASC` | Milestone sort order: `ASC` (chronological) or `DESC` (reverse). |
| `limit` | `int` | `0` | Maximum number of items to display (`0` for all). |

---

### Customization & Styling

Under the **Colors & Styling** panel in the Timeline Editor, you can fine-tune:
- **Card Background & Text**: Set background color, text color, and border radius.
- **Marker & Connector Spine**: Control marker node color, size, and line thickness.
- **Horizontal Slider Navigation**:
  - Adjust side button dimensions and icon size.
  - Set button background, hover background, and icon color.
  - Position buttons on the sides or aligned to the top/bottom.

---

### Automatic Dashboard Updates

WPCalibrate Timeline Builder includes native GitHub update metadata headers:
```php
GitHub Plugin URI: https://github.com/zeeshanraza-official/wpcalibrate-timeline-builder
Primary Branch: main
```

If you use the **Git Updater** plugin (or any GitHub update manager), updates will automatically appear in your WordPress **Dashboard > Updates** and **Plugins** page whenever a new release tag is published on GitHub.

---

## Changelog

### [1.0.0] - 2026-10-14
#### Added
- Initial production release.
- Custom Post Type `wpcal_timeline` with meta capabilities.
- Native admin metabox interface with drag-and-drop milestone reordering.
- Vertical layout with Alternating, Left, and Right alignments.
- Horizontal layout with smooth slider navigation, touch-swipe, and keyboard controls.
- 5 visual design presets: Modern, Clean, Minimalist, Elegant, and Bold.
- Comprehensive Colors & Styling customizer with persistent database storage.
- Custom sliding button controls (dimensions, colors, hovers, positioning).
- Native Gutenberg Block with server-side render preview.
- Elementor Widget integration under dedicated category.
- Universal shortcode `[wpcal_timeline]` with dynamic override attributes.
- Built-in Overview guide explaining purpose, features, and setup.
- License management screen supporting WPCalibrate marketplace activation.
- Fully translatable codebase with `.pot` template and localized strings.
- 100% WCAG 2.1 AA accessible front-end markup.
- Zero external JavaScript libraries or jQuery dependencies.

---

## Architecture & Standards

- **Namespaced PSR-4 Structure**: Organized under `\WPCalibrate\TimelineBuilder\`.
- **WordPress Coding Standards**: Adheres to WordPress PHP Coding Standards (WPCS) with strict escaping (`esc_html`, `esc_attr`, `wp_kses`) and input sanitization.
- **Database Hygiene**: Uses custom post types and structured post meta. Clean `uninstall.php` handler removes options and post meta only upon user-confirmed deletion.
- **Security**: Nonce validation on all AJAX endpoints and capability checks (`edit_posts`, `manage_options`).

---

## Author & Support

- **Author**: [WPCalibrate](https://wpcalibrate.com)
- **Marketplace**: [marketplace.wpcalibrate.com](https://marketplace.wpcalibrate.com/)
- **Email Support**: [support@wpcalibrate.com](mailto:support@wpcalibrate.com)
- **Phone / WhatsApp**: [+447474795976](https://wa.me/447474795976)

---

*WPCalibrate Timeline Builder is released under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).*
