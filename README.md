# Atomic

A flexible, open-source template for Joomla 4, 5, and 6.

**Demo:** https://atomic.kontentdesign.com

Atomic serves both novice Joomla users who need a straightforward website and developers who want a clean starting point for custom builds. The design philosophy is minimal custom styling, relying on Bootstrap 5 for layout and responsiveness. Performance is a top priority.

## Installation

The template installs through Joomla's standard extension installer as a package that includes the template and an optional sample data plugin.

1. Download the latest `pkg_atomic_VERSION.zip` from [Releases](https://github.com/Kontent/Atomic/releases)
2. In Joomla, go to **System > Install > Extensions**
3. Upload the package ZIP

The package installs both the template and the sample data plugin. To populate your site with example content, go to **System > Sample Data** and activate the Atomic sample data.

## Features

- **Bootstrap 5** — Joomla's built-in version, CDN, custom URL, or any of 24 Bootswatch themes
- **Light/Dark/Auto theme switcher** — Saved to localStorage, follows OS preference in auto mode
- **Cassiopeia compatibility** — Enable mapped positions for a seamless switch from Cassiopeia
- **Two-layer CSS architecture** — Core layout fixes (`atomic.css`) and optional decorative styles (`atomicstyles.css`) with glassmorphism, gradients, and typography
- **Google Fonts** — Separate header and body font selections with Google Fonts link or custom CSS
- **Font Awesome** — Version-aware options including Font Awesome 7 CDN
- **Bootstrap Icons** — Optional CDN loading
- **Responsive mobile menu** — Offcanvas slide-in panel supporting multiple menu modules
- **Configurable layouts** — One, two, or three-column header, body, and footer with adjustable grid ratios
- **Sticky header** — Optional fixed header on scroll
- **ScrollReveal** — Built-in scroll-triggered animation support
- **Social media meta tags** — OpenGraph, Twitter/X, and Schema.org image support
- **Google Analytics** — GA4 measurement ID integration
- **Custom code injection** — Four hook points: after `<head>`, before `</head>`, after `<body>`, before `</body>`
- **Custom CSS and JS** — Dedicated override files for user customizations
- **Body menu alias** — Page-specific CSS targeting via body class or ID
- **Favicon set** — Default icons for Android, Apple, Microsoft, and standard browsers
- **Component-only view** — Stripped-down rendering for modals, print views, and embeds
- **Automatic updates** — Integrates with Joomla's built-in update system, with optional beta channel
- **Web Asset Manager** — CSS and JS assets managed through Joomla's WAM with weighted load ordering
- **Lazy stylesheet loading** — Google Fonts, Font Awesome, and Bootstrap Icons load non-render-blocking via `media="print"` swap with `<noscript>` fallbacks
- **PreloadManager** — Automatic `preconnect` hints for CDN origins (Google Fonts, jsDelivr, cdnflare, unpkg)
- **Error pages** — Bootstrap-styled 403/404 pages with theme support, search form, module positions, and localized strings
- **Offline page** — Maintenance mode login with multi-factor authentication support

## Module Positions

| Position | Description |
|----------|-------------|
| `mobilemenu` | Offcanvas mobile menu (supports multiple modules) |
| `alert` | Top-of-page alert bar |
| `header` | Left/primary header area |
| `header-center` | Center header column |
| `header-right` | Right header area |
| `topmenu` | Horizontal navigation below the header |
| `hero` | Full-width hero area |
| `leftbody` | Left sidebar column |
| `breadcrumbs` | Breadcrumb navigation |
| `main-top` | Above main content |
| `abovebody` | Row above the component |
| `belowbody` | Row below the component |
| `main-bottom` | Below main content |
| `rightbody` | Right sidebar column |
| `footer` | Footer left column |
| `footer-center` | Footer center column |
| `footer-right` | Footer right column |
| `error-403` | Custom 403 error page |
| `error-404` | Custom 404 error page |
| `debug` | Joomla debug position |

Columns collapse and rows disappear automatically when positions have no content assigned.

## Module Chrome

| Chrome | Description |
|--------|-------------|
| `default` | Card-style wrapper with header and body |
| `card` | Bootstrap card component |
| `column` | Minimal wrapper for column layouts |
| `row` | Minimal wrapper for row layouts |
| `none` | Raw module content, no wrapper |

## Building from Source

Run the build script from the repository root to produce installable ZIP packages:

```bash
bash build_package.sh
```

This creates three files in the `ZIP/` folder: the full package, standalone template, and standalone sample data plugin.

## Documentation

See the [Wiki](https://github.com/Kontent/Atomic/wiki) for detailed documentation on all settings and features.

## Requirements

- Joomla 4.x, 5.x, or 6.x
- PHP 7.4+ (Joomla 4), PHP 8.1+ (Joomla 5/6)

## License

GNU General Public License v3.0 — see [LICENSE](LICENSE) for details.

## Author

[Ron Severdia](https://www.severdia.com)
