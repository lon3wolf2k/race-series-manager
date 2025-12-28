# Race Series Manager – WordPress Plugin

Race Series Manager is a lightweight WordPress plugin for organizing and displaying endurance events such as running, cycling, and multi-stage races. It lets you create events, add multiple races, and easily embed registration forms, results, participant lists, and live timing via iframe or external links.

## Version & author

- **Version:** 1.0
- **Author:** lon3wolf (lon3wolf2k@gmail.com)

## Key features

- Create and manage events with multiple race distances and dates.
- Add rich race metadata (distance, elevation, categories, terrain, etc.).
- Automatic buttons and layout blocks on event and race pages.
- Countdown timers for upcoming races (widgets and event pages).
- Front-end results display with lightbox galleries.
- Shortcodes for showcasing events, races, banners, and results on any page.
- Widgets for race showcases and event banners with theme-aware styling.
- PDF race booklet generation (via bundled Dompdf).
- Custom admin dashboard, help tabs, and settings pages.
- REST API integration for external consumption of race data.
- Translation-ready with `.po`/`.mo` files in `/languages`.
- Theme-friendly frontend styles with selectable light/dark/green/autumn/summer themes.

## Requirements

- WordPress 6.0 or newer.
- PHP 7.4 or newer.

## Installation

1. Upload the `race-series-manager` folder to the `/wp-content/plugins/` directory or install via the WordPress plugin uploader.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **RS Manager** in the admin sidebar to configure settings and manage events/races.

## Usage

- **Events & Races:** Create events (`cmt_event`) and attach one or more races (`cmt_race`) with full metadata.
- **Results:** Add results (`cmt_result`) and display them via shortcodes or the built-in results layout.
- **Shortcodes & Widgets:** Use the provided shortcodes and widgets to place showcases, banners, and countdowns anywhere on your site.
- **PDF Booklet:** Generate printable race booklets from the admin area; bundled Dompdf is used if no system version is available.
- **Themes:** Choose a frontend theme (light, dark, green, autumn, summer) under **RS Manager > Settings** to match your site style.

## Support

For questions or issues, contact lon3wolf at **lon3wolf2k@gmail.com**. Bug reports and feature suggestions are welcome.
