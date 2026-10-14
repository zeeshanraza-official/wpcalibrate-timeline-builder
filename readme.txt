=== WPCalibrate Timeline Builder ===
Contributors: wpcalibrate
Tags: timeline, history, roadmap, milestone, process
Requires at least: 6.2
Tested up to: 7.1.3
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create responsive vertical and horizontal timelines, company history, roadmaps, and milestone journeys with Gutenberg, Shortcode, and Elementor integrations.

== Description ==

WPCalibrate Timeline Builder is a lightweight, responsive WordPress timeline and roadmap plugin built to present company history, product releases, project milestones, and event journeys clearly.

= Key Features =
* **Unlimited Timelines**: Create and publish as many separate timelines as your website needs.
* **Vertical Layout**: Alternating cards or single-side mode with central or side timeline tracks, collapsing gracefully on mobile.
* **Horizontal Layout**: Touch-friendly, keyboard-accessible horizontal timeline tracks with scroll snapping and navigation controls.
* **Four Flexible Date Modes**: Exact dates, Month & Year, Year Only, and Custom Text (e.g. "Phase One", "Founded", "Coming Soon") without forcing Gregorian dates.
* **Five Visual Presets**: Choose between Clean, Cards, Minimal, Modern, and Compact styling.
* **Per-Timeline Styling**: Fine-tune colors, marker node sizes, border radius, and container widths.
* **Accessible Ordering**: Manual drag-and-drop reordering with accessible keyboard controls (Move Up and Move Down buttons).
* **Deterministic Sorting**: Choose between Manual, Oldest First (Ascending), or Newest First (Descending) with secondary tie-breaking.
* **Frontend Filtering**: Client-side filtering by categories and years without page reloads, with accessible fallbacks.
* **Subtle Animations**: Optional scroll reveal animations that respect user `prefers-reduced-motion` settings.
* **Three Display Channels**: Full support for Gutenberg Block (`wpcalibrate/timeline`), Shortcode (`[wpcalibrate_timeline id="123"]`), and Elementor Widget.
* **Performance Optimized**: Zero sitewide frontend assets. Assets load only on pages where a timeline is rendered.
* **Coexistence**: Fully compatible with WooCommerce and other page builders without imposing dependencies.

== Installation ==

1. Upload the `wpcalibrate-timeline-builder` folder to the `/wp-content/plugins/` directory, or install the ZIP file via Plugins > Add New > Upload Plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WPCalibrate > Timeline Builder** to create your first timeline.
4. Add timeline items and customize your layout and preset.
5. Embed the timeline using the Gutenberg block, the shortcode `[wpcalibrate_timeline id="123"]`, or the Elementor widget.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =
No. WPCalibrate Timeline Builder works independently of WooCommerce, while coexisting safely if WooCommerce is active.

= Can I use custom labels instead of calendar dates? =
Yes. Timeline items support four distinct date modes: Exact Date, Month & Year, Year Only, and Custom Text labels such as "Phase One" or "Founded".

= Does the horizontal layout work on mobile devices? =
Yes. The horizontal layout features touch swipe support, CSS scroll snapping, and responsive fallbacks.

= How does the plugin handle data when uninstalled? =
By default, all your timelines and settings are preserved if the plugin is uninstalled. If you wish to delete all plugin data upon uninstall, enable the "Delete plugin data when uninstalled" setting under WPCalibrate > Timeline Settings.

== Screenshots ==

1. All Timelines list view showing item counts, shortcode copy, and management actions.
2. Timeline Editor featuring item builder with drag-and-drop and keyboard ordering controls.
3. Design and visual preset customization settings panel.
4. Vertical alternating cards timeline display on frontend.
5. Horizontal scrolling milestone track display on frontend.

== Changelog ==

= 1.0.0 =
* Initial production release.
* Added vertical (alternating/single-side) and horizontal timeline layouts.
* Added 5 visual presets: Clean, Cards, Minimal, Modern, and Compact.
* Added 4 date modes: Exact Date, Month & Year, Year Only, and Custom Text.
* Added drag-and-drop plus accessible keyboard reordering.
* Added Gutenberg block, Shortcode, and Elementor widget integrations.
* Added Category and Year frontend filters.
* Added live admin preview and duplication tools.

== Upgrade Notice ==

= 1.0.0 =
Initial production release of WPCalibrate Timeline Builder.
