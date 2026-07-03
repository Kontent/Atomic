# CLAUDE.md — Atomic Template for Joomla

> **Purpose of this file.** An exhaustive, navigable map of the Atomic repository so future AI requests can *locate* and *fix* things fast. It documents the template, its code structure, the Joomla APIs it uses, every parameter and module position, the CSS/JS architecture, the sample-data plugin, the package/build/update machinery, and the separate `com_joomlareset` utility component.
>
> **Scope.** Everything under `Atomic Template/Atomic/` (the repo root; note the workspace folder name has a space). Paths below are relative to that root.

---

## 1. What this is

**Atomic** is a barebones, open-source **Joomla site template** (supports **Joomla 4, 5, and 6**) by **Ron Severdia / Kontent Design**. Design philosophy: *one template, many designs* — users customize via settings and CSS without editing core files. It leans on **Bootstrap 5** for layout, keeps custom styling minimal, and prioritizes performance (lazy/preloaded stylesheets, Web Asset Manager).

The repository is a **multi-extension bundle**, not just a template:

- **`tpl_atomic/`** — the site template itself (the bulk of the code).
- **`plg_sampledata_atomic/`** — a Joomla *sample-data* plugin that populates a demo site.
- **`pkg_atomic/`** — a *package* manifest bundling the template + plugin into one installable ZIP.
- **`utilities/com_joomlareset/`** — a **separate, destructive dev/maintenance component** ("Joomla Reset") that wipes a site back to a fresh install.
- **`docs/`, `source/`, `build_package.sh`** — update-server feeds, design masters/preview art, and the build script.

- **Languages:** PHP (Joomla extension conventions), CSS (two-layer system), vanilla JS (theme switcher), XML manifests, SQL (reset component).
- **License:** GPL (v3 for the template; v2+ for `com_joomlareset`). Demo: https://atomic.kontentdesign.com

---

## 2. At-a-glance facts

| Fact | Value |
|---|---|
| Template + package version | **5.3.0** |
| Sample-data plugin version | **0.8.0** |
| `com_joomlareset` version | **1.1.0** |
| Joomla support | **4.x, 5.x, 6.x** (update `targetplatform` regex `(4|5|6)\.`) |
| PHP | 7.4+ (J4), 8.1+ (J5/J6) |
| CSS framework | Bootstrap 5 (Joomla core / CDN / custom / 9 Bootswatch themes) |
| Template install path | `templates/atomic/` (per-site: `css/`, `js/`) |
| Shared media path | `media/templates/site/atomic/` (shared across instances) |
| Update server (stable) | `https://kontent.github.io/Atomic/update.xml` |
| Update server (beta) | `https://kontent.github.io/Atomic/update-beta.xml` (toggled by `betachannel` param) |
| Module positions | **30** (21 Atomic + 9 Cassiopeia-compat) |
| Template params | ~100 across 10 fieldsets (see §5) |
| Asset loading | Joomla **Web Asset Manager** (`joomla.asset.json`) + lazy `media="print"` swap + preconnect |
| Key params / defaults | `bootstrapsource=2` (CDN), `bootscolumns=2-8-2`, `headerfont/bodyfont=0` (system), `fontawesome=0` (none) |

---

## 3. Repository structure

```
Atomic Template/Atomic/                 ← repo root
├── README.md                    project readme (feature list; its position table lists 19 — STALE, see §6)
├── LICENSE                      GPL v3
├── build_package.sh             build script → 3 ZIPs in ZIP/
├── CLAUDE.md                    this file
│
├── tpl_atomic/                  THE TEMPLATE (installs to templates/atomic/)
│   ├── index.php                (~973) main render pipeline — params, WAM, <head>, layout, positions
│   ├── component.php            (~462) component-only view (tmpl=component); mirrors index.php assets
│   ├── error.php                (~236) 403/404 error pages
│   ├── offline.php              (~99)  maintenance/offline login page
│   ├── helper.php               (~108) Google-font helpers (getGoogleFontFamily/Link, isGoogleFont)
│   ├── install.php              (~302) install/update script (sets Bootstrap source per Joomla version)
│   ├── templateDetails.xml      (~450) MANIFEST — all params, 30 positions, update server, files
│   ├── joomla.asset.json        Web Asset Manager registry (atomicstyles, themeswitcher, atomicjs, js)
│   ├── html/                    Joomla template overrides:
│   │   ├── fields/              custom admin field types (betachannel, bootstrapsource, fontawesome)
│   │   ├── layouts/chromes/     module wrappers (default, card, column, row, none, mobilemenu, mobilemenupanel)
│   │   ├── layouts/header/      styleswitcher.php (light/dark/auto dropdown)
│   │   ├── mod_custom/          (default, contentonly, modulesuffix)
│   │   ├── mod_menu/            menu overrides (default, horizontal*, horizontaltabs*, vertical*)
│   │   ├── mod_tags_popular/    tag module override (alias-based CSS classes)
│   │   └── modules.php          module-rendering helper
│   ├── css/template.css         USER-editable CSS (loaded last when enabled) — never edit atomic*.css for a site
│   ├── js/template.js           USER-editable JS
│   ├── media/                   shared assets (→ media/templates/site/atomic/):
│   │   ├── css/atomic(.min).css        CORE layout + Joomla/Bootstrap fixes
│   │   ├── css/atomicstyles(.min).css  DECORATIVE layer (design tokens, glassmorphism, gradients)
│   │   ├── js/atomic.js  js/themeswitcher.min.js
│   │   ├── favicons/site.webmanifest + favicon.svg   fonts/  images/(logo, previews)
│   ├── language/en-GB/          tpl_atomic.ini (front) + tpl_atomic.sys.ini (admin) — prefix TPL_ATOMIC_
│   └── utilities/               htaccess.txt, robots.txt (bundled site utilities)
│
├── plg_sampledata_atomic/       SAMPLE-DATA PLUGIN (group: sampledata)
│   ├── atomic.xml  script.php  services/provider.php
│   ├── src/Extension/Atomic.php  the onSampleData* steps
│   └── language/en-GB/*.ini
│
├── pkg_atomic/pkg_atomic.xml    PACKAGE manifest (bundles template + plugin)
│
├── docs/
│   ├── atomic-positions.html    visual MODULE-POSITION MAP (authoritative position reference)
│   ├── update.xml               stable update-server feed (v5.3.0)
│   └── update-beta.xml          beta update-server feed (v5.3.0)
│
├── source/                      DESIGN MASTERS & SOCIAL PREVIEW ART (not shipped)
│   ├── atomic_logo.ai/.psd/.png  atomic_module_positions.ai/.png  "Atomic Module Positions.zip"
│   └── preview/preview_{google,opengraph,twitter}.png   social-card images
│
└── utilities/                  DEV UTILITY — NOT part of the template
    ├── README.md                (duplicate of the component readme)
    └── com_joomlareset/         "Joomla Reset" component (v1.1.0) — DESTRUCTIVE site wipe
        ├── joomlareset.xml  script.php  services/provider.php  README.md
        ├── src/Controller/{DisplayController,ResetController}.php
        ├── src/Dispatcher/Dispatcher.php  (Super-Admin-only gate)
        ├── src/Model/ResetModel.php  (6-phase reset logic)
        ├── src/View/Reset/HtmlView.php  tmpl/reset/default.php
        ├── sql/j5/{base,extensions,supports}.sql
        └── sql/j6/{base,extensions,supports}.sql   (version-selected reinstall SQL)
```

---

## 4. Architecture & rendering model

- **Procedural template + Joomla APIs.** `index.php`/`component.php`/`error.php`/`offline.php` are procedural PHP that read `$this->params` (a Joomla `Registry`) and emit HTML, using Joomla services: **Web Asset Manager** (`$this->getWebAssetManager()`), `Factory`, `HTMLHelper`, `Text`, `Uri`, `AuthenticationHelper`, and `<jdoc:include>` for metas/styles/scripts/modules/message/component.
- **Web Asset Manager (WAM).** Assets are declared in `joomla.asset.json` (`template.atomic.atomicstyles`, `.themeswitcher`, `.atomicjs`, `.js`) with weights, and enabled conditionally via `useStyle()`/`useScript()`. Third-party CSS (Bootstrap/Fonts/FA/BS-Icons) is registered with a **lazy `media="print" onload="this.media='all'"`** swap (+ `<noscript>` fallback) and **preconnect** hints for CDN origins.
- **Params-driven everything.** ~100 params in `templateDetails.xml` control sources (Bootstrap, jQuery, Font Awesome, fonts), layout (column ratios), theming (`data-bs-theme`, custom `data-theme`), metadata, and four custom-code injection hooks. Many are **integer-mapped** (see §5).
- **Two-layer CSS.** `atomic.css` = opinion-free core layout / Joomla-Bootstrap fixes (should always load). `atomicstyles.css` = optional decorative layer built on CSS custom properties (design tokens). User overrides live in `css/template.css` (loaded last). See the Assets section.
- **Theme switching.** An inline head IIFE resolves `data-bs-theme` from `localStorage['theme']` → param default → OS `prefers-color-scheme` (for `auto`) *before paint* to avoid flash; `themeswitcher.min.js` + the `styleswitcher.php` header layout provide the UI.
- **The Joomla extension trio** (plugin, package, component) follow modern Joomla conventions: **PSR-4 `src/` namespaces**, **DI `services/provider.php`**, MVC (`com_joomlareset`), and XML manifests. The template itself is classic procedural (templates aren't namespaced in Joomla).
- **Media vs per-site split.** `media/templates/site/atomic/` is shared/overwritten on update; `templates/atomic/css|js/` (`template.css`, `template.js`) is per-site and preserved — that's where site customizations belong.

---

## 5. Template parameters (`templateDetails.xml`)

~100 params in 10 fieldsets. Full field-by-field detail is in the Template Configuration section below; this is the index + the integer maps that trip people up.

| Fieldset | Key params |
|---|---|
| **JOOMLA_SETTINGS** | `killgenerator`, `casspositions` (Cassiopeia compat), `feediting`, `betachannel` (custom field) |
| **CSS_SETTINGS** (+THEMES) | `bootstrapsource`, `bootstrapcdn`, `bsfixjoomla` (atomic.css), `atomicstyles`, `customcssfile`, `bodymenu`, `bsthemes` (theme switcher), `bstheme`/`bsthemecustom`, `theme`, `usergroupdata` |
| **JAVASCRIPT_SETTINGS** | `jqlibrary`, `jquerycdn`, `atomicjs`, `customjs` |
| **FONT_SETTINGS** | `headerfont`/`bodyfont`, `systemFontHeader`/`Body`, `headergooglefont`/`bodygooglefont`, `headerfontname`/`bodyfontname`, `typescale` |
| **ICON_SETTINGS** | `fontawesome`, `fontawesomecdn`, `loadbsicons`, `loadfavicons`, `maskiconcolor` |
| **LAYOUT** (page/header/body/footer) | `fluidcontainer`, `headercolumns`, `logo`, `sitetitle`, `sitedescription`, `stickyhead`, `headerbackground`, `bootscolumns` (default `2-8-2`), `footercolumns`, `copyright`/`copyrighttxt` |
| **FEATURE_SETTINGS** | `gacode` (GA4), `scrollreveal`, `errorsearch` |
| **METADATA_SETTINGS** | `socialtitle`/`socialdescription`/`socialurl`, `socialthumb{google,facebook,twitter}` |
| **CUSTOM_CODE** | `codeafterhead`, `codebeforehead`, `codeafterbody`, `codebeforebody` |

**Integer-mapped values (the important ones):**

- **`bootstrapsource`** (default 2): `0`=None · `1`=Joomla J4 core · `2`=CDN (jsDelivr) · `3`=Joomla J5 core · `4`=Joomla J6 core · `5`=Custom (`bootstrapcdn`) · `6–14`=Bootswatch themes.
- **`fontawesome`** (default 0): `0`=None · `1`=Joomla core FA CSS · `2`=FA7 CSS CDN · `3`=FA7 JS CDN · `4–5`=Custom (`fontawesomecdn`) · `6`=J5/J6 system.
- **`headerfont`/`bodyfont`** (default 0): `0`=Bootstrap default · `1`=Custom Google Font (`*fontname` + `*googlefont` link) · `2`=None · `3–12`=built-in Google Fonts (Inter, Lato, Montserrat, Open Sans, Roboto, Fraunces, Libre Baskerville, Merriweather, Noto Serif, Unna) · `13`=System font (`systemFont*` dropdown).
- **`jqlibrary`**: off / Joomla / CDN / slim / custom. **`bodymenu`**: 0–6 (append/replace body class/ID/both). **`loadfavicons`**: 0=media default · 1=template · 2=site root. **`copyright`**: preset / custom text modes.
- **`bstheme`** → `data-bs-theme`: `''`/`light`/`dark`/`auto`/`custom` (`bsthemecustom`).

---

## 6. Module positions (30) & Cassiopeia compatibility

The authoritative visual map is **`docs/atomic-positions.html`** (dark-mode-aware, color-legends shared vs Atomic-only vs Cassiopeia-compat).

**Atomic's own 21 positions:**
`alert` · `mobilemenu` (offcanvas) · `header` / `header-center` / `header-right` · `menu` · `topmenu` · `hero` · `leftbody` · `breadcrumbs` · `abovebody` · `main-top` · *[component]* · `main-bottom` · `belowbody` · `rightbody` · `footer` / `footer-center` / `footer-right` · `error-403` · `error-404` · `debug`.

**Cassiopeia-compatibility positions (9)** — declared so a site migrating from Joomla's default *Cassiopeia* template keeps working; enabled/mapped by the **`casspositions`** param:
`banner` · `top-a` · `top-b` · `below-top` · `bottom-a` · `bottom-b` · `sidebar-left` · `sidebar-right` · `topbar`.

> ⚠️ The old CLAUDE.md and `README.md` under-count positions ("21"/"19"). `templateDetails.xml` declares **30**. When editing positions, update the manifest *and* `docs/atomic-positions.html`.

Body columns (`bootscolumns`, e.g. `2-8-2`) control sidebar visibility: `leftbody` → left sidebar, `rightbody` → right sidebar; empty columns collapse automatically.

---

## 7. Versioning & updates

| Extension | Version | Manifest |
|---|---|---|
| Template (`tpl_atomic`) | 5.3.0 | `tpl_atomic/templateDetails.xml` |
| Package (`pkg_atomic`) | 5.3.0 | `pkg_atomic/pkg_atomic.xml` |
| Sample data plugin | 0.8.0 | `plg_sampledata_atomic/atomic.xml` |
| Joomla Reset component | 1.1.0 | `utilities/com_joomlareset/joomlareset.xml` |

**Update channels.** The template manifest registers one update server (`https://kontent.github.io/Atomic/update.xml`). Two feeds exist: **`docs/update.xml`** (stable) and **`docs/update-beta.xml`** (beta). The **`betachannel`** param (custom field `html/fields/betachannel.php`) switches which feed Joomla polls, so users can opt into pre-releases. Both feeds are currently 5.3.0 with `targetplatform (4|5|6)\.`. *(The infourl/GitHub repo is `github.com/Kontent/Atomic`; the update host is `kontent.github.io` — mind the casing.)*

---

## 8. Design & marketing source (`source/`)

Not shipped in any ZIP; kept for maintainers:
- `atomic_logo.ai/.psd/.png` — logo masters. `atomic_module_positions.ai/.png` + `Atomic Module Positions.zip` — the position-map artwork.
- `source/preview/preview_{google,opengraph,twitter}.png` — the **social-card preview images** that back the `socialthumb*` metadata feature (see the Core Rendering section's social-meta logic).

---

## 9. Subsystem reference

Detailed, file-level documentation of each area:

1. [Template Core Rendering](#template-core-rendering-indexphp-componentphp-errorphp-offlinephp)
2. [Template Configuration](#template-configuration-templatedetailsxml-installphp-helperphp-joomlaassetjson)
3. [Template Overrides & Custom Fields](#template-overrides--custom-fields-html--chromes-menus-fields-module-layouts)
4. [CSS/JS Assets & Theme Switcher](#cssjs-assets--theme-switcher-media-css-js)
5. [Sample Data Plugin](#sample-data-plugin-plg_sampledata_atomic)
6. [Packaging, Build, Updates & Docs](#packaging-build-updates--docs-pkg_atomic-build_packagesh-docs)
7. [Joomla Reset Utility Component](#joomla-reset-utility-component-com_joomlareset)

---

## Template Core Rendering (index.php, component.php, error.php, offline.php)

### Purpose

Atomic's template core files generate the complete HTML document structure for normal pages, component-only views, error pages, and maintenance mode. They read ~100 template parameters via Joomla's Registry API, orchestrate the Web Asset Manager for CSS/JS/font loading, render dynamic header/body/footer layouts with configurable column ratios, emit 21 module positions, apply Bootstrap theming, and inject custom code at four hooks.

### Key Files

- `tpl_atomic/index.php` (~973 lines) — Main template rendering engine: reads params, registers WAM assets, builds HTML document, handles all module positions, renders header/body/footer with column logic, processes theme switching, GA4, ScrollReveal.
- `tpl_atomic/component.php` (~462 lines) — Component-only view mirror of index.php: duplicates asset/param logic but without site chrome (header/footer/navigation/sidebars), used when `tmpl=component` query param is set.
- `tpl_atomic/error.php` (~236 lines) — Error page template (403/404): minimal HTML with error-specific module positions (`error-403`, `error-404`), optional search form, theme support.
- `tpl_atomic/offline.php` (~99 lines) — Maintenance/offline mode login page: Bootstrap-based login form, supports social auth buttons via `AuthenticationHelper::getLoginButtons()`.
- `tpl_atomic/helper.php` (~109 lines) — Google Font utilities: `getGoogleFontFamily()` (maps font param values 0–13 to CSS family strings), `getGoogleFontLink()` (returns `<link>` tag for 11 built-in Google Fonts), `isGoogleFont()` (boolean checker for font param).
- `tpl_atomic/joomla.asset.json` — Web Asset Manager registry: declares 4 assets (`template.atomic.atomicstyles`, `template.atomic.themeswitcher`, `template.atomic.atomicjs`, `template.atomic.js`) with URIs and weights.

### Key Logic & Structures

#### Parameter Reading & Validation

All three main files use `$this->params` (a Joomla `Registry` object injected by the template engine) to read settings via `$this->params->get('PARAM_NAME', DEFAULT)`. Core params include:

- **Bootstrap source** (param: `bootstrapsource`, integer 0–14): Controls Bootstrap CSS source. Values: 0=None, 1=Joomla J4 core, 2=CDN jsDelivr, 3=Joomla J5 core, 4=Joomla J6 core, 5=Custom (via `bootstrapcdn` textarea), 6–14=Bootswatch themes (Cosmo, Flatly, Minty, Spacelab, Yeti, Cyborg, Darkly, Slate, Superhero).
- **Font params** (e.g., `headerfont`, `bodyfont`, integers 0–13): Map to font sources. Values: 0=Bootstrap default, 1=Custom Google Font (uses `headerfontname`/`bodyfontname` + Google Fonts link), 2=None, 3–12=Built-in Google Fonts (Inter, Lato, Montserrat, Open Sans, Roboto, Fraunces, Libre Baskerville, Merriweather, Noto Serif, Unna), 13=System font (uses `systemFontHeader`/`systemFontBody` dropdowns).
- **Font Awesome** (param: `fontawesome`, integer 0–6): 0=None, 1=Joomla core FontAwesome CSS, 2=FA7 CSS CDN (cdnjs), 3=FA7 JS CDN, 4–5=Custom (via `fontawesomecdn` textarea), 6=J5/J6 system.
- **Layout columns** (params: `bootscolumns`, `headercolumns`, `footercolumns`, string like "4-4-4" or "2-8-2"): Dash-separated Bootstrap column widths (sum ≤ 12). E.g., "2-8-2" = 3 cols (left, main, right), "12" = single col.
- **Theme & editing** (params: `bstheme`, `theme`, `feediting`, `usergroupdata`): `bstheme` = Bootstrap data-bs-theme (none/'light'/'dark'/'auto'/'custom'); `theme` = custom data-theme attribute; `feediting` = hide Joomla edit buttons; `usergroupdata` = emit data-user attribute with user's highest group name (sanitized to kebab-case).

#### Web Asset Manager (WAM) Registration

Both index.php and component.php call `$this->getWebAssetManager()` to register CSS/JS assets with lazy-loading hints:

1. **Google Fonts (lazy)**: If header/body font is Google Font (values 3–12), calls `getGoogleFontLink($font)` to extract `href`, then registers with WAM using `media="print" onload="this.media='all'"` trick to defer loading until page load.
2. **FontAwesome CSS (lazy)**: Cases 1, 2, 6 register via WAM; cases 3 (JS) and 4–5 (custom) load manually via `<script>` or textarea.
3. **Bootstrap Icons (lazy)**: If `loadbsicons == 1`, registers jsDelivr CDN link via WAM.
4. **Atomic styles**: `$wa->useStyle('template.atomic.atomicstyles')` if `atomicstyles == 1`.
5. **Theme switcher JS**: `$wa->useScript('template.atomic.themeswitcher')` if `bsthemes == 1`.
6. **Atomic & custom JS**: `$wa->useScript()` for `template.atomic.atomicjs` and `template.atomic.js` if enabled.

Assets declared in `joomla.asset.json` are weight-ordered: lower weight = earlier load. WAM outputs via `<jdoc:include type="styles" />` and `<jdoc:include type="scripts" />`.

#### Document Head Assembly (index.php lines 250–518)

1. **Inline theme resolution script** (if `bstheme` != ''): IIFE that checks localStorage for 'theme' key, falls back to param value, handles 'auto' via `matchMedia('prefers-color-scheme: dark')`, sets `data-bs-theme` attribute before CSS paints to prevent flash.
2. **Custom code injection**: `codeafterhead` textarea echoed after `<head>` opens.
3. **Joomla metas**: `<jdoc:include type="metas" />` outputs Joomla's meta tags.
4. **Generator tag removal**: If `killgenerator == 1`, calls `$this->setMetaData('generator', '')`.
5. **Favicons** (lines 279–293): Three modes based on `loadfavicons`: 0=default (media/templates/site/atomic/favicons/), 1=template (templates/atomic/favicons/), 2=site root. Emits `.svg`, `.png`, `.ico`, apple-touch-icon, mask-icon, manifest links.
6. **Social meta tags** (lines 299–376): If `socialtitle`/`socialdescription`/`socialurl` enabled, emits OpenGraph (`og:title`, `og:image`, `og:url`, etc.) and Twitter Card meta tags using media picker URLs (`socialthumbgoogle`, `socialthumbfacebook`, `socialthumbtwitter`). Helper function `cleanImageURL()` strips query params and prefixes site base if needed.
7. **Bootstrap CSS**: Conditional load based on `bootstrapsource` (cases 0–14) with integrity hashes for CDN sources.
8. **Google Fonts (manual)**: If `headerfont == 1` or `bodyfont == 1`, echoes `$headergooglefont` / `$bodygooglefont` (custom textarea links).
9. **Inline `<style>` for CSS custom properties** (lines 414–443): Builds `:root { ... }` rule with `--atomic-header-background-color`, `--atomic-header-font`, `--atomic-body-font` if non-default. Also hides Joomla edit tooltips if `feediting == 1`.
10. **FontAwesome JS or custom** (lines 444–449): Case 3 loads FA7 JS from cdnjs; cases 4–5 echo textarea.
11. **WAM styles output** (line 453).
12. **Atomic.min.css & template.css** (lines 457–462): Manual `<link>` tags (not via WAM) to ensure they load last, after extension styles.
13. **No-script fallbacks** (lines 464–474): Repeats FontAwesome CSS and BS Icons for JS-disabled browsers.
14. **jQuery**: Conditional load (4 modes) based on `jqlibrary` param.
15. **Theme switcher default** (line 489): If `bsthemes == 1`, emits `<script>var defaultTheme = '...';</script>` for themeswitcher.min.js to read.
16. **WAM scripts output** (line 494).
17. **ScrollReveal** (line 498): If `scrollreveal == 1`, loads unpkg CDN script.
18. **Custom code before head close** (lines 501–505): `codebeforehead` textarea.
19. **GA4** (lines 507–516): If `gacode` set, loads gtag.js from Google Tag Manager.

#### Body & Layout Rendering (index.php lines 520–894)

1. **Body class construction** (lines 520–543): Builds class list from menu alias, component/view/itemid, pageclass suffix (from menu params). Menu alias handling via `bodymenu` param (0–6 modes: append class, append ID, append both, replace class, replace ID, replace both; mode 1–6 overwrites defaults).
2. **Data attributes** (lines 528–542): Emits `data-user="user-[GROUP_NAME]"` (if `usergroupdata == 1`), `data-theme`, `data-editing`, `data-typescale`.
3. **Alert bar** (lines 550–558): If `alert` position has modules, wraps in `.alertbar` div with container/row.
4. **Header section** (lines 560–741): Rendered only if `$hasHeaderContent` true (checks for modules, logo, title, description, theme switcher). Sticky class applied if `stickyhead == 1`. **Column logic**: Parses `headercolumns` (e.g., "4-4-4" → 3 cols). Three-column: col 1 has mobile burger + logo/title, col 2 has header-center mods, col 3 has header-right + theme switcher. Two-column: col 1 = logo/title/header mods, col 2 = header-right/theme switcher. One-column: all combined. Mobile burger (`d-sm-none`) toggles offcanvas with `data-bs-target="#mobilemenuOffcanvas"`. **`topmenu` position** rendered in separate row if modules present.
5. **Mobile menu offcanvas** (lines 743–755): Rendered outside header to avoid z-index stacking issues. Single offcanvas wrapper emits all mobilemenu modules with `style="mobilemenu"` chrome (custom chrome in `html/layouts/chromes/mobilemenu.php`).
6. **Body column logic** (lines 780–836): Complex logic based on `bootscolumns` param. **Three-column parse** (e.g., "2-8-2" splits to array [2, 8, 2]). If `$showLeft` and `$showRight` (based on presence of leftbody/rightbody modules or Cassiopeia positions): applies full 3-col layout with `d-none d-lg-block` for sidebars (responsive hide on mobile). If only left or only right, adjusts main width. If no sidebars, main is col-12. Legacy integer mapping (lines 156–159) converts old J5.0 values (0–4) to new string format for backward compat.
7. **Main content row** (lines 839–879): Three divs (left sidebar, main, right sidebar, each conditionally rendered). Main div includes breadcrumbs, abovebody modules, message queue, component output, belowbody modules. Cassiopeia positions (sidebar-left, sidebar-right, main-top, main-bottom, banner, top-a, top-b, bottom-a, bottom-b) rendered if `casspositions == 1`.
8. **Footer section** (lines 896–952): Similar column logic as header (1–3 cols). Copyright text via `copyright` param (0=none, 1=year+site name, 2=year+custom txt, 3=custom only). Year auto-generated via `date('Y')`.

#### Hero & Module Positions

Total 21 module positions defined in templateDetails.xml:

- **Header zone**: `header`, `header-center`, `header-right` (rendered in 3-column header row).
- **Navigation**: `topmenu`, `mobilemenu` (offcanvas).
- **Content zone**: `hero` (full-width hero section above body), `alert` (sticky top bar), `breadcrumbs`, `abovebody`, `main-top`, `main-bottom`, `belowbody`.
- **Sidebars**: `leftbody`, `rightbody` (or Cassiopeia `sidebar-left`, `sidebar-right`).
- **Footer**: `footer`, `footer-center`, `footer-right`.
- **Special**: `debug` (Joomla debug output), `error-403`, `error-404` (error page positions).
- **Cassiopeia compat** (if `casspositions == 1`): `banner`, `top-a`, `top-b`, `bottom-a`, `bottom-b`, `topbar`, `below-top`.

#### Preload Manager

`$this->getPreloadManager()` emits `preconnect` hints (via `<link rel="preconnect">`) for CDN domains if needed:
- Google Fonts (if header/body font is Google Font).
- jsDelivr CDN (if Bootstrap source is CDN or Bootswatch 6–14 or BS Icons enabled).
- cdnjs.cloudflare.com (if FontAwesome is CDN cases 2–3).
- unpkg (if ScrollReveal enabled).

### Component.php Duplication & Why

`component.php` (~462 lines) mirrors index.php's logic **but omits**:
- Header section (lines 564–741 in index.php).
- Mobile menu offcanvas (lines 743–755).
- Body column layout rendering (lines 780–879).
- Footer section (lines 896–952).
- Custom code hooks (it still includes `codeafterhead`, `codebeforehead`, `codeafterbody`, `codebeforebody`).
- Most module positions (only outputs Joomla's message queue and component via `<jdoc:include type="component" />`).

**Why duplication?** Component view is meant for AJAX/modal/embedded contexts where the page chrome (header, sidebars, footer) is not needed—only CSS setup and the component output. Duplication avoids PHP includes/conditionals that might not apply in component-only context, making debugging clearer. Both files read identical params (Bootstrap source, fonts, theme, GA4, etc.) to ensure consistent asset loading and styling.

### Error.php Structure

Error pages (403/404) are minimal but complete:

1. **Query error code**: `$code = $this->error->getCode()`.
2. **Font/theme setup**: Same font/theme logic as index.php (lines 40–57).
3. **Bootstrap CSS**: Loads via param-based conditional (lines 254–282).
4. **Inline styles**: Error-specific classes (`.error-page`, `.error-code`, `.error-title`, `.error-desc`, `.error-search`, `.error-logo`, `.error-debug`) for full-height centered layout.
5. **Error message rendering** (lines 202–211): Emits error code (large), title (localized via `TEXT::_('TPL_ATOMIC_ERROR_403_TITLE')` etc.), description. If `errorsearch == 1` (default true), renders search form targeting com_finder.
6. **Error-specific module position**: `error-403` or `error-404` position allows modules to render custom content (buttons, etc.).
7. **Nav buttons**: "Home" and "Back" buttons (back uses `history.back()` if history exists, else home).
8. **Debug backtrace** (if `$this->debug` true): Emits via `$this->renderBacktrace()`.

### Offline.php Structure

Offline/maintenance mode page (~99 lines):

1. **Minimal Bootstrap** (~5.3.8 CDN only, no template params respected).
2. **Site name/logo from global config**: `$app->get('sitename')`, `$app->get('logo')`.
3. **Offline message** (param `display_offline_message`, modes 0=none, 1=custom message, 2=Joomla default).
4. **Login form** (custom HTML form, not module-based):
   - Username/password inputs (class `form-control`).
   - Extra auth buttons via `AuthenticationHelper::getLoginButtons('form-login')` (e.g., social login plugins).
   - Submit button (`btn btn-primary`).
   - Post to `com_users.user.login` task.
5. **No template params** (offline page bypasses template settings to avoid database queries during maintenance).

### Control/Data Flow

#### Normal Page Render (index.php)

```
Request → Joomla routing → index.php loaded
  ↓
Params read from #__extensions table (template settings)
  ↓
WAM registration & preconnect hints
  ↓
<head> assembly (fonts, Bootstrap, GA4, custom code hooks)
  ↓
<body> construction (theme script, alert bar, header, mobile menu offcanvas)
  ↓
Main content area: hero → body columns (left sidebar | main content | right sidebar)
  ↓
Footer section
  ↓
Bootstrap JS & custom JS scripts
  ↓
HTML sent to browser
```

#### Component View (component.php)

Same flow but:
- No header/footer render.
- Only `<jdoc:include type="component" />` for content.
- Used when request has `tmpl=component` (e.g., modal dialogs, AJAX templates).

#### Error Page (error.php)

```
Request → 403/404 error triggered
  ↓
error.php loaded by Joomla error handler
  ↓
Error code/title/message retrieved
  ↓
Minimal HTML: error display + search form + buttons
  ↓
No modules, no sidebars, no WAM (manual Bootstrap CSS only)
```

### Parameters & Config

**Comprehensive param list** (read from `templateDetails.xml` config section):

| Param | Type | Default | Integer Values / Description |
|-------|------|---------|-----|
| `bootstrapsource` | int | 2 | 0=None, 1=J4, 2=CDN, 3=J5, 4=J6, 5=Custom, 6–14=Bootswatch (Cosmo, Flatly, Minty, Spacelab, Yeti, Cyborg, Darkly, Slate, Superhero) |
| `bootstrapcdn` | textarea | — | Custom Bootstrap link (if bootstrapsource=5) |
| `bsfixjoomla` | int | 1 | Load atomic.min.css (Joomla/Bootstrap fixes) |
| `atomicstyles` | int | 0 | Load atomicstyles.min.css (decorative) |
| `customcssfile` | int | 0 | Load templates/atomic/css/template.css (user edits) |
| `headerfont` | int | 0 | 0=Bootstrap, 1=Custom Google, 2=None, 3–12=Built-in Google, 13=System |
| `bodyfont` | int | 0 | Same as headerfont |
| `headerfontname` | string | — | Custom font family string (if headerfont=1) |
| `bodyfontname` | string | — | Custom font family string (if bodyfont=1) |
| `headergooglefont` | textarea | — | Custom Google Fonts link (if headerfont=1) |
| `bodygooglefont` | textarea | — | Custom Google Fonts link (if bodyfont=1) |
| `systemFontHeader` | string | — | System font family (if headerfont=13) |
| `systemFontBody` | string | — | System font family (if bodyfont=13) |
| `typescale` | int | 0 | 0=Bootstrap, 1=Major-third, 2=Minor-third, 3=Major-second, 4=Minor-second; sets data-typescale attr |
| `fontawesome` | int | 0 | 0=None, 1=J4 system, 2=FA7 CSS CDN, 3=FA7 JS CDN, 4–5=Custom, 6=J5/J6 system |
| `fontawesomecdn` | textarea | — | Custom FA link (if fontawesome=4 or 5) |
| `loadbsicons` | int | 0 | Load Bootstrap Icons CDN |
| `loadfavicons` | int | 0 | 0=Default (media/), 1=Template folder, 2=Site root |
| `maskiconcolor` | color | #000000 | SVG mask-icon color |
| `fluidcontainer` | int | 0 | 0=Fixed container, 1=Fluid |
| `headercolumns` | string | 12 | Header layout: "12", "6-6", "4-8", "2-10", "4-4-4" |
| `bootscolumns` | string | 2-8-2 | Body layout: "12", "2-10", "4-8", "8-4", "10-2", "2-6-4", "2-7-3", "2-8-2", "3-6-3", "4-4-4" |
| `footercolumns` | string | 12 | Footer layout: "12", "6-6", "4-4-4" |
| `logo` | media | — | Logo image URL |
| `sitetitle` | string | — | Site title in header |
| `sitedescription` | string | — | Site tagline/description |
| `stickyheader` | int | 0 | 1=Sticky header (class="sticky") |
| `headerbackground` | color | rgba(0,0,0,0) | Header background RGBA; sets CSS custom property |
| `bodymenu` | int | 0 | 0=Default, 1=Append class, 2=Append ID, 3=Append both, 4=Replace class, 5=Replace ID, 6=Replace both |
| `bsthemes` | int | 0 | Enable theme switcher (loads themeswitcher.min.js) |
| `bstheme` | string | — | Default theme: "", "light", "dark", "auto", "custom" |
| `bsthemecustom` | string | — | Custom theme name (if bstheme=custom) |
| `theme` | string | — | Custom data-theme attribute |
| `usergroupdata` | int | 0 | Emit data-user attr with user group name |
| `feediting` | int | 0 | Hide Joomla edit buttons (data-editing="no") |
| `jqlibrary` | int | 0 | jQuery source: 0=Joomla (J4 vendor), 1=CDN 4.0, 2=Slim CDN, 3=Custom, 4=None |
| `jquerycdn` | textarea | — | Custom jQuery link (if jqlibrary=3) |
| `atomicjs` | int | 0 | Load template.atomic.atomicjs script |
| `customjs` | int | 0 | Load templates/atomic/js/template.js (user edits) |
| `gacode` | string | — | GA4 measurement ID (e.g., G-XXXXX); loads gtag.js |
| `scrollreveal` | string | — | Load ScrollReveal unpkg library |
| `killgenerator` | int | 0 | Remove Joomla generator meta tag |
| `casspositions` | int | 0 | Enable Cassiopeia compat positions (banner, sidebar-left, sidebar-right, etc.) |
| `errorsearch` | int | 1 | Show search form on error pages |
| `socialtitle` | int | 0 | Emit OG title & Twitter title |
| `socialdescription` | int | 0 | Emit OG description & Twitter description |
| `socialurl` | int | 0 | Emit OG url & Twitter url |
| `socialthumbgoogle` | media | — | itemprop="image" & og:image |
| `socialthumbfacebook` | media | — | og:image (Facebook-specific) |
| `socialthumbtwitter` | media | — | twitter:image |
| `copyright` | int | — | 0/blank=none, 1=Year+site, 2=Year+custom, 3=Custom |
| `copyrighttxt` | string | — | Custom copyright text (if copyright=2 or 3) |
| `codeafterhead` | textarea | — | Injected after `<head>` opens |
| `codebeforehead` | textarea | — | Injected before `</head>` closes |
| `codeafterbody` | textarea | — | Injected after `<body>` opens |
| `codebeforebody` | textarea | — | Injected before `</body>` closes |
| `betachannel` | int | 0 | Enable beta update channel (custom field type) |

**WAM asset keys** (declared in joomla.asset.json, used via `$wa->useStyle()` / `$wa->useScript()`):
- `template.atomic.atomicstyles` (style, weight 200)
- `template.atomic.themeswitcher` (script, weight 100)
- `template.atomic.atomicjs` (script, weight 200)
- `template.atomic.js` (script, weight 500)

**Language string prefixes** (in `language/en-GB/en-GB.tpl_atomic.ini` and `en-GB.tpl_atomic.sys.ini`):
- All strings use `TPL_ATOMIC_*` prefix (e.g., `TPL_ATOMIC_BOOTSTRAP_LABEL`, `TPL_ATOMIC_ERROR_403_TITLE`).

### Cross-References

- **helper.php** (imported via `require_once`): Provides `getGoogleFontFamily()`, `getGoogleFontLink()`, `isGoogleFont()` functions used in all three main files.
- **html/layouts/header/styleswitcher.php**: Rendered via `LayoutHelper::render('header.styleswitcher', [...])` in index.php header section; emits theme switcher dropdown UI.
- **html/layouts/chromes/mobilemenu.php**: Custom module chrome used for mobilemenu position (`style="mobilemenu"`); wraps modules in offcanvas format.
- **media/css/atomic.min.css**: Core layout fixes (always load if `bsfixjoomla == 1`).
- **media/css/atomicstyles.min.css**: Decorative styles (load if `atomicstyles == 1`).
- **templates/atomic/css/template.css**: User-editable site-specific CSS (load if `customcssfile == 1`).
- **media/js/themeswitcher.min.js**: Theme switcher script (loads if `bsthemes == 1`); reads `defaultTheme` JS var and localStorage.
- **media/js/atomic.js**: Optional template JS (loads if `atomicjs == 1`).
- **templates/atomic/js/template.js**: User-editable JS (loads if `customjs == 1`).
- **templateDetails.xml**: Manifest with all parameter definitions, positions, media folder declarations, language files, update server.
- **install.php**: Post-install script (auto-configures `bootstrapsource` param based on detected Joomla version).

### Gotchas, Conventions, Joomla Version Differences

1. **Integer-mapped params**: `bootstrapsource`, `fontawesome`, `bodymenu`, `copyright`, `jqlibrary` use integer values 0–N that must be documented and respected. Adding new options requires updating the mapping logic.

2. **Theme switcher 'auto' value**: Bootstrap's `data-bs-theme` attribute only accepts 'light' or 'dark'. If user selects 'auto', index.php sets the initial `data-bs-theme="light"` as no-JS fallback, then inline script immediately corrects it based on `matchMedia('prefers-color-scheme: dark')` and localStorage (lines 256–258). This prevents FOUC (flash of unstyled content).

3. **Body menu alias vs. class construction**: The `bodymenu` param (0–6) controls how the active menu item's alias is applied to the `<body>` tag. Mode 0 (default) appends alias to default classes; mode 4–6 replace them entirely. This is useful for CSS selectors targeting specific pages or sections.

4. **Cassiopeia backward compatibility**: Original Cassiopeia template positions (`banner`, `sidebar-left`, `sidebar-right`, `topbar`, `below-top`, `bottom-a`, `bottom-b`, etc.) are **optionally** rendered only if `casspositions == 1`. This allows Atomic to act as a drop-in replacement for Cassiopeia without breaking layouts.

5. **Column string parsing**: `bootscolumns`, `headercolumns`, `footercolumns` are stored as strings like "4-4-4". PHP `explode('-', $value)` splits them. Legacy integer values (0–4) are mapped to strings for backward compat (lines 156–159). Always validate column count and sum before rendering.

6. **Sticky header class**: If `stickyhead == 1`, header gets `class="sticky"`. CSS (in atomic.css) applies `position: sticky; top: 0;` and `z-index: 1040` to keep it visible on scroll.

7. **Responsive sidebar hide**: Sidebars get `d-none d-lg-block` classes (Bootstrap display utilities) to hide on mobile/tablet, show on large breakpoints. This is hard-coded in layout logic (lines 801, 820) and cannot be overridden by params.

8. **Lazy-loaded stylesheets**: Google Fonts, FontAwesome CSS, Bootstrap Icons use `media="print" onload="this.media='all'"` trick to load asynchronously without blocking page render. Browser swaps media query from 'print' to 'all' after load. No JS fallback provided in `<noscript>` block (lines 464–474) for FontAwesome and BS Icons.

9. **Preload manager vs. WAM**: `$this->getPreloadManager()` emits `<link rel="preconnect">` hints (DNS prefetch + early connection). WAM handles actual asset registration. Preconnect should be done first (Atomic does this correctly in lines 169–192).

10. **Component vs. index params**: component.php reads identical params as index.php but only applies those relevant to asset loading (fonts, Bootstrap, GA4, etc.). It **does not** read layout params like `bootscolumns`, `headercolumns`, etc., since component view has no layout.

11. **Error page param inheritance**: error.php reads template params from `$this->params` (same Registry as index.php), so error page respects theme, fonts, Bootstrap source. Useful for branding error pages consistently.

12. **Offline page no-params**: offline.php does **not** read template params; it only uses global Joomla config (`$app->get('sitename')`, etc.). This avoids database queries during maintenance mode.

13. **Image URL cleaning**: Social meta tags use `cleanImageURL()` helper (lines 320–333) to strip query params and ensure absolute URLs for social platforms (many crawlers reject relative paths or URLs with query strings).

14. **Module position rendering**: All modules are rendered via `<jdoc:include type="modules" name="POSITION" style="CHROME" />`. Chrome defaults to 'none' (no wrapper). Custom chromes in `html/layouts/chromes/` override default wrapping. Module chrome name is passed via `style` attribute.

15. **Minified asset regeneration**: No build process auto-regenerates .min.css files. Update atomic.css or atomicstyles.css, then manually minify and save .min.css versions. CLAUDE.md in repo root documents Python minification script.

16. **Web Asset Manager weight ordering**: Lower weight = earlier in cascade. Atomic assets (weight 100–500) load after extension styles (typically weight 0). Always check joomla.asset.json weights when adding new assets.

17. **HTMLHelper::_('bootstrap.framework')** (line 962): Joomla 5/6 API to load Bootstrap JS. For J4 (bootstrapsource=1), uses `/media/vendor/bootstrap/js/bootstrap-es5.min.js`.

### Where to Look to Fix X

| Task | Files |
|------|-------|
| Change header/footer/body column layout defaults | `templateDetails.xml` lines 318–364 (field defaults); `index.php` lines 102–104 (param reads) |
| Add/remove module position | `templateDetails.xml` lines 33–65 (positions); `index.php` lines 550–894 (rendering logic for each position) |
| Modify header structure (logo placement, responsive behavior) | `index.php` lines 564–741 (header rendering with column logic) |
| Fix theme switcher or data-bs-theme behavior | `index.php` lines 111–142 (bstheme param logic), lines 256–258 (inline script), lines 488–489 (defaultTheme var) |
| Add new custom code injection hook | `templateDetails.xml` lines 427–443 (add field); `index.php` (add param read + injection point) |
| Change Bootstrap CSS source priority or add Bootswatch theme | `index.php` lines 378–404 (Bootstrap loading); `templateDetails.xml` lines 100 (bootstrapsource field options) |
| Modify error page layout or messages | `error.php` lines 59–69 (error title/desc logic), lines 147–191 (inline error-page styles), lines 202–227 (error UI rendering) |
| Fix social meta tag output or add new OG properties | `index.php` lines 299–376 (social meta tag logic); `templateDetails.xml` lines 396–425 (social param fields) |
| Adjust preconnect hints or CDN loading | `index.php` lines 169–192 (preload manager logic with CDN conditionals) |
| Duplicate logic for new template variant | Review component.php lines 1–215 to understand what params/logic are essential vs. chrome-specific; create new file with subset of index.php |

---

## Template Configuration (templateDetails.xml, install.php, helper.php, joomla.asset.json)

**Purpose**: Registry and initialization layer for the Atomic template. Declares all template parameters, module positions, custom field types, and asset dependencies; auto-detects Joomla version on install and sets safe defaults for Bootstrap/Font Awesome sources; provides Google Font resolution logic; and registers Web Asset Manager (WAM) assets for declarative inclusion.

### Key Files

- `tpl_atomic/templateDetails.xml` — Joomla extension manifest. Declares 21 module positions, ~100 template parameters organized in 11 fieldsets (Joomla Settings, CSS Settings, JavaScript, Fonts & Icons, Layout, Features, Metadata, Custom Code), custom form fields, media folder mappings, update servers, and language tags.
- `tpl_atomic/install.php` — Post-install/upgrade script (class `AtomicInstallerScript`). Auto-detects Joomla major version, sets `bootstrapsource` and `fontawesome` parameters to safe defaults, enables `atomicstyles` on fresh install only, syncs beta update channel, copies CSS/JS templates to user-writable locations, and shows success message with link to settings.
- `tpl_atomic/helper.php` — Three procedural functions for Google Font resolution: `getGoogleFontFamily()` (maps font param values 1–13 to CSS `font-family` strings), `getGoogleFontLink()` (returns preconnect + link tags for Google Fonts), `isGoogleFont()` (boolean; checks if font value 3–12 is a preset Google Font).
- `tpl_atomic/joomla.asset.json` — Web Asset Manager registry; declares 4 asset definitions (`template.atomic.atomicstyles`, `template.atomic.themeswitcher`, `template.atomic.atomicjs`, `template.atomic.js`) with URIs and load weights (100–500).

### Joomla APIs & Conventions Used

- **Registry** (`Joomla\Registry\Registry`) — Parses/serializes JSON template params from `#__template_styles.params`.
- **Factory** (`Joomla\CMS\Factory`) — Gets DBO and Application for DB queries and enqueue messages.
- **Version** (`Joomla\CMS\Version::MAJOR_VERSION`) — Detects installed Joomla version (4, 5, or 6) for defaults.
- **Form Field API** (`Joomla\CMS\Form\Field\ListField`) — Extends to create custom fields `bootstrapsource` and `fontawesome` with version-aware option lists.
- **Language API** (`Joomla\CMS\Language\Text::_()`) — Translates string keys prefixed with `TPL_ATOMIC_`.
- **Web Asset Manager** — Loads assets declared in `joomla.asset.json` when `$wa->useScript()`, `$wa->useStyle()` called in index.php.
- **Template Registry $params** — Accessed in index.php via `$params = $this->params` (Registry object); all settings are integer-keyed or string values.

### Template Parameters Schema

All params stored in `#__template_styles.params` as JSON. Integer values are mapped per field below. Language keys (e.g., `TPL_ATOMIC_KILLGENERATOR_LABEL`) resolve via en-GB translation files.

#### Fieldset: JOOMLA_SETTINGS

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `killgenerator` | radio (yes/no) | 0 | 0=no, 1=yes | Remove Joomla generator meta tag |
| `casspositions` | radio | 0 | 0=no, 1=yes | Expose Cassiopeia compat positions (banner, below-top, bottom-a, bottom-b, sidebar-*, top-*, topbar) |
| `feediting` | radio | 0 | 0=no, 1=yes | Hide front-end edit buttons for all users |
| `betachannel` | betachannel (custom field) | 0 | 0=GA, 1=beta | Swap update server URL between GA (update.xml) and beta (update-beta.xml) |

#### Fieldset: CSS_SETTINGS

**Subfieldset: CSS_SETTINGS_CSS**

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `bootstrapsource` | bootstrapsource (custom) | 2 | 0=None, 1=J4 vendor, 2=CDN, 3=J5 vendor, 4=J6 vendor, 5=Custom, 6–14=Bootswatch | Custom field (bootstrapsource.php) shows only the local option matching installed Joomla version. Values: 0=None, 1=Bootstrap 5.3.0 from J4, 2=BS 5.3.8 from CDN, 3=BS 5.3.8 from J5, 4=BS 5.3.8 from J6, 5=Custom (showon field `bootstrapcdn`), 6=Bootswatch Cosmo, 7=Flatly, 8=Minty, 9=Spacelab, 10=Yeti, 11=Cyborg, 12=Darkly, 13=Slate, 14=Superhero |
| `bootstrapcdn` | textarea | (empty) | N/A | Raw HTML snippet for custom Bootstrap. Shown only when `bootstrapsource:5`. |
| `bsfixjoomla` | radio | 1 | 0=no, 1=yes | Load atomic.min.css (Joomla/Bootstrap fixes). Should remain enabled. |
| `atomicstyles` | radio | 0 | 0=no, 1=yes | Load atomicstyles.min.css (decorative layer: glassmorphism, gradients, tokens). Fresh install defaults to 1. |
| `customcssfile` | radio | 0 | 0=no, 1=yes | Load css/template.css (user customizations). |

**Subfieldset: CSS_SETTINGS_THEMES**

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `bodymenu` | list | 0 | 0=None, 1=Append Class, 2=Append ID, 3=Append Class & ID, 4=Replace Class, 5=Replace ID, 6=Replace Class & ID | Add current menu item alias to <body> tag for page-specific styling. |
| `bsthemes` | radio | 0 | 0=no, 1=yes | Enable Bootstrap 5 theme switcher (light/dark/auto toggle). |
| `bstheme` | list | (empty) | (empty)=None, light, dark, auto, custom | Sets data-bs-theme attribute on <html>. |
| `bsthemecustom` | text | (empty) | N/A | Custom value for data-bs-theme when bstheme:custom (showon). |
| `theme` | text | (empty) | N/A | Custom data-theme attribute on <html> for non-Bootstrap theming. |
| `usergroupdata` | radio | 0 | 0=no, 1=yes | Add user group ID as data attribute on <body>. |

#### Fieldset: JAVASCRIPT_SETTINGS

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `jqlibrary` | list | 0 | 0=Joomla default, 1=jQuery 3 full, 2=jQuery 3 slim, 3=Custom, 4=None | jQuery source. Custom (3) shows showon field `jquerycdn`. |
| `jquerycdn` | textarea | (empty) | N/A | Raw HTML <script> tag for custom jQuery. |
| `atomicjs` | radio | 0 | 0=no, 1=yes | Load atomic.js (optional template JS). |
| `customjs` | radio | 0 | 0=no, 1=yes | Load js/template.js (user customizations). |

#### Fieldset: FONT_ICON_SETTINGS

**Subfieldset: FONT_SETTINGS**

Header font and body font are symmetric; each has 3 groups: system, Google Fonts, and custom system fonts.

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `headerfont` | list | 0 | 0=Bootstrap (var(--bs-body-font-family)), 1=Custom Google, 2=None, 3–12=Preset Google Fonts, 13=System Font, 97=disabled group, 98=disabled group, 99=disabled group | Font for h1–h6 and header elements. Group 98 is "Google Sans-Serif", group 99 is "Google Serif". |
| `systemFontHeader` | list | (empty) | (empty)=Select, then 12 system font stack options (system-ui, -apple-system, Segoe UI, etc.) | Shown only when headerfont:13 (system font). |
| `headergooglefont` | textarea | (empty) | N/A | Raw HTML <link rel="preconnect"> + <link href="https://fonts.googleapis.com/..."> for custom Google Font import. Shown when headerfont:1. |
| `headerfontname` | text | (empty) | N/A | CSS font-family name for custom Google Font (e.g., "Inter" or "Inter", sans-serif). Shown when headerfont:1. |
| `bodyfont` | list | 0 | Same as headerfont | Font for body text. Default 0 uses Bootstrap system fonts. |
| `systemFontBody` | list | (empty) | Same 12 system options | Shown when bodyfont:13. |
| `bodygooglefont` | textarea | (empty) | N/A | Raw import for custom body Google Font. Shown when bodyfont:1. |
| `bodyfontname` | text | (empty) | N/A | CSS font-family for custom body font. Shown when bodyfont:1. |
| `typescale` | list | 0 | 0=Bootstrap, 1=Major Third, 2=Minor Third, 3=Major Second, 4=Minor Second | Typographic scaling ratio for headings. |

**Google Font preset mappings (headerfont / bodyfont values 3–12)**:
- 3 = Inter (sans)
- 4 = Lato (sans)
- 5 = Montserrat (sans)
- 6 = Open Sans (sans)
- 7 = Roboto (sans)
- 8 = Fraunces (serif)
- 9 = Libre Baskerville (serif)
- 10 = Merriweather (serif)
- 11 = Noto Serif (serif)
- 12 = Unna (serif)

**Subfieldset: ICON_SETTINGS**

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `fontawesome` | fontawesome (custom) | 0 | 0=None, 1=FA from J4 vendor, 2=FA 7 CSS CDN, 3=FA 7 JS CDN, 4=Custom CSS, 5=Custom JS, 6=FA from J5/6 system CSS | Custom field (fontawesome.php) shows only the local option for installed Joomla. Set to 1 by install.php on J4, 6 on J5+. |
| `fontawesomecdn` | textarea | (empty) | N/A | Raw HTML <link> or <script> for custom FA. Shown when fontawesome:4 OR fontawesome:5. |
| `loadbsicons` | radio | 0 | 0=no, 1=yes | Load Bootstrap Icons (from Joomla 5+). |
| `loadfavicons` | list | 0 | 0=Joomla default, 1=Template favicons, 2=Site favicon | Which favicon set to load. |
| `maskiconcolor` | color | #000000 | N/A | Color for SVG mask icons (format: hex). |

#### Fieldset: LAYOUT_SETTINGS

**Subfieldset: LAYOUT_PAGE_SETTINGS**

| Field | Type | Default | Integer Mapping | Notes |
|-------|------|---------|-----------------|-------|
| `fluidcontainer` | radio | 0 | 0=Fixed width, 1=Fluid width | Container width mode (Bootstrap .container vs .container-fluid). |

**Subfieldset: LAYOUT_HEADER_SETTINGS**

| Field | Type | Default | Options | Notes |
|-------|------|---------|---------|-------|
| `headercolumns` | list | 12 | 12, 6-6, 4-8, 2-10, 4-4-4 | Header grid columns (sum = 12). E.g., "6-6" = two equal-width columns. |
| `logo` | media | (empty) | N/A | Logo image (media picker). |
| `sitetitle` | text | (empty) | N/A | Site title (rendered in header). |
| `sitedescription` | text | (empty) | N/A | Site tagline/description (rendered in header). |
| `stickyhead` | radio | 0 | 0=no, 1=yes | Make header sticky (position: sticky). |
| `headerbackground` | color | rgba(0, 0, 0, 0) | N/A | Header background color (RGBA format). Sets inline --atomic-header-background-color CSS var. |

**Subfieldset: LAYOUT_BODY_SETTINGS**

| Field | Type | Default | Options | Notes |
|-------|------|---------|---------|-------|
| `bootscolumns` | list | 2-8-2 | 12, 2-10, 4-8, 8-4, 10-2, 2-6-4, 2-7-3, 2-8-2, 3-6-3, 4-4-4 | Body grid (left-main-right sidebar columns). Columns not used are hidden. |

**Subfieldset: LAYOUT_FOOTER_SETTINGS**

| Field | Type | Default | Options | Notes |
|-------|------|---------|---------|-------|
| `footercolumns` | list | 12 | 12, 6-6, 4-4-4 | Footer grid columns. |
| `copyright` | list | (empty) | (empty)=None, 1=Year & site name, 2=Year & custom text, 3=Custom text | Copyright text mode. |
| `copyrighttxt` | text | (empty) | N/A | Copyright text (shown when copyright:2 OR copyright:3). |

#### Fieldset: FEATURE_SETTINGS

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `gacode` | text | (empty) | Google Analytics tag ID (e.g., G-TAGID12345). |
| `scrollreveal` | radio | 0 | Enable ScrollReveal.js for animation on scroll. |
| `errorsearch` | radio | 1 | Show search form on error pages (403/404). |

#### Fieldset: METADATA_SETTINGS

**Subfieldset: METADATA_DESCRIPTIONS**

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `socialtitle` | radio | 0 | Inject page title as og:title / twitter:title meta tags. |
| `socialdescription` | radio | 0 | Inject page description as og:description / twitter:description. |
| `socialurl` | radio | 0 | Inject page URL as og:url. |

**Subfieldset: METADATA_IMAGES**

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `socialthumbgoogle` | media | (empty) | Image for Google social preview (og:image). |
| `socialthumbfacebook` | media | (empty) | Image for Facebook preview (og:image). |
| `socialthumbtwitter` | media | (empty) | Image for Twitter preview (twitter:image). |

#### Fieldset: CUSTOM_CODE

**Subfieldset: CUSTOM_CODE_HEAD**

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `codeafterhead` | textarea | (empty) | Raw HTML injected after `</head>` tag. |
| `codebeforehead` | textarea | (empty) | Raw HTML injected before `</head>` tag. |

**Subfieldset: CUSTOM_CODE_BODY**

| Field | Type | Default | Notes |
|-------|------|---------|-------|
| `codeafterbody` | textarea | (empty) | Raw HTML injected after `<body>` opening tag. |
| `codebeforebody` | textarea | (empty) | Raw HTML injected before `</body>` closing tag. |

### Module Positions (21 total)

Declared in `<positions>` block. Atomic positions: `alert`, `header`, `header-center`, `header-right`, `topmenu`, `hero`, `leftbody`, `breadcrumbs`, `abovebody`, `main-top`, `main-bottom`, `belowbody`, `rightbody`, `footer`, `footer-center`, `footer-right`, `mobilemenu`, `error-403`, `error-404`, `debug`, `menu`. Plus 9 Cassiopeia compat positions (when `casspositions:1`): `banner`, `below-top`, `bottom-a`, `bottom-b`, `sidebar-left`, `sidebar-right`, `top-a`, `top-b`, `topbar`.

### install.php Control Flow

**Entry point**: `postflight($route, $adapter)` called by Joomla after successful install/upgrade.

1. **initFolders()** — Creates user-writable folders: `/templates/atomic/css`, `/templates/atomic/js`.
2. **copyFiles()** — Copies non-customization files from package (e.g., joomla.asset.json).
3. **ensureCustomFile()** — Creates stub `template.css` and `template.js` if missing.
4. **setBetaChannel()** — Reads `betachannel` param from DB; updates `#__update_sites.location` to swap between GA and beta URLs.
5. **setBootstrapDefault($route)** — If fresh install or Joomla version mismatch detected, sets `bootstrapsource` to matching value (1=J4, 3=J5, 4=J6). Does not overwrite CDN/Custom/Bootswatch selections on update.
6. **setFontAwesomeDefault($route)** — Sets `fontawesome` to bundled FA (1=J4, 6=J5+) on fresh install or when still at default (0).
7. **setAtomicStylesDefault($route)** — Enables `atomicstyles:1` on fresh install only (respects user choice on update).
8. **showSuccessMessage()** — Enqueues success message with link to template settings page.

**Database interaction**: Queries `#__template_styles` (template params) and `#__update_sites` (update URLs).

### helper.php Functions

**Context**: Called from index.php when rendering fonts. All are procedural functions (no namespaces, no classes).

#### getGoogleFontFamily($fontval, $pos, $customfontname = '')
- **Input**: 
  - `$fontval` (int) — font param value (0–13)
  - `$pos` (string) — "header" or "body" (used to select fallback)
  - `$customfontname` (string) — custom font-family string (used when fontval=1 or 13)
- **Logic**: Switch on $fontval:
  - 0 (default) → returns empty string (header) or `var(--bs-body-font-family)` (body)
  - 1 (custom Google) → returns `$customfontname` 
  - 2 (none) → returns "none"
  - 3–12 (preset Google Fonts) → returns quoted font name + fallback (e.g., `"Inter", sans-serif`)
  - 13 (system font) → returns `$customfontname` (from systemFontHeader / systemFontBody param)
- **Output**: CSS `font-family` value (or empty string).

#### getGoogleFontLink($fontval)
- **Input**: `$fontval` (int) — same as above
- **Logic**: Switch on 3–12 (preset Google Fonts only); returns raw HTML `<link href="https://fonts.googleapis.com/...">` tag with preconnect headers and &display=swap.
- **Output**: HTML string or empty string.
- **Note**: Called in index.php `<head>` to inject Google Font stylesheet. Custom fonts (fontval=1) rely on `headergooglefont` / `bodygooglefont` raw HTML params.

#### isGoogleFont($fontval)
- **Input**: `$fontval` (int)
- **Logic**: Returns true if fontval in [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; false otherwise.
- **Output**: Boolean.
- **Use**: In index.php to decide whether to load Google Font preconnect headers.

### joomla.asset.json Web Asset Registry

WAM assets for optional/dynamic inclusion via `$wa->useScript()` / `$wa->useStyle()` in index.php or layouts.

| Asset Name | Type | URI | Weight | Notes |
|------------|------|-----|--------|-------|
| `template.atomic.atomicstyles` | style | media/templates/site/atomic/css/atomicstyles.min.css | 200 | Decorative CSS (loaded if atomicstyles:1). |
| `template.atomic.themeswitcher` | script | media/templates/site/atomic/js/themeswitcher.min.js | 100 | Light/dark theme toggle JS (loaded if bsthemes:1). |
| `template.atomic.atomicjs` | script | media/templates/site/atomic/js/atomic.js | 200 | Optional template JS (loaded if atomicjs:1). |
| `template.atomic.js` | script | templates/atomic/js/template.js | 500 | User customization JS (loaded if customjs:1). Weight 500 ensures it loads last. |

**WAM note**: Path `media/templates/site/atomic/...` installs to `/media/templates/site/atomic/` (shared across all sites). Path `templates/atomic/...` is per-site (root level).

### Language Keys (Partial Reference)

All keys are prefixed `TPL_ATOMIC_`. See en-GB files:

- **Boolean labels**: `TPL_ATOMIC_YES`, `TPL_ATOMIC_NO`
- **Bootstrap**: `TPL_ATOMIC_BS5_JOOMLA4`, `TPL_ATOMIC_BS5_JOOMLA5`, `TPL_ATOMIC_BS5_JOOMLA6`, `TPL_ATOMIC_BS5_REMOTE`, `TPL_ATOMIC_BS_CUSTOM`, `TPL_ATOMIC_BOOTSWATCH_OPTION_*` (Cosmo, Flatly, Minty, etc.)
- **Font Awesome**: `TPL_ATOMIC_FAJOOMLA4`, `TPL_ATOMIC_FAJOOMLA5`, `TPL_ATOMIC_FACSSCDN`, `TPL_ATOMIC_FAJSCDN`, `TPL_ATOMIC_FACUSTOMCSS`, `TPL_ATOMIC_FACUSTOMJS`, `TPL_ATOMIC_NONE`
- **Google Fonts**: `TPL_ATOMIC_GOOGLEFONT_INTER`, `TPL_ATOMIC_GOOGLEFONT_LATO`, ... `TPL_ATOMIC_GOOGLEFONT_UNNA`, `TPL_ATOMIC_SYSTEMFONT_OPTION`, `TPL_ATOMIC_SYSTEMFONT_*` (SYSTEM_UI, APPLE, SEGOE, etc.)
- **Columns**: `TPL_ATOMIC_COLUMNS_12`, `TPL_ATOMIC_COLUMNS_6_6`, `TPL_ATOMIC_COLUMNS_4_8`, etc.

### Cross-References & Dependencies

- **index.php** — Reads all params via `$this->params`, calls helper.php functions (getGoogleFontFamily, getGoogleFontLink, isGoogleFont), uses WAM to include/exclude assets, renders module positions.
- **html/fields/bootstrapsource.php** — Supplies options for `bootstrapsource` param field; filters based on Joomla version.
- **html/fields/fontawesome.php** — Supplies options for `fontawesome` param field; filters based on Joomla version.
- **html/fields/betachannel.php** — Custom field (not yet inspected; assumed simple radio).
- **language/en-GB/tpl_atomic.ini** — Translates all param labels & descriptions.
- **language/en-GB/tpl_atomic.sys.ini** — Translates extension system strings (template name, short description).
- **build_package.sh** — References templateDetails.xml version when building ZIPs.

### Joomla Version-Specific Behavior

| Joomla Version | Bootstrap Default | Font Awesome Default | Notes |
|---|---|---|---|
| J4 (major=4) | 1 (vendor 5.3.0) | 1 (vendor FA6) | Oldest supported. |
| J5 (major=5) | 3 (vendor 5.3.8) | 6 (system FA6 CSS) | Mid-tier. |
| J6 (major=6) | 4 (vendor 5.3.8) | 6 (system FA6 CSS) | Latest. |

**Detection**: Via `Joomla\CMS\Version::MAJOR_VERSION` in install.php and bootstrapsource.php/fontawesome.php.

### Gotchas & Conventions

1. **Integer-mapped params are load-bearing**: Bootstrap source and Font Awesome source are stored as integers in the database; the corresponding field classes (bootstrapsource.php, fontawesome.php) must match these integer values. Mismatch = broken selectors.
2. **Custom fields filter by Joomla version**: When upgrading Joomla (e.g., J4→J5), the installed Bootstrap/FA version-specific value (1 or 3 for BS; 1 or 6 for FA) may become obsolete. install.php detects this via `$wrongVersion` logic and resets to the new version's default. CDN/Bootswatch/Custom selections are preserved.
3. **Fresh install vs. upgrade**: install.php behaves differently based on $route (`install`, `discover_install`, or `update`). Only fresh installs enable `atomicstyles` by default.
4. **User-writable customization folders**: `/templates/atomic/css/` and `/templates/atomic/js/` are created on install and never touched on upgrade (preserve user edits).
5. **Raw HTML in textarea params**: `bootstrapcdn`, `jquerycdn`, `headergooglefont`, `bodygooglefont`, `fontawesomecdn`, and custom code fields use `filter="raw"` to preserve HTML/scripts. Dangerous if not sanitized—only admins can set these.
6. **Google Font helpers are "dumb"**: getGoogleFontLink() always returns the same link for a given $fontval; customization via fontval=1 + raw textarea is required for non-preset fonts.
7. **Prefix convention**: All language keys use `TPL_ATOMIC_` prefix to avoid collisions with system strings. System (manifest) strings in tpl_atomic.sys.ini.
8. **WAM weights matter**: `template.atomic.js` has weight 500 (last) so user JS runs after all framework/library assets.
9. **Color field format**: `headerbackground` uses format="rgba" and default "rgba(0, 0, 0, 0)" to preserve alpha transparency. Not a standard hex color.

### Where to Look to Fix X

| Task | Files |
|------|-------|
| **Add a new template parameter** | templateDetails.xml `<fieldset>` → `<field>` block; add translation key to language/en-GB/tpl_atomic.ini; if integer-mapped, ensure default & options match. |
| **Add Bootstrap Bootswatch theme** | tpl_atomic/html/fields/bootstrapsource.php (append new option value 15+); templateDetails.xml to document the value; language file. |
| **Add Google Font preset** | helper.php (add case in getGoogleFontFamily, getGoogleFontLink, isGoogleFont switches); templateDetails.xml (add option value, e.g. 14); language file. |
| **Change Bootstrap/FA default per Joomla version** | install.php lines 132–133 (BS map) and 182 (FA map); bootstrapsource.php lines 42–48 (version check for option visibility); fontawesome.php lines 50–54. |
| **Sync update server URL** | install.php setBetaChannel() method; update.xml / update-beta.xml URLs; language key `TPL_ATOMIC_BETACHANNEL_DESC`. |
| **Add new module position** | templateDetails.xml `<positions>` block (add `<position>name</position>`); index.php (render position at logical location); language file (optional label). |
| **Disable asset by default but allow opt-in** | joomla.asset.json (register asset); index.php (add conditional `if ($params->get('assetparam')) { $wa->useScript(...) }`); templateDetails.xml (add param with default=0). |
| **Customize font or icon stacks** | helper.php (modify switch cases or add logic); update language file; ensure install.php defaults align. |
| **Change color field format or header bg behavior** | templateDetails.xml line 337 (headerbackground field); check index.php for inline style generation using `$params->get('headerbackground')`. |

---

## Template Overrides & Custom Fields (html/ — chromes, menus, fields, module layouts)

**Purpose**: Provides Bootstrap 5-integrated module display variants (chromes), custom admin form field types for configuration, and overrides for Joomla's core mod_menu and mod_custom modules with consistent navigation/theming patterns.

### Key Files

`tpl_atomic/html/fields/betachannel.php` — Custom radio field that syncs Atomic's update site URL between stable and beta XML feeds; extends RadioField, queries #__template_styles and #__update_sites on render.

`tpl_atomic/html/fields/bootstrapsource.php` — List field that conditionally shows Bootstrap source options per Joomla version (J4/J5/J6) while always including CDN, Custom, and Bootswatch variants; maps 15 integer values (0–14).

`tpl_atomic/html/fields/fontawesome.php` — List field filtering Font Awesome options per Joomla version (J4 vendor vs J5+ system CSS), always showing CDN and Custom; maps 7 integer values (0–6).

`tpl_atomic/html/layouts/chromes/default.php` — Standard module chrome with configurable module/header tags, class suffix, and optional title; wraps content in a semantic container.

`tpl_atomic/html/layouts/chromes/card.php` — Bootstrap card chrome for sidebars/content; uses card and card-body classes; applies header_class and moduleclass_sfx to card-title.

`tpl_atomic/html/layouts/chromes/column.php` — Grid-aligned chrome; wraps content in div.col with optional suffix classes; used for flexible layout rows.

`tpl_atomic/html/layouts/chromes/row.php` — Bootstrap row chrome; wraps content in div.row for horizontal layout grouping.

`tpl_atomic/html/layouts/chromes/none.php` — Passthrough chrome; emits only module content without wrapper.

`tpl_atomic/html/layouts/chromes/mobilemenu.php` — Inner module renderer for offcanvas mobile menu; wraps each module in div.mobilemenu-module with optional h4 title; multiple modules share the same offcanvas panel.

`tpl_atomic/html/layouts/chromes/mobilemenupanel.php` — Alias of mobilemenu for backward-compatible module assignments.

`tpl_atomic/html/layouts/header/styleswitcher.php` — Light/dark/auto theme dropdown; renders only when bsthemes template param = 1; uses Font Awesome icons (fa-circle-half-stroke, fa-sun, fa-moon) and data-theme attributes for JS integration.

`tpl_atomic/html/mod_menu/default.php` — Atomic's default menu override; adds Bootstrap 5 nav/nav-item/nav-link/dropdown classes; dispatches to item-type partials (horizontal_url, etc.); supports alias menu items and active/parent states.

`tpl_atomic/html/mod_menu/horizontal.php` — Horizontal menu variant; renders ul.nav.menu.horizontal with Bootstrap dropdown logic; dispatches type-specific partials.

`tpl_atomic/html/mod_menu/horizontal_url.php` — URL menu item partial; uses HTMLHelper::_('link',...) with nav-link and optional dropdown-toggle (if $item->deeper); preserves anchor_css, browser_nav (target/onclick), and menu_icon/menu_image support.

`tpl_atomic/html/mod_menu/horizontal_heading.php` — Heading menu item partial; renders as span.nav-item-heading (or dropdown-item if nested); no link, just text with optional icon/image.

`tpl_atomic/html/mod_menu/horizontal_component.php` — Component menu item partial (identical to horizontal_url); renders links with nav-link/dropdown-toggle classes and aria attributes.

`tpl_atomic/html/mod_menu/horizontal_separator.php` — Renders hr.dropdown-divider for menu dividers.

`tpl_atomic/html/mod_menu/horizontaltabs.php` — Tabs menu variant; ul.nav.nav-tabs.menu with Bootstrap tab styling; item-level grouping via btn-group class for top-level items.

`tpl_atomic/html/mod_menu/horizontaltabs_url.php`, `horizontaltabs_heading.php` — Tab item partials (identical to horizontal variants); separate files follow Joomla override convention.

`tpl_atomic/html/mod_menu/vertical.php` — Vertical menu variant; ul.nav.menu.vertical.flex-column; uses dropend class for sideways dropdowns; dispatches type partials.

`tpl_atomic/html/mod_menu/vertical_url.php`, `vertical_heading.php`, `vertical_component.php` — Vertical item partials; same link/icon/image logic as horizontal.

`tpl_atomic/html/mod_menu/vertical_separator.php` — Vertical divider (hr.dropdown-divider).

`tpl_atomic/html/mod_custom/default.php` — Custom module with optional background image via backgroundimage parameter; uses WebAssetManager to inject inline CSS; wraps content in div#mod-custom{ID}.mod-custom.custom.

`tpl_atomic/html/mod_custom/contentonly.php` — Strips all wrapper markup; emits raw module content only.

`tpl_atomic/html/mod_custom/modulesuffix.php` — Custom module with moduleclass_sfx support; allows CSS class injection via module settings.

`tpl_atomic/html/mod_tags_popular/default.php` — Tags popular override; renders tags as inline flex-wrap badges with tag alias as CSS class (e.g., class="tag articlename"); each tag is an anchor with RouteHelper::getComponentTagRoute().

`tpl_atomic/html/modules.php` — Legacy chrome functions: modChrome_default (h4 title + content wrapper) and modChrome_basic (passthrough); retained for backward compatibility.

### Key Logic / Structures

**Custom Admin Fields** (Extends Joomla\CMS\Form\Field\*):

- **JFormFieldBetachannel** — Extends RadioField. On render, syncUpdateSite() queries the home Atomic template style, reads betachannel param (0 or 1), then updates #__update_sites rows to point to the correct GA or BETA XML URL. Uses Factory::getDbo() and Registry for param parsing.

- **JFormFieldBootstrapsource** — Extends ListField. getOptions() reads Version::MAJOR_VERSION and conditionally builds an options array: value 0 (None), version-specific value (1=J4, 3=J5, 4=J6), values 2/5 for CDN/Custom (always), values 6–14 for Bootswatch themes (light: 6–10, dark: 11–14). Each option is a stdClass with value, text, disable, class, onclick, onchange.

- **JFormFieldFontawesome** — Extends ListField. getOptions() returns: value 0 (None), version-specific value (1=J4 vendor, 6=J5+ system), values 2–5 for CDN and custom CSS/JS. Like bootstrapsource, options are stdClass objects.

**Module Chromes** (layouts/chromes/*.php):

Each chrome receives displayData['module'], displayData['params'], displayData['attribs'] and may call $params->get(key, default) to fetch settings like module_tag, header_tag, header_class, moduleclass_sfx.

- **card** — Uses card/card-body Bootstrap classes; applies $headerClass to card-title; typical sidebar use case.
- **column** — Wraps in div.col; supports responsive grid systems.
- **row** — Wraps in div.row; groups child col modules.
- **default** — Semantic div with optional h3 title; configurable tag names.
- **none** — No wrapper; raw content.
- **mobilemenu/mobilemenupanel** — Inner renderers for offcanvas; wrap in div.mobilemenu-module with h4 title (if showtitle); multiple modules stack in the same offcanvas panel (emitted once in index.php).

**Menu Overrides**:

- **default**, **horizontal**, **horizontal_tabs**, **vertical** — Main menu layout files. Each is a foreach loop over $list (menu items); builds class string with item-{id}, default, current, active, deeper, parent, divider; dispatches to type-specific partials via ModuleHelper::getLayoutPath().

- **Item Type Partials** (\*_url, \*_heading, \*_component, \*_separator):
  - url/component — Use HTMLHelper::_('link',...) with anchor title/css/rel, menu_icon/menu_image, browser_nav (target/window.open), and nav-link/dropdown-toggle classes.
  - heading — Render as span (not a link) with nav-item-heading class; support icon/image.
  - separator — hr.dropdown-divider.

- **Key Features Across Menu Types**:
  - active/current/default/parent/deeper/divider/alias-parent-active class logic.
  - Dropdown support: $item->deeper appends dropdown-toggle, sets data-bs-toggle="dropdown", aria-expanded="false", data-bs-auto-close="outside".
  - Icon rendering: If $item->menu_icon, render span.{icon-class} with aria-hidden; if no menu_text param, add visually-hidden span for accessibility.
  - Alias menu items check $itemParams->get('aliasoptions') to find the true target and mark active if in path.
  - Horizontal menus use class="nav-item item-{id}"; vertical use class="nav-item item-{id}" but add dropend for nested dropdowns.
  - horizontaltabs variant wraps top-level items in btn-group; nested items get dropend.

**mod_custom Overrides**:

- **default** — Supports backgroundimage parameter; uses WebAssetManager to add inline style #mod-custom{ID} {background-image: url(...)}.
- **contentonly** — Raw content only.
- **modulesuffix** — Same as default but includes moduleclass_sfx.

**mod_tags_popular**:

Renders tags as a flex-wrap row of anchor badges; each anchor has href set via RouteHelper::getComponentTagRoute(), and class="tag {alias}" for per-tag CSS targeting.

**Theme Switcher** (layouts/header/styleswitcher.php):

Conditional render when bsthemes param = 1. Emits a dropdown button (id="themeBtn", data-bs-toggle="dropdown") with three menu items (data-theme="light|dark|auto"). Requires Font Awesome icons and JS handler (in js/themeswitcher.min.js or atomic.js) to listen for click and apply data-bs-theme attribute or localStorage.

### Control / Data Flow

1. **Template render** (index.php) calls `<jdoc:include type="modules" name="POSITION" style="STYLE" />` where STYLE maps to a chrome layout file.
2. Joomla's ModuleHelper resolves style="card" → tpl_atomic/html/layouts/chromes/card.php.
3. Each module in that position is rendered with the chrome, passing $module, $params (from module settings), $attribs.
4. Menu modules use mod_menu overrides: when mod_menu renders, it requires type-specific partials (e.g., horizontal_url.php) to render each menu item.
5. Custom fields are instantiated when the template settings form is loaded; betachannel.php's getInput() is called, triggering syncUpdateSite() to update the DB.
6. Theme switcher is included in the header layout; JS (themeswitcher.min.js) listens for data-theme button clicks and applies the theme.

### Parameters & Config

**Custom Field Parameters** (templateDetails.xml):

- `betachannel` — Type: betachannel (custom radio). Values: 0 (off) or 1 (on). Default: 0. Label/desc: TPL_ATOMIC_BETACHANNEL_LABEL/DESC.
- `bootstrapsource` — Type: bootstrapsource (custom list). Values: 0=None, 1=J4 (if J4), 2=CDN, 3=J5 (if J5), 4=J6 (if J6), 5=Custom, 6–14=Bootswatch. Default: 2. Label/desc: TPL_ATOMIC_BOOTSTRAP_LABEL/DESC. Showon: bootstrapcdn (5).
- `bootstrapcdn` — Type: textarea (custom HTML). Shown if bootstrapsource=5. Raw HTML snippet (link/script tags). Label/desc: TPL_ATOMIC_BOOTSTRAPCDN_LABEL/DESC.
- `fontawesome` — Type: fontawesome (custom list). Values: 0=None, 1=J4 vendor (if J4), 2=FA7 CSS CDN, 3=FA7 JS CDN, 4=Custom CSS, 5=Custom JS, 6=J5/6 system (if J5+). Default: 0. Label/desc: TPL_ATOMIC_FONTAWESOME_LABEL/DESC. Showon: fontawesomecdn (4 or 5).
- `fontawesomecdn` — Type: textarea (custom HTML). Shown if fontawesome=4 or 5. Raw HTML snippet. Label/desc: TPL_ATOMIC_FONTAWESOMECDN_LABEL/DESC.
- `bsthemes` — Type: radio. Values: 0 (off) or 1 (on). Default: (verify in manifest). Controls visibility of styleswitcher layout in header.

**Module Chrome Parameters** (per module assignment):

- `module_tag` — HTML tag wrapping the module; default "div".
- `header_tag` — HTML tag for module title; default "h3".
- `header_class` — CSS classes for title element.
- `moduleclass_sfx` — Suffix classes appended to main wrapper or card class.

**Language Keys** (language/en-GB/tpl_atomic.ini):

- TPL_ATOMIC_BETACHANNEL_LABEL, TPL_ATOMIC_BETACHANNEL_DESC
- TPL_ATOMIC_BOOTSTRAP_LABEL, TPL_ATOMIC_BOOTSTRAP_DESC, TPL_ATOMIC_BOOTSTRAPCDN_LABEL, TPL_ATOMIC_BOOTSTRAPCDN_DESC
- TPL_ATOMIC_FONTAWESOME_LABEL, TPL_ATOMIC_FONTAWESOME_DESC, TPL_ATOMIC_FONTAWESOMECDN_LABEL, TPL_ATOMIC_FONTAWESOMECDN_DESC
- TPL_ATOMIC_BS5_JOOMLA4, TPL_ATOMIC_BS5_JOOMLA5, TPL_ATOMIC_BS5_JOOMLA6 (version-specific)
- TPL_ATOMIC_BS5_REMOTE, TPL_ATOMIC_BS_CUSTOM, TPL_ATOMIC_BOOTSWATCH_OPTION_* (theme names)
- TPL_ATOMIC_FAJOOMLA4, TPL_ATOMIC_FAJOOMLA5, TPL_ATOMIC_FACSSCDN, TPL_ATOMIC_FAJSCDN, TPL_ATOMIC_FACUSTOMCSS, TPL_ATOMIC_FACUSTOMJS
- TPL_ATOMIC_NONE, TPL_ATOMIC_YES, TPL_ATOMIC_NO

**SQL Tables Updated by Fields**:

- #__template_styles — betachannel field reads params JSON to determine current state.
- #__update_sites — betachannel field updates location column to swap between GA_URL and BETA_URL.

### Cross-References

- **index.php** — Calls `<jdoc:include type="modules" name="POSITION" style="STYLE" />` using chrome styles; renders mobilemenu and styleswitcher layouts.
- **helper.php** — No direct dependency, but helpers can be added for menu/theme logic.
- **templateDetails.xml** — Defines custom field types and their field elements.
- **media/js/themeswitcher.min.js** — JS handler for theme switcher dropdown; listens to data-theme clicks and updates data-bs-theme or localStorage.
- **media/js/atomic.js** — May include menu/module JS (e.g., dropdown behavior, mobile menu toggle).
- **language/en-GB/tpl_atomic.ini** — All language keys for field labels, options, descriptions.

### Gotchas / Conventions

- **Integer Mapping**: Bootstrap source and Font Awesome values are hardcoded integers (0–14 and 0–6 respectively); changing them requires updating both the field class and any code that reads them (e.g., index.php conditionals).

- **Version Detection**: Custom fields use Joomla\CMS\Version::MAJOR_VERSION to detect J4/J5/J6 at form render time. If the Joomla version is upgraded/downgraded after install, the field will re-render with different options on next template edit.

- **Joomla Override Conventions**: Menu partials must follow mod_menu/TYPE_ITEMTYPE.php naming (e.g., horizontal_url.php, vertical_heading.php). Non-standard names will not be found by ModuleHelper::getLayoutPath().

- **Chrome Style Attribute**: The style attribute in `<jdoc:include type="modules" name="..." style="STYLE" />` must match a layout file in html/layouts/chromes/STYLE.php or a legacy function modChrome_STYLE() in modules.php. Invalid styles silently fall back to default chrome.

- **Beta Channel Sync**: The betachannel field performs DB writes on every template settings form load (not just on save). This is by design to catch manual URL changes in #__update_sites, but means the query runs even if the setting hasn't changed.

- **Mobile Menu Offcanvas**: The mobilemenu chrome does not render the offcanvas wrapper itself; the wrapper is emitted once in index.php as a container element. Each module assigned to the mobilemenu position renders its content inside via the chrome.

- **Menu Item Icons**: Menu icons are pulled from $item->menu_icon (a CSS class string like "fa-solid fa-home"); the chrome code wraps this in a span and adds aria-hidden="true" for screen readers.

- **Alias Menu Items**: Menu items with type="alias" point to another menu item via $itemParams->get('aliasoptions'); the override code checks if the aliased target is in the current breadcrumb path to determine active state. This allows menu items to mirror other branch visibility.

- **Tag Alias Classes**: mod_tags_popular uses $item->alias as a CSS class; ensure tag aliases are valid CSS identifiers (URL-safe, no spaces/special chars), or HTML-escape them if they may contain invalid characters.

### Where to Look to Fix X

- **Add a new Bootstrap source option** → tpl_atomic/html/fields/bootstrapsource.php (add line to $options array); update language keys in tpl_atomic.ini; update index.php if new conditional logic needed for asset loading.

- **Change Font Awesome to a different version or CDN** → tpl_atomic/html/fields/fontawesome.php (modify value mapping); update fontawesomecdn parameter hints in templateDetails.xml; update index.php Font Awesome loading logic.

- **Create a new menu style (e.g., "megamenu")** → Create tpl_atomic/html/mod_menu/megamenu.php; create megamenu_url.php, megamenu_heading.php, megamenu_separator.php for item types; assign modules with style="megamenu".

- **Fix mobile menu offcanvas positioning or styling** → tpl_atomic/html/layouts/chromes/mobilemenu.php (chrome structure) and tpl_atomic/media/css/atomic.css (offcanvas CSS); index.php emits the offcanvas wrapper element itself, so check there for id/class/position.

- **Customize theme switcher dropdown appearance or add new themes** → tpl_atomic/html/layouts/header/styleswitcher.php (change button/menu HTML); tpl_atomic/media/js/themeswitcher.min.js (JS theme apply logic); tpl_atomic/media/css/atomicstyles.css (theme CSS custom properties).

- **Add active-state icon to menu items** → Modify tpl_atomic/html/mod_menu/*_url.php partials; check $item->current or $item->id == $active_id to conditionally append an icon class or additional HTML.

- **Allow per-module chrome config in admin UI** → Extend modules.php or create a new chrome with dynamic params; ensure module assignment form allows style selection (built-in Joomla feature).

- **Fix tag badge styling or routing** → tpl_atomic/html/mod_tags_popular/default.php (structure/class logic); verify RouteHelper::getComponentTagRoute() arguments; check tpl_atomic/media/css/atomicstyles.css for .tag class definitions.

---

## CSS/JS Assets & Theme Switcher (media/, css/, js/)

**Purpose**: Manages a two-layer CSS architecture (layout + decorative styles), optional JavaScript, and light/dark/auto theme switching. The subsystem implements Joomla's Web Asset Manager (WAM) conventions, provides design tokens via CSS custom properties, and exposes toggle controls for end-user theme selection without modifying core files.

### Key Files

- `tpl_atomic/media/css/atomic.css` (~359 lines) — Core layout & Joomla/Bootstrap fixes: header/footer structure, dropdowns, mobile menu, pagination, responsive breakpoints, z-index stack. Always load.
- `tpl_atomic/media/css/atomic.min.css` — Minified version; served to users. Regenerate after edits.
- `tpl_atomic/media/css/atomicstyles.css` (~996 lines) — Optional decorative layer: design tokens (CSS custom properties), glassmorphism, gradients, hero styling, navigation hover states, sidebar cards, login form, tag badges, theme transitions.
- `tpl_atomic/media/css/atomicstyles.min.css` — Minified version; loaded via WAM if `atomicstyles` param = 1.
- `tpl_atomic/css/template.css` — User-editable custom CSS (template-specific, per-site). Loaded last if `customcssfile` param = 1. Overrides atomicstyles.css tokens.
- `tpl_atomic/media/js/atomic.js` (~2 lines) — Placeholder; minimal JS. Loaded if `atomicjs` param = 1 via WAM.
- `tpl_atomic/media/js/themeswitcher.min.js` (~119 lines) — Light/dark/auto theme switcher. Manages localStorage key `theme`, controls `data-bs-theme` on `<html>`, detects OS preference. Always loads via WAM (weight 100).
- `tpl_atomic/js/template.js` — User-editable custom JavaScript (template-specific). Loaded if `customjs` param = 1 via WAM.
- `tpl_atomic/joomla.asset.json` — WebAssetManager registration for WAM assets: `template.atomic.atomicstyles`, `template.atomic.themeswitcher`, `template.atomic.atomicjs`, `template.atomic.js`.
- `tpl_atomic/html/layouts/header/styleswitcher.php` — Renders dropdown menu for theme toggle (light/dark/auto). Outputs `<button id="themeBtn">` + `.dropdown-item[data-theme]` options. Conditional on `bsthemes` param = 1.
- `tpl_atomic/media/favicons/site.webmanifest` — PWA manifest (displays theme colors, icons).
- `tpl_atomic/media/images/` — Template thumbnail, logo, preview. Serves as fallback reference in admin.
- `tpl_atomic/media/fonts/index.html` — Placeholder; fonts folder (reserved for custom fonts).

### Architecture: Two-Layer CSS System

#### Layer 1: atomic.css (Core Layout)
Handles structural incompatibilities between Bootstrap 5 and Joomla outputs. **Always enable** (via `bsthemes` param = 1 → outputs `<link>` outside WAM). Contains:
- **Base**: typescale utility classes (`[data-typescale="major-third|minor-third|major-second|minor-second"]` for `h1`–`h6` sizing), smooth scroll, last-paragraph margin reset, min-height 100vh on body.
- **Header**: sticky behavior, z-index 1040, theme switcher dropdown positioning.
- **Navigation (topmenu)**: hover-open dropdowns (desktop/vertical/flyout), nav-item gap, nested dropdown left-positioning.
- **Main content**: z-index 1 for `#main-content`, padding/spacing for modules.
- **Breadcrumb**: margin reset, custom divider (›).
- **Mobile menu**: display block (flex inside), static dropdown-menu, hidden toggles. Responsive breakpoint: hidden <576px, shown ≥576px.
- **Search (mod-finder)**: flexbox no-wrap, border-radius fix for autocomplete.
- **Login/reset forms**: fieldset spacing, label margin, star color, button svg sizing.
- **Edit buttons**: inline-style tooltips (role="tooltip"), z-index 1070.
- **Footer**: z-index 1, margin-top 2rem, list reset, mobile column gutters <768px.
- **Z-Index Stack**: 1070 (edit tooltips) > 1050 (alert bar) > 1040 (header + dropdowns) > 1 (main content) > 0 (ambient orbs).

#### Layer 2: atomicstyles.css (Decorative & Tokens)
**Optional** (enable via `atomicstyles` param = 1). Provides visual opinion through design tokens and glassmorphism. Can be disabled for plain Bootstrap.

**CSS Custom Properties** (defined in `:root`, with light/dark overrides):
- `--page-bg-start/mid/end`: Gradient stops for body background (light: #f8fafc → #f1f5f9 → #f8fafc; dark: #0f172a → #1e293b → #0f172a).
- `--glass-bg`: Glassmorphism background (light: rgba(255,255,255,0.6); dark: rgba(255,255,255,0.05)).
- `--glass-border`: Glassmorphism border (light: rgba(255,255,255,0.75); dark: rgba(255,255,255,0.1)).
- `--glass-blur`: Backdrop blur (12px).
- `--glass-radius`: Border-radius for glass elements (16px).
- `--accent-primary`: Primary accent (#6063F1 indigo).
- `--accent-secondary`: Secondary accent (#0ea5e9 sky blue).
- `--accent-tertiary`: Tertiary accent (#0d9488 teal).
- `--accent-warm`: Warm accent (#f59e0b amber).
- `--accent-start/end`: Gradient stops for buttons, links, hero overlays.
- `--text-primary/secondary`: Text colors (light: #0f172a / rgba(15,23,42,0.55); dark: #f1f5f9 / rgba(241,245,249,0.6)).
- `--card-shadow`: Box shadow for cards (light: 0 4px 24px rgba(15,23,42,0.06); dark: 0 8px 32px rgba(0,0,0,0.25)).
- `--nav-hover-bg`: Navigation hover background (light: rgba(99,102,241,0.08); dark: rgba(255,255,255,0.08)).
- `--transition-speed`: Animation duration (0.3s); also `--theme-easing: cubic-bezier(0.4,0,0.2,1)` for smooth theme transitions.
- **Inline styles set by index.php**:
  - `--atomic-header-font`: Font family (set via `<style>` in head).
  - `--atomic-body-font`: Font family (set via `<style>` in head).
  - `--atomic-header-background-color`: Header background color (set via `<style>` in head).

**Components** in atomicstyles.css:
- **Alert bar** (`.alertbar`): Gradient background, z-index 1050, white text, center-aligned.
- **Header**: Backdrop-filter blur (25px), gradient bottom border (semi-transparent), site title gradient text.
- **Glass utility** (`.glass`): Applies `--glass-*` tokens, hover border color shift to accent.
- **Buttons** (`.btn-accent`, `.btn-secondary`, `.btn-glass`): Gradient fill, hover translateY(-2px) + shadow.
- **Icon button** (`.atomic-iconbtn`): 38px square, flex center, hover lift + shadow. Used for theme switcher.
- **Gradient text** (`.gradient-text`): Background clip text effect.
- **Section label/kicker** (`.section-label`): Small caps, accent color, upper letter-spacing.
- **Tag badges** (`.tag`, `.tag.blue`, `.tag.teal`, `.tag.amber`): Inline-block, semi-transparent background + border, matching accent colors. Tag aliases: `.tag.typography` (blue), `.tag.layout` (teal), `.tag.themes` (amber), `.tag.fonts` (blue).
- **Navigation** (`.navigation .nav-link`, `.dropdown-item`): Font-weight 600, border-radius 12px, hover/active bg toggle, dropdown-menu glassmorphic (backdrop-filter blur 20px).
- **Hero** (`.hero`, `.hero-card`, `.hero-stats`): Background image + overlay, large heading (2.5rem), stats row with number/label pairs.
- **Feature cards** (`.feature-card`): Icon box (56px, gradient bg), centered text, hover effect.
- **Sidebar cards** (`.container-sidebar-left/right .card`): Glass styling, nav-link hover/active states, custom module padding.
- **Login form** (`.mod-login`): Glass-styled inputs, gradient button, password-toggle styling.
- **Footer**: Gradient divider line above, link colors (accent), footer h6 text-transform uppercase.
- **Article badges/tags** (`.tags a.btn`, `.com-content-article .badge`): Inline-block badges matching tag styling.
- **Theme transitions**: All surfaces use `transition: property var(--transition-speed) var(--theme-easing)` for crossfade effect on theme toggle.

### CSS Loading Order (index.php, lines ~213–462)

1. **Bootstrap** (source depends on `bootstrapsource` param):
   - 0: None
   - 1: Joomla J4 vendor
   - 2: CDN (jsDelivr)
   - 3: Joomla J5 vendor
   - 4: Joomla J6 vendor
   - 5: Custom HTML (textarea in settings)
   - 6–14: Bootswatch themes (Cosmo, Flatly, Minty, Spacelab, Yeti, Cyborg, Darkly, Slate, Superhero)
2. **Google Fonts** (if `headerfont` or `bodyfont` = 1, calls `helper.php::getGoogleFontLink()`)
3. **Inline `<style>`** with CSS custom properties: `--atomic-header-font`, `--atomic-body-font`, `--atomic-header-background-color` (set from template params)
4. **Font Awesome** (if `fontawesome` param ≠ 0):
   - 0: None
   - 1: Joomla J4 system font
   - 2: FA7 CSS CDN
   - 3: FA7 JS CDN
   - 4–5: Custom
   - 6: Joomla J5/J6 system font
5. **Bootstrap Icons** (if `bootstrapicons` param = 1)
6. **WebAssetManager outputs**:
   - `atomicstyles.min.css` (if `atomicstyles` = 1; WAM weight 200)
   - `template.css` (if `customcssfile` = 1; loaded via `<link>` outside WAM, comes after WAM)
7. **atomic.min.css** (if `bsthemes` = 1; loaded outside WAM on line 458, comes AFTER Bootstrap/atomicstyles for override priority)

**Critical**: `atomic.min.css` is loaded **outside** WAM to ensure it overrides Bootstrap incompatibilities regardless of WAM weight ordering.

### JavaScript & Theme Switcher

#### Theme Switcher (themeswitcher.min.js)
- **Storage key**: localStorage key = `'theme'`
- **Resolution order**: 
  1. Check localStorage for stored preference
  2. Check for `defaultTheme` variable (set inline by index.php before script load)
  3. Return empty string (OS preference or none)
- **Theme values**: `'light'` | `'dark'` | `'auto'` (auto → detect via `window.matchMedia('(prefers-color-scheme: dark)').matches`)
- **DOM updates**:
  - Sets `data-bs-theme` attribute on `<html>` (e.g., `<html data-bs-theme="dark">`)
  - Updates icon in `#themeBtn`: FontAwesome classes (`fa-sun` / `fa-moon` / `fa-circle-half-stroke`) or inline SVG
  - Marks `.dropdown-item[data-theme]` as `.active` and sets `aria-pressed="true"`
- **Event listeners**:
  - Listens for `.dropdown-item[data-theme]` clicks (event delegation)
  - If theme = 'auto', removes localStorage entry; otherwise stores theme name
  - Calls `applyTheme()` to update DOM
  - Responds to `window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change')` when set to 'auto'
  - Applies theme on `DOMContentLoaded`

#### inline `<style>` in Head (index.php, line ~488)
```php
<?php if ($bstheme): ?>
<script>var defaultTheme = '<?php echo $bstheme; ?>';</script>
<?php endif; ?>
```
Sets `defaultTheme` variable (value: '', 'light', 'dark', 'auto', or custom value from `bsthemecustom`) before themeswitcher.min.js loads.

#### Dropdown Rendering (styleswitcher.php)
- Conditionally renders only if `$bsthemes = 1`
- Outputs `<button id="themeBtn">` with icon (FontAwesome or SVG fallback)
- Three `.dropdown-item[data-theme]` buttons: light (sun icon), dark (moon icon), auto (half-circle icon)
- Each item has aria-label for accessibility

### Template Parameters (templateDetails.xml)

**CSS Settings**:
- `bootstrapsource` (list, 0–14) — Bootstrap source (see loading order above)
- `bootstrapcdn` (textarea) — Custom Bootstrap HTML if `bootstrapsource` = 5
- `bsthemes` (radio, 0|1) — Enable theme switcher dropdown
- `bstheme` (list: '', 'light', 'dark', 'auto', 'custom') — Default theme
- `bsthemecustom` (text, if `bstheme` = 'custom') — Custom theme value
- `atomicstyles` (radio, 0|1) — Load atomicstyles.min.css
- `customcssfile` (radio, 0|1) — Load templates/atomic/css/template.css
- `bodymenu` (list, 0–6) — Menu alias body attribute handling (None / Append class / Append ID / Append both / Replace class / Replace ID / Replace both)

**JavaScript Settings**:
- `jqlibrary` (list, 0–4) — jQuery source (Off / J4 / Slim / Custom CDN / None)
- `jquerycdn` (textarea) — Custom jQuery HTML if `jqlibrary` = 3
- `atomicjs` (radio, 0|1) — Load media/templates/site/atomic/js/atomic.js
- `customjs` (radio, 0|1) — Load templates/atomic/js/template.js

**Font/Icon Settings**:
- `headerfont` (list, 0–11) — Font source for headers (Bootstrap default / Google Font options)
- `bodyfont` (list, 0–11) — Font source for body
- `fontawesome` (list, 0–6) — Font Awesome source
- `bootstrapicons` (radio, 0|1) — Load Bootstrap Icons

### WebAssetManager (joomla.asset.json)

Registers WAM assets for lazy-loading via `$wa->useStyle()/useScript()`:

```json
{
  "template.atomic.atomicstyles": { "type": "style", "uri": "media/templates/site/atomic/css/atomicstyles.min.css", "weight": 200 },
  "template.atomic.themeswitcher": { "type": "script", "uri": "media/templates/site/atomic/js/themeswitcher.min.js", "weight": 100 },
  "template.atomic.atomicjs": { "type": "script", "uri": "media/templates/site/atomic/js/atomic.js", "weight": 200 },
  "template.atomic.js": { "type": "script", "uri": "templates/atomic/js/template.js", "weight": 500 }
}
```

**Weights** determine load order: lower weight loads first. Theme switcher (100) loads before custom JS (500).

### Control Flow

1. **Install**: `install.php::preflight()` auto-detects Joomla version and sets `bootstrapsource` (1=J4, 3=J5, 4=J6) and `fontawesome` accordingly.
2. **Render (index.php)**:
   - Load template params: `$atomicstyles`, `$customcssfile`, `$atomicjs`, `$customjs`, `$bstheme`, `$bsthemes`, etc.
   - Instantiate WebAssetManager: `$wa = $this->getWebAssetManager()`
   - Output Bootstrap source (conditional on `bootstrapsource`)
   - Output Google Fonts (if `headerfont`/`bodyfont` > 0; calls `helper.php`)
   - Output inline `<style>` with CSS custom properties (fonts, header background)
   - Output Font Awesome / Bootstrap Icons (conditional)
   - Register and use WAM styles: `$wa->registerAndUseStyle()` for Font Awesome, `$wa->useStyle('template.atomic.atomicstyles')` if `atomicstyles` = 1
   - Output `atomic.min.css` directly via `<link>` (outside WAM, line 458)
   - Output `template.css` directly via `<link>` if `customcssfile` = 1 (line 461)
   - Output jQuery (conditional on `jqlibrary`)
   - Output inline `<script>` setting `var defaultTheme = '...'` if `bstheme` is set
   - Register and use WAM scripts: `$wa->useScript('template.atomic.themeswitcher')` (always), `$wa->useScript('template.atomic.atomicjs')` if `atomicjs` = 1, `$wa->useScript('template.atomic.js')` if `customjs` = 1
   - Output Google Analytics (if enabled)
3. **User Interaction**:
   - User clicks `.dropdown-item[data-theme]` (rendered by styleswitcher.php, position header-right)
   - themeswitcher.min.js event listener fires, stores theme in localStorage, updates `data-bs-theme` on `<html>`
   - CSS transitions (via `--transition-speed` + `--theme-easing`) crossfade colors across all surfaces

### Media/ vs templates/atomic/ Split

- **`media/templates/site/atomic/`** (shared across all sites using this template):
  - CSS: `atomic.min.css`, `atomicstyles.min.css` (core/optional decorative)
  - JS: `atomic.js`, `themeswitcher.min.js` (shared logic)
  - Images: `template_preview.png`, `template_thumbnail.png`, `logo.png`
  - Favicons: manifest, icons, etc.
- **`templates/atomic/`** (per-site customization):
  - `css/template.css` — Site-specific CSS overrides
  - `js/template.js` — Site-specific JS
  - Never touch `index.php`, `templateDetails.xml`, or `joomla.asset.json` in custom deployments

### Minified Asset Regeneration

After editing `atomic.css` or `atomicstyles.css`, regenerate minified versions. Provided Python script in CLAUDE.md:

```python
python3 -c "
import re
def minify_css(css):
    css = re.sub(r'/\*[\s\S]*?\*/', '', css)
    css = re.sub(r'\s+', ' ', css)
    css = re.sub(r'\s*([{}:;,>+~])\s*', r'\1', css)
    css = re.sub(r';(})', r'\1', css)
    css = re.sub(r'\(\s+', '(', css)
    css = re.sub(r'\s+\)', ')', css)
    return css.strip()
for name in ['atomic', 'atomicstyles']:
    src = f'tpl_atomic/media/css/{name}.css'
    with open(src) as f: content = f.read()
    with open(src.replace('.css', '.min.css'), 'w') as f: f.write(minify_css(content))
"
```

Also regenerate after minification:
- `.min.css.map` files (source maps) are automatically generated by build tools; if using a modern bundler, update accordingly.

### Joomla Version Compatibility

- **Joomla 4**: Bootstrap source 1 (vendor bootstrap), Font Awesome 1 (system font), supports Web Asset Manager
- **Joomla 5**: Bootstrap source 3 (vendor bootstrap), Font Awesome 6 (system font), improved WAM support
- **Joomla 6**: Bootstrap source 4 (vendor bootstrap), Font Awesome 6 (system font), same WAM conventions

`install.php` auto-detects version and sets defaults. Template syntax (index.php) is compatible with all three versions.

### Gotchas & Conventions

1. **atomic.min.css loaded outside WAM**: Ensures overrides apply before component/module output. Don't move it to WAM.
2. **CSS custom properties set via inline `<style>`**: Font families and header background must be set inline in index.php (lines ~428–448) before atomicstyles.css loads, otherwise cascade fails.
3. **Theme localStorage persistence**: `themeswitcher.min.js` stores preference in localStorage key `'theme'`. Clearing localStorage or deleting via DevTools will reset to default/OS preference.
4. **data-bs-theme applied to `<html>`**: Bootstrap 5.3+ uses this attribute for dark mode; earlier Bootstrap versions ignore it. Test theme toggle against your Bootstrap version.
5. **Font Awesome icon fallback**: If Font Awesome fails to load, themeswitcher.min.js falls back to inline SVG (lines 9–11 of themeswitcher.min.js).
6. **Template language prefix**: All language strings use prefix `TPL_ATOMIC_` (e.g., `TPL_ATOMIC_ATOMICSTYLES_LABEL`).
7. **Integer-mapped parameters**: `bootstrapsource`, `fontawesome`, `jqlibrary`, etc. use integer values that must be exactly matched in index.php conditional statements.
8. **No vendor CSS/JS in repo**: Bootstrap, Font Awesome, jQuery are loaded via CDN or Joomla vendor/ folder; not included in this subsystem's scope.
9. **Minified file regeneration is manual**: No build pipeline; edit source CSS and run the Python minify script manually before commit.
10. **z-index: 1040 header overlap**: If overlays (modals, popovers) appear behind header, increase their z-index above 1040.

### Where to Look to Fix X

- **Theme switcher not appearing**: Check `bsthemes` param = 1 in templateDetails.xml settings; verify `styleswitcher.php` is rendered in header layout (call `$this->getBuffer('header')` or equivalent in index.php around position `header-right`).
- **Dark mode not working**: Verify `themeswitcher.min.js` loads (check Network tab); confirm `data-bs-theme` attribute set on `<html>` (inspect element); check localStorage `'theme'` key for stored preference; verify Bootstrap version supports `data-bs-theme` (5.3+).
- **CSS colors not changing on theme toggle**: Check that CSS custom properties use light/dark selectors in atomicstyles.css (lines 42–65); ensure `--transition-speed` and `--theme-easing` are defined; verify atomicstyles.min.css is loaded (check Network tab or enable `atomicstyles` param).
- **Font families not applying**: Verify Google Font links loaded (Network tab); check inline `<style>` in head contains `--atomic-header-font` and `--atomic-body-font` with correct font names from index.php (lines ~436–448); ensure font parameter selected (e.g., `headerfont = 3` for Inter).
- **Header background color stuck**: Check that `--atomic-header-background-color` is set in inline `<style>` (index.php line ~444); verify parameter `headerbackgroundcolor` is stored in database (use `$this->params->get('headerbackgroundcolor')`); confirm atomicstyles.css references this variable (line 159: `var(--atomic-header-background-color, var(--glass-bg))`).
- **atomic.min.css not overriding Bootstrap**: Verify it's loaded AFTER Bootstrap in HTML source order (should appear after `<link ... bootstrap.min.css>`); check that selectors in atomic.css use higher specificity or `!important` where necessary; regenerate minified file after any edits.
- **Custom CSS not loading**: Verify `customcssfile` param = 1; check that `templates/atomic/css/template.css` exists in the site's template folder (not media/); confirm `<link>` tag appears in HTML source AFTER atomicstyles.min.css.
- **Custom JS not firing**: Verify `customjs` param = 1; check that `templates/atomic/js/template.js` exists; open browser console (F12) for JS errors; confirm WAM weight ordering (template.js has weight 500, loads after other scripts).
- **Module colors wrong in light/dark**: Add theme-specific selectors (e.g., `[data-bs-theme="dark"] .my-class { ... }`) to template.css or html/layouts/mod_*/default.php; avoid hardcoded colors; use CSS custom properties instead.
- **Dropdown menu invisible on mobile**: Check mobile menu CSS in atomic.css (lines 157–178); verify `.mobilemenu-offcanvas .dropdown-menu` is set to `position: static; display: block`; test responsive breakpoint (mobile = <576px).

---

## Sample Data Plugin (plg_sampledata_atomic)

**Purpose**: Populates Joomla with demo content (4 articles + 1 category), sample menus, and ~22 modules across all Atomic template positions. Hooks into Joomla's **System > Sample Data** feature to deliver a ready-to-use site layout in 6 sequential steps. Autoregistration via PSR-4 DI service provider; enables itself on install/update.

### Key Files

- `plg_sampledata_atomic/atomic.xml` — Plugin manifest. Declares namespace `Kontent\Plugin\SampleData\Atomic`, folder structure, language strings. Group: `sampledata` (standard for sample-data plugins).
- `plg_sampledata_atomic/script.php` — Install hook (`PlgSampledataAtomicInstallerScript`): auto-enables plugin on install/update/postflight via `#__extensions` table query.
- `plg_sampledata_atomic/services/provider.php` — DI service provider: registers `PluginInterface` as `Atomic` class, injects `DispatcherInterface`, `DatabaseInterface`, application, using Factory.
- `plg_sampledata_atomic/src/Extension/Atomic.php` — Main plugin class (2477 lines). Extends `CMSPlugin`, uses `DatabaseAwareTrait`. Implements 6 AJAX step methods + 40+ helpers.
- `plg_sampledata_atomic/language/en-GB/plg_sampledata_atomic.ini` — Runtime strings: success/error messages per step, descriptions.
- `plg_sampledata_atomic/language/en-GB/plg_sampledata_atomic.sys.ini` — System (installer) strings: plugin name, XML description.

### Key Logic / Structures

#### Entry Points

**`onSampledataGetOverview()`** — Returns `stdClass` with:
- `name` = "atomic" (plugin ID)
- `title` = `PLG_SAMPLEDATA_ATOMIC_OVERVIEW_TITLE`
- `description` = `PLG_SAMPLEDATA_ATOMIC_OVERVIEW_DESC`
- `icon` = "palette"
- `steps` = 6 (tells Joomla UI to show 6 progress steps)

Joomla's sample-data UI calls this once to register the plugin and display the wizard.

#### Ajax Step Methods

Each `onAjaxSampledataApplyStepN()` (N = 1–6) handles input validation:
1. Checks `$app->getInput()->get('type') === 'atomic'` (ensures only Atomic is processing)
2. Wraps in `ob_start()` / `cleanBuffers()` to suppress extensions' debug output (keep JSON clean for AJAX)
3. Returns `array['success' => bool, 'message' => string]` per step result

**Step 1: Welcome Article + Category**
- Creates `#__categories` entry (alias: "atomic", extension: "com_content", access from `$app->get('access', 1)`)
- Unflag all existing featured articles (clears `#__content.featured = 1` and `#__content_frontpage`)
- Creates "Welcome to Atomic" article with intro + full text, featured = 1, ordering = 1
- Checks `alreadyInstalled()` via "welcome-to-atomic" alias to prevent re-runs

**Step 2: Multi-Article + Tags**
- Creates "Getting Started", "Explore Atomic Features", "Style Guide" articles (featured = 1 for first two; featured = 0 for third)
- Calls `ensureSampleTags()` → creates 5 tags: bootstrap, typography, layout, themes, fonts (in `#__tags`, parent_id = 1)
- Assigns tags to articles via `assignTagsToArticle()` (updates article model with `tags` key)
- Skips if articles already exist

**Step 3: Menu Type + Menu Items**
- Ensures menu type: prefer existing "mainmenu" or create "mainmenu-atomic" (in `#__menu_types`, client_id = 0)
- Creates 4 top-level menu items:
  - Home → category blog view (1 leading article, 0 intro, 0 links), home = 1
  - Getting Started → single article view
  - Features → single article view
  - Style Guide → single article view
- Creates 3 child items under Features (submenu demo) pointing to article anchors (#positions, #settings, #design-tokens)

**Step 4: Navigation Modules (8 total)**
- **topmenu** (`mod_menu`, horizontal layout, endLevel = 0 = show all)
- **sidebar-menu** (`mod_menu`, showAllChildren = 0, endLevel = 0)
- **mobilemenu** (`mod_menu`, offcanvas use)
- **header-right search** (`mod_finder`, showtitle = 0)
- **breadcrumbs** (`mod_breadcrumbs`, showHere = 1, showHome = 1)
- **main-top** custom module (glass-style info box, ordering = 7)
- **main-bottom** custom module (ordering = 8)
- **leftbody sidebar nav** (`mod_menu`, startLevel = 1, endLevel = 1, top-level items only, ordering = 9)

**Step 5: Position Demo Modules (13 custom HTML + special types)**
Sequentially populate all Atomic positions with sample content. Each module uses `section-label` + Font Awesome icons + inline styles:
1. **alert** (ordering 1) — Welcome banner with CTA
2. **hero** (ordering 4) — Hero card: title, lead, stats grid (50+ Settings, 17 Positions, 9 Themes, 1500+ Fonts)
3. **abovebody** (ordering 5) — Featured section info
4. **leftbody info** (ordering 6, showtitle = 1) — Sidebar description
5. **leftbody tags** (ordering 7, showtitle = 1) — `mod_tags_popular` with layout = "atomic:default"
6. **rightbody quick links** (ordering 8, showtitle = 1) — List with icons
7. **rightbody tip** (ordering 9, showtitle = 1) — Design guidance
8. **rightbody login** (ordering 10, showtitle = 1) — `mod_login`
9. **belowbody next step** (ordering 11) — CTA section
10. **footer** (ordering 12) — Brand + links
11. **footer-center** (ordering 13) — Resources list
12. **footer-right** (ordering 14) — About links

**Step 6: Enhanced Content + Finalization**
- Appends component-area section to "Welcome to Atomic" article (if not already present, checks for marker `<!-- ATOMIC_SAMPLE_COMPONENT_START -->`)
- Calls `applyTemplateSettings()`: sets Atomic template (row in `#__template_styles`) as site default (home = 1), applies sample params:
  - `headercolumns` = "6-6"
  - `footercolumns` = "4-4-4"
  - `bsthemes` = "1" (J4 Bootstrap source)
  - `sitetitle` = "Atomic Template for Joomla"
  - `sitedescription` = "Powerful. Flexible. SEO friendly."
  - `logo` = "media/templates/site/atomic/images/logo.png"
  - `copyright` = "1"
- Calls `fixFeaturedArticleOrdering()`: sets explicit ordering in `#__content_frontpage` (welcome-to-atomic = 1, getting-started = 2, explore-atomic-features = 3)
- Calls `setHomeMenuPageHeading()`: sets `show_page_heading = 0` on home menu item
- Removes duplicate breadcrumbs/footer modules (keeps only "Atomic" versions)
- Removes any "Atomic" modules in unsupported positions (cleanup)

#### Helpers by Category

**Article / Content Queries**
- `alreadyInstalled()` — checks if "welcome-to-atomic" exists (detect prior runs)
- `ensureSampleCategory()` — get or create "atomic" category; uses CategoryModel MVC
- `articleExists(alias)`, `getArticleIdByAlias(alias)` — lookup by alias in `#__content`
- `createWelcomeArticle(catid)`, `createGettingStartedArticle(catid)`, `createFeaturesArticle(catid)`, `createStyleGuideArticle(catid)` — use `com_content` ArticleModel to persist; all set access, language = "*", created_by from identity

**Menu Queries & Creation**
- `ensureMenuType()` — get "mainmenu" or create "mainmenu-atomic"
- `menuItemExists(menuType, alias)`, `getMenuItemIdByAlias(menuType, alias)` — lookup in `#__menu`
- `getContentComponentId()` — fetch `com_content` extension_id via ExtensionHelper
- `createArticleMenuItem(menuType, title, articleId, isHome)` — ItemModel save with type = "component", link = "index.php?option=com_content&view=article&id=..."
- `createCategoryBlogMenuItem(menuType, title, catId, isHome)` — same, view = "category", layout = "blog", plus params for num_leading_articles = 1, show_* flags
- `createChildMenuItem(menuType, title, parentId, link)` — submenu item (level = 2, parent_id set); strips `#` fragment for Joomla lookup

**Module Creation / Query**
- `moduleExists(position, module, title)`, `getModuleId(position, module, title)` — lookup in `#__modules` by position, module, title
- `createMenuModule(title, position, menuType, showTitle, ordering, layout)` — `mod_menu` with params (menutype, startLevel, endLevel, showAllChildren, layout)
- `createSidebarMenuModule(title, position, menuType, showTitle, ordering)` — `mod_menu` with endLevel = 1 (top-level only), layout = "_:vertical"
- `createFinderModule(title, position, ordering)` — `mod_finder` with show_button = 0, show_label = 0
- `createLoginModule(title, position, ordering, showTitle)` — `mod_login` with greeting = 1, name = 0
- `createTagsModule(title, position, ordering, showTitle)` — `mod_tags_popular` with maximum = 10, order_value = "title", layout = "atomic:default"
- `createCustomModule(title, position, ordering, showTitle, html)` — `mod_custom` with prepare_content = 1; if exists, update content; else create
- `ensureBreadcrumbsModule(title, position, ordering)` — `mod_breadcrumbs` with showHere = 1, showHome = 1

**Cleanup / Finalization**
- `removeModulesInPositionExcept(position, keepTitles)` — delete all modules in a position except those in keepTitles list (e.g., keep "Atomic Breadcrumbs", remove others)
- `removeModulesInPositionsNotInList(allowedPositions, titlePrefixAllow)` — delete modules NOT in allowedPositions, but only if title starts with a prefix from titlePrefixAllow (e.g., "Atomic")
- `applyTemplateSettings()` — find Atomic template style (template = "atomic", client_id = 0), merge params JSON, set home = 1, unset home on others
- `fixFeaturedArticleOrdering()` — set explicit ordering in `#__content_frontpage` for featured articles
- `setHomeMenuPageHeading()` — set show_page_heading = 0 on home menu item (home = 1)

**Tag Management**
- `ensureSampleTags()` → creates or fetches 5 tags: bootstrap, typography, layout, themes, fonts; returns alias => id map
- `assignTagsToArticle(articleId, tagIds)` → ArticleModel save with tags key

**Utilities**
- `cleanBuffers(level)` — ob_end_clean() loop until ob_get_level() <= level (clean output to keep JSON clean)
- `unflagAllFeaturedArticles()` — set featured = 0 on all articles, delete all `#__content_frontpage` rows (start fresh)

### Parameters & Config

**DI Injection** (services/provider.php):
- `DispatcherInterface` — event dispatcher (passed to CMSPlugin constructor)
- `DatabaseInterface` — injected via `setDatabase()` trait
- `$app` — Joomla application, injected via `setApplication()`
- Plugin params: `(array) PluginHelper::getPlugin('sampledata', 'atomic')`

**Database Tables Modified** (all persist to `#__` prefixed):
- `#__extensions` — mark plugin enabled (script.php)
- `#__categories` — create "atomic" category (extension = "com_content")
- `#__content` — create 4 articles (welcome, getting-started, explore-atomic-features, style-guide)
- `#__content_frontpage` — mark welcome, getting-started, explore-atomic-features as featured with ordering
- `#__menu_types` — create "mainmenu-atomic" if mainmenu doesn't exist
- `#__menu` — create 4 top-level items + 3 child items
- `#__modules` — create ~22 modules across positions (menu, finder, breadcrumbs, custom, login, tags, etc.)
- `#__modules_menu` — assign modules to menu items (module assignments)
- `#__tags` — create 5 sample tags
- `#__contentitem_tag_map` — map tags to articles
- `#__template_styles` — update Atomic style params (headercolumns, footercolumns, bsthemes, sitetitle, logo, copyright), set home = 1

**Template Params Set by Step 6** (in `#__template_styles` JSON params):
- `headercolumns` = "6-6" (two equal columns in header, see template index.php layout logic)
- `footercolumns` = "4-4-4" (three equal columns in footer)
- `bsthemes` = "1" (Bootstrap source: 1 = Joomla 4, see templateDetails.xml field values)
- `sitetitle` = "Atomic Template for Joomla"
- `sitedescription` = "Powerful. Flexible. SEO friendly."
- `logo` = "media/templates/site/atomic/images/logo.png"
- `copyright` = "1" (enable copyright display)

**Language Keys** (en-GB):
- `PLG_SAMPLEDATA_ATOMIC_OVERVIEW_TITLE`, `_DESC` — wizard display
- `PLG_SAMPLEDATA_ATOMIC_STEP1_SUCCESS` through `_STEP6_SUCCESS` — step completion messages
- `PLG_SAMPLEDATA_ATOMIC_STEP_FAILED` — error format string with step # and exception message
- `PLG_SAMPLEDATA_ATOMIC_ALREADY_INSTALLED` — skip on re-run
- `PLG_SAMPLEDATA_ATOMIC_ERROR_NO_CONTENT`, `_ERROR_NO_MENUS`, `_ERROR_NO_MODULES`, `_ERROR_STEP6_REQUIREMENTS` — component enablement checks

### Control / Data Flow

**Joomla Sample Data Lifecycle**:
1. Admin navigates to **System > Sample Data**
2. Joomla discovers all `sampledata` plugins, calls each `onSampledataGetOverview()` → displays tiles with step counts
3. Admin clicks Atomic tile → Joomla AJAX-loops `onAjaxSampledataApplyStep1()` → `onAjaxSampledataApplyStep2()` etc., displays progress
4. Each step returns JSON success/message; UI advances to next step on success
5. After step 6, sample data is complete; home page displays hero module + featured articles

**Per-Step Request Flow**:
```
HTTP POST /index.php?option=com_ajax&... (type=atomic&step=N)
  ↓
Joomla plugin event dispatcher triggers onAjaxSampledataApplyStepN()
  ↓
Atomic::onAjaxSampledataApplyStepN():
  - ob_start() (suppress output)
  - Validate app, input type = "atomic"
  - Check component enablement (com_content, com_menus, com_modules)
  - Execute database & MVC operations (insert/update/delete via models)
  - ob_end_clean() (clean buffered output)
  - return ['success' => true/false, 'message' => string]
  ↓
JSON response to UI
```

**Article Model Creation Flow**:
```
$articleModel = $app->bootComponent('com_content')->getMVCFactory()
                    ->createModel('Article', 'Administrator', ['ignore_request' => true]);
$data = [id, title, alias, catid, state, access, introtext, fulltext, featured, ...];
$articleModel->save($data);
$newId = (int) $articleModel->getState('article.id');
```

Similar pattern for categories, menu items, modules, tags (each component has its own MVC factory).

### Cross-References

**Dependencies**:
- Atomic Template (`tpl_atomic/`) — plugin assumes template is installed and available for style params
- `com_content` — article creation (must be enabled for steps 1, 2, 6)
- `com_menus` — menu creation (must be enabled for steps 3, 4, 6)
- `com_modules` — module creation (must be enabled for steps 4, 5, 6)
- `com_categories` — category creation (used in step 1)
- `com_tags` — tag creation (used in step 2)

**Used by**:
- Joomla admin UI: **System > Sample Data** page (triggers on-demand)
- Fresh installations: sample data is optional but recommended for new Atomic users
- pkg_atomic (package manifest) includes the plugin for distribution

**Collaborates with Template**:
- Template positions referenced: alert, hero, topmenu, header, header-center, header-right, breadcrumbs, abovebody, leftbody, rightbody, belowbody, main-top, main-bottom, footer, footer-center, footer-right, mobilemenu, error-403, error-404, debug
- Template layout logic in `tpl_atomic/index.php` renders these positions; sample data populates them

### Gotchas / Conventions

**Joomla Version Compatibility**:
- Targets Joomla 5.4+ (uses PSR-4 namespaces, DI service provider, modern MVC factories)
- Plugin group must be exactly "sampledata" (Joomla core searches this group for sample-data plugins)
- AJAX events only fire in Joomla 5.4+ (older versions don't have this pattern)

**Language String Prefix**:
- All strings use prefix `PLG_SAMPLEDATA_ATOMIC_` (Joomla convention: PLG_<PLUGIN_GROUP>_<ELEMENT>_)
- Two INI files: `.ini` (runtime) and `.sys.ini` (system / installer)

**Output Buffering**:
- Each step wraps operations in ob_start() → cleanBuffers() to prevent extensions' debug output from corrupting JSON response
- Critical for AJAX reliability; if an extension emits a notice/warning, JSON would become invalid without this

**Featured Article Handling**:
- Step 1 unflags all existing featured articles (clears `#__content.featured = 1` + `#__content_frontpage`)
- Step 2 marks new articles as featured = 1
- Step 6 sets explicit ordering (1, 2, 3) in frontpage table to ensure Welcome is first
- If site already has featured articles, Atomic content takes priority (by design)

**Menu Type Strategy**:
- Prefers existing "mainmenu" (most common)
- Falls back to creating "mainmenu-atomic" if mainmenu not found
- Allows multiple runs without duplicating menu types (idempotent)

**Module Title Matching**:
- Module existence checked by (position, module, title) tuple, not ID
- All sample modules start with "Atomic " prefix (e.g., "Atomic Top Menu", "Atomic Hero")
- Cleanup in step 6 removes modules by prefix to avoid conflicts with other sample-data plugins

**Parameter Types**:
- Module params, menu item params, template params stored as JSON in `#__modules.params`, `#__menu.params`, `#__template_styles.params`
- Plugin uses `json_decode(..., true)` to read, `json_encode()` to write
- All params are case-sensitive keys (e.g., "menutype", "startLevel", "layout")

**Access Level**:
- All created content uses `(int) $app->get('access', 1)` (default public access)
- Can be overridden per installation via Joomla global config

**Image / Logo Reference**:
- Sets `logo` param to "media/templates/site/atomic/images/logo.png"
- File is not auto-created by plugin; template installer should provide it or set null

### Where to Look to Fix X

- **Change sample article content** → Edit article creation methods: `createWelcomeArticle()`, `createGettingStartedArticle()`, `createFeaturesArticle()`, `createStyleGuideArticle()` in Atomic.php (lines ~1057–1659). Search for `introtext =` or `fulltext =` to locate HTML.
- **Add/remove a module position demo** → Step 5 method `onAjaxSampledataApplyStep5()` (lines ~428–580). Add/remove a `createCustomModule()` or other module creation call with the position name and HTML content.
- **Modify template settings applied by sample data** → `applyTemplateSettings()` method (lines ~660–732). Update the `$sampleSettings` array with new param keys/values.
- **Change featured article ordering or home menu behavior** → `fixFeaturedArticleOrdering()` (lines ~737–774) and `setHomeMenuPageHeading()` (lines ~779–812).
- **Add/remove sample tags** → `ensureSampleTags()` method (lines ~2385–2440). Modify the `$tags` array (alias => title).
- **Debug AJAX step failures** → Check component enablement first (com_content, com_menus, com_modules). Then examine step method's error checks and database queries. Enable Joomla system logging to see exceptions.
- **Prevent sample data re-installation** → `alreadyInstalled()` method (lines ~848–861) checks for "welcome-to-atomic" article. Change alias to match if article name changes.

---

## Packaging, Build, Updates & Docs (pkg_atomic, build_package.sh, docs/)

**Purpose:** Manages distribution packages, automates builds of three installable ZIP files (full package, standalone template, sample data), maintains Joomla update-server XML feeds (stable + beta channel), documents module positions for Cassiopeia migration, and provides hardened server utilities (.htaccess, robots.txt).

### Key files

- `pkg_atomic/pkg_atomic.xml` — Package manifest (type="package") bundling template + sample-data plugin; declares install order and update server URL.
- `build_package.sh` — Bash build script; produces three ZIPs in `ZIP/` folder, auto-extracts versions from source XMLs, generates SHA-256 checksums, updates feed XMLs.
- `docs/update.xml` — Joomla update-server feed (stable channel); declares version 5.3.0, SHA-256 checksum, and download URL for pkg_atomic and tpl_atomic.
- `docs/update-beta.xml` — Beta channel feed; same structure as update.xml, activated when `betachannel` param = 1 in template settings.
- `docs/atomic-positions.html` — Interactive module-position map; visualizes 21 Atomic positions, Cassiopeia compatibility mappings (when `casspositions` = 1), and migration checklist with JS toggle for compatibility mode.
- `tpl_atomic/utilities/htaccess.txt` — Hardened .htaccess rules (5 sections: security headers CSP/HSTS, block exploit/recon paths, block bad bots by UA, compression, browser caching); append to Joomla root .htaccess after confirming no conflicts.
- `tpl_atomic/utilities/robots.txt` — robots.txt template blocking Joomla internal dirs (`/administrator/`, `/cache/`, `/components/`, etc.), AI/LLM crawlers (GPTBot, ClaudeBot, etc.), aggressive SEO bots (AhrefsBot, SemrushBot), and scanners; must move to site root if Joomla is in subfolder.
- `LICENSE` — GNU General Public License v2 or later.
- `.gitignore` — Ignores `.DS_Store` only.
- `README.md` — Installation, feature overview, module-positions table, build instructions, Joomla 4/5/6 requirements.

### Key logic / structures

**Package manifest (`pkg_atomic.xml`)**

- `<extension type="package" method="upgrade">` — Container type for bundling multiple extensions.
- `<version>5.3.0</version>` — Version synced across all three components (tpl_atomic, plg_sampledata_atomic, pkg_atomic).
- `<files folder="packages">` — References two child ZIPs to include in the package:
  - `<file type="template" client="site" id="atomic">tpl_atomic.zip</file>` — Installs to `templates/atomic/`.
  - `<file type="plugin" group="sampledata" id="atomic">plg_sampledata_atomic.zip</file>` — Installs to `plugins/sampledata/atomic/`.
- `<blockChildUninstall>true</blockChildUninstall>` — Prevents uninstalling template/plugin individually; must uninstall package to remove both.
- `<updateservers>` — Single stable update server: `https://kontent.github.io/Atomic/update.xml`. Update system checks this URL for new versions.

**Build script (`build_package.sh`)**

Process:
1. Extract versions from source XML files using `sed` (one-liner grep):
   - TPL_VERSION from `tpl_atomic/templateDetails.xml`
   - PLG_VERSION from `plg_sampledata_atomic/atomic.xml`
   - PKG_VERSION from `pkg_atomic/pkg_atomic.xml`
2. Clean `.DS_Store` files from all source trees.
3. Create `ZIP/` output directory and temp workspace.
4. Build three ZIPs in sequence:
   - **Step 1:** `tpl_atomic_${TPL_VERSION}.zip` — Standalone template (all files under `tpl_atomic/`, excludes `.DS_Store`, `__MACOSX/`, `.git/`).
   - **Step 2:** `plg_sampledata_atomic_${PLG_VERSION}.zip` — Standalone sample-data plugin.
   - **Step 3:** Assemble `pkg_atomic_${PKG_VERSION}.zip` in temp dir: copy manifest + both ZIPs into `packages/` folder, then zip the whole tree.
5. Calculate SHA-256 checksums for template and package ZIPs.
6. Update both `docs/update.xml` and `docs/update-beta.xml` using embedded Python script (regex-based):
   - Parse each `<update>` block, extract `<element>` value (`atomic` = template, `pkg_atomic` = package).
   - Insert or replace `<sha256>` tag with corresponding checksum.
7. Print summary and exit.

**Update server feeds (`update.xml`, `update-beta.xml`)**

Both use Joomla's update-server XML format:
- `<update>` block declares single version.
- `<element>atomic</element>` — Template identifier (matches `id` in templateDetails.xml).
- `<type>template</type>`, `<client>0</client>` — Type and client ID (0 = site).
- `<version>5.3.0</version>` — Current version.
- `<sha256>...</sha256>` — Auto-updated by build script; Joomla verifies checksum before installing.
- `<downloads>` → `<downloadurl>` — Full-package ZIP on GitHub releases: `https://github.com/Kontent/Atomic/releases/download/v5.3/pkg_atomic_5.3.0.zip`.
- `<targetplatform name="joomla" version="(4|5|6)\.">` — Regex pattern; applies to J4.x, J5.x, J6.x.
- **Beta channel activation:** When `betachannel` param = 1 in template settings, Joomla switches update source from update.xml to update-beta.xml. Both currently show same version/URL; beta feed typically hosts pre-release versions.

**Module positions (documented in `atomic-positions.html`)**

21 total positions organized by section:
- **Alert/Header:** {alert}, {header}, {header-center}, {header-right}, {topmenu}, {mobilemenu}
- **Main content:** {hero}, {breadcrumbs}, {abovebody}, {main-top}, [component], {main-bottom}, {belowbody}, {leftbody}, {rightbody}
- **Footer:** {footer}, {footer-center}, {footer-right}
- **Error pages:** {error-403}, {error-404}
- **Debug:** {debug}

Cassiopeia compatibility layer (enabled via `casspositions` param = 1):
- Adds 9 additional positions that render alongside or after Atomic-native positions:
  - {topbar}, {below-top} — Inside header.
  - {menu} — Inside {topmenu} nav bar (maps Cassiopeia {menu} modules).
  - {banner}, {top-a}, {top-b} — After {hero}.
  - {sidebar-left}, {sidebar-right} — Same columns as {leftbody}, {rightbody}.
  - {main-top}, {main-bottom} — Center column (different from Atomic's {abovebody}/{belowbody}).
  - {bottom-a}, {bottom-b} — Full-width rows below body.

The HTML map includes a JavaScript toggle to show/hide Cassiopeia positions, a mapping table, and a migration checklist.

### Control/data flow

**Install flow:**
1. User downloads `pkg_atomic_5.3.0.zip` from GitHub releases.
2. In Joomla admin, **System > Install > Extensions**, upload package.
3. Joomla installer extracts pkg_atomic.xml, detects two child ZIPs in `packages/` folder.
4. Installs template first (type=template), then plugin (type=plugin, group=sampledata).
5. Calls `tpl_atomic/install.php` post-install script (auto-configures Bootstrap source per Joomla version).
6. Package marked as installed; both components locked together by `<blockChildUninstall>true</blockChildUninstall>`.

**Update flow:**
1. Joomla update check queries update server (update.xml or update-beta.xml based on `betachannel` setting).
2. Compares remote version (5.3.0) against installed version; if newer, shows update notification.
3. User clicks **Update**; Joomla downloads ZIP, verifies SHA-256, extracts, and replaces files.
4. Template settings and site content remain unchanged; only extension files updated.

**Build flow (developer):**
1. Edit template/plugin source files.
2. Run `bash build_package.sh` from repo root.
3. Script extracts versions, zips all three artifacts, calculates checksums, updates feed XMLs.
4. Outputs ready-to-install ZIPs in `ZIP/` folder.
5. Developer tags release in Git, uploads ZIPs to GitHub releases, and update.xml feed already points to that release URL.

### Parameters & config

**Template settings** (`templateDetails.xml`, fieldset JOOMLA_SETTINGS):

- `killgenerator` (integer, 0/1) — Suppress `<meta name="generator">` tag; default 0 = include.
- `casspositions` (integer, 0/1) — Enable Cassiopeia-compatible positions; default 0. When 1, all 9 Cassiopeia positions render in index.php.
- `feediting` (integer, 0/1) — Allow frontend editing; default 0. When 1, shows edit buttons on articles/modules for authorized users.
- `betachannel` (integer, 0/1) — Subscribe to beta update feed; default 0. When 1, update server URL switches to docs/update-beta.xml (custom field type).

**Package metadata** (`pkg_atomic.xml`):

- `<packagename>atomic</packagename>` — Unique identifier in Joomla extensions table.
- `<version>5.3.0</version>` — Current release version; must match template and plugin versions.
- `<author>Ron Severdia</author>` — Author name.
- `<license>GNU General Public License version 2 or later</license>` — GPLv2+.

**Version mapping:**

- Template: `tpl_atomic/templateDetails.xml` line 4
- Plugin: `plg_sampledata_atomic/atomic.xml` line 8
- Package: `pkg_atomic/pkg_atomic.xml` line 5

All three **must** use the same version number (5.3.0 as of 2026-03-07). Build script extracts from each manifest; if they differ, builds succeed but inconsistency can cause update confusion.

### Cross-references

- **tpl_atomic/install.php** — Post-install script; auto-configures `bootstrapsource` param based on Joomla version (J4 → 1, J5 → 3, J6 → 4).
- **tpl_atomic/templateDetails.xml** — Full template manifest; declares 21 positions (lines 33–65), CSS/JS settings (100+ lines), update servers (not shown in snippet; likely appended below line 100).
- **plg_sampledata_atomic/src/** — Sample-data plugin code; creates demo menu, articles, modules.
- **tpl_atomic/utilities/htaccess.txt** — Companion to update mechanism; hardens site against scanner probes that might interfere with update checks.
- **tpl_atomic/utilities/robots.txt** — Discourages AI bots from crawling; doesn't affect Joomla's update server (which uses direct HTTP POST/GET, not bot crawling).
- **README.md** — Installation section references `pkg_atomic_VERSION.zip`; build script produces this file.

### Gotchas / conventions

**Version synchronization:** All three XML files (template, plugin, package) must declare the same version number. Build script does not validate this; a mismatch causes update system confusion (template updates to 5.3.0, plugin stays at 0.8.0, package shows as misaligned).

**SHA-256 auto-update:** Build script modifies `docs/update.xml` and `docs/update-beta.xml` in place using Python regex. If feed files are manually edited or checksums diverge, update system may reject ZIPs. Always regenerate checksums after rebuilding ZIPs.

**Cassiopeia compatibility toggle:** `casspositions` param is a template setting, not a build-time flag. Both Atomic-native and Cassiopeia positions exist in templateDetails.xml (lines 33–65) regardless of setting; the template's index.php conditionally renders Cassiopeia positions only when param = 1. Switching the toggle does not require rebuild.

**Beta channel:** `betachannel` is a custom field type (type="betachannel" in templateDetails.xml line 89). This field maps to a custom form field class in `tpl_atomic/html/fields/betachannel.php` (inferred; verify file exists). When enabled, Joomla reads update URLs from Joomla's Extension Update configuration, switching the source URL.

**Joomla version detection:** install.php (post-install script) uses Joomla's version constant to auto-set Bootstrap source. Fails silently if version constant missing; defaults to a sensible value. Test on all three supported versions (J4, J5, J6) before release.

**Update server URL:** Points to `https://kontent.github.io/Atomic/update.xml` (GitHub Pages). If this domain goes down or feed format breaks, update checks fail silently; users won't be notified of new versions. Monitor feed validity.

**Namespacing (sample-data plugin):** `<namespace path="src">Kontent\Plugin\SampleData\Atomic</namespace>` (line 13 of atomic.xml). Plugin classes must use PSR-4 namespace; if namespace mismatch, plugin fails to load. (Not a packaging issue, but affects plugin distribution.)

**Minified CSS not auto-generated:** Build script does not minify CSS; `.min.css` files must be pre-built manually or via a separate minification step (not part of build_package.sh). If source CSS is edited, minified versions may become stale. CLAUDE.md notes a Python one-liner for CSS minification, but it's manual — not automated in build_package.sh.

### Where to look to fix X

- **Add a new version / release:** Update `<version>` in all three XMLs (templateDetails.xml, atomic.xml, pkg_atomic.xml). Run `bash build_package.sh`. Test update check in Joomla admin **Extensions > Manage > Find Updates**.

- **Change update server URL:** Edit `<updateservers>` block in `pkg_atomic.xml` line 19 (currently `https://kontent.github.io/Atomic/update.xml`). Also verify `docs/update.xml` and `docs/update-beta.xml` are accessible at the new domain.

- **Activate beta channel:** Users enable `betachannel` param in template settings UI (maps to templateDetails.xml line 89, custom field type). Verify custom field class exists at `tpl_atomic/html/fields/betachannel.php` and correctly switches update source.

- **Fix build errors / missing ZIPs:** Check `ZIP/` folder permissions (must be writable). Verify all source XMLs have well-formed `<version>` tags (build script uses regex; malformed tags break extraction). Run `bash build_package.sh` with `set -x` (bash debug mode) to trace sed/Python steps.

- **Update SHA-256 after manual rebuild:** Rerun `bash build_package.sh` in full; embedded Python re-calculates checksums. Do not manually edit SHA-256 tags in feed XMLs — they will be overwritten on next build.

- **Migrate Cassiopeia sites to Atomic:** Enable `casspositions` = 1 in Atomic template settings. Sites continue to work (existing Cassiopeia module assignments render in Cassiopeia-mapped positions). Reference `docs/atomic-positions.html` interactive map for position equivalence. Gradually reassign modules to Atomic-native positions, then disable `casspositions`.

- **Harden deployed site:** Copy `tpl_atomic/utilities/htaccess.txt` content to site's `.htaccess` (or Apache config). Uncomment HSTS / HTTPS enforcement rules after confirming TLS cert covers all domains. Test with curl to verify headers. Move `tpl_atomic/utilities/robots.txt` to site root if Joomla is not in site root subfolder; update Disallow paths accordingly.

---

## Joomla Reset Utility Component (com_joomlareset)

**Version:** 1.1.0 | **Joomla support:** 5, 6 | **PHP:** 8.1+ | **Status:** Admin utility — DESTRUCTIVE

### Purpose

com_joomlareset is a separate admin-only component (not part of the template) that restores a Joomla installation to a fresh post-install state by dropping all database tables and recreating core tables from bundled SQL. It preserves only the primary Super Admin user account (with password intact) and re-registers itself so it survives the reset. **This is a nuclear option for dev/testing**: all content, users (except primary admin), menus, modules, custom fields, component settings, and media database references are destroyed. Files on disk (templates, images, media/) remain untouched.

### Key Files

- `utilities/com_joomlareset/joomlareset.xml` — Component manifest; declares namespace, MVC provider, admin menu icon (warning), version 1.1.0
- `utilities/com_joomlareset/services/provider.php` — DI container; registers MVCFactory and ComponentDispatcherFactory for `Severdia\Component\JoomlaReset` namespace
- `utilities/com_joomlareset/src/Dispatcher/Dispatcher.php` — Access control layer; enforces Super Admin only (checks `core.admin` permission via Factory getIdentity), throws NotAllowed(403)
- `utilities/com_joomlareset/src/Controller/DisplayController.php` — Default view dispatcher; routes to `reset` view
- `utilities/com_joomlareset/src/Controller/ResetController.php` — POST handler; validates CSRF token, checks authorization, checks confirmation flag (POST `confirm_reset`), delegates to ResetModel::resetDatabase(), handles redirect/messages
- `utilities/com_joomlareset/src/Model/ResetModel.php` — Core reset logic; 7-phase orchestration (preserve admin, drop tables, recreate from SQL, populate #__schemas, clear checked_out, restore admin, re-register component)
- `utilities/com_joomlareset/src/View/Reset/HtmlView.php` — Admin view; calls model->getResetInfo(), passes to template, sets toolbar title with warning icon
- `utilities/com_joomlareset/tmpl/reset/default.php` — Bootstrap-5 admin UI; displays reset info table (version, table count, admin user), "will destroy" and "will preserve" alerts, checkbox confirmation + disabled reset button, JavaScript client-side toggle, final confirm() dialog, CSRF token via HTMLHelper form.token()
- `utilities/com_joomlareset/script.php` — Installer hook (Com_JoomlaresetInstallerScript); runs preflight() on install/update to repair menu nested-set tree before Joomla adds the menu item
- `utilities/com_joomlareset/sql/j5/base.sql` — Joomla 5 core tables (97 KB): assets, extensions, users, user_usergroup_map, menu, content, categories, modules, etc.
- `utilities/com_joomlareset/sql/j5/supports.sql` — Joomla 5 support/utility tables (47 KB): workflow, banners, newsfeeds, redirect, postinstall_messages, etc.
- `utilities/com_joomlareset/sql/j5/extensions.sql` — Joomla 5 extension-related tables (72 KB): languages, fields, custom fields, tags, contact_details, etc.
- `utilities/com_joomlareset/sql/j6/base.sql` — Joomla 6 equivalents (99 KB); same structure as J5, version-specific schema
- `utilities/com_joomlareset/sql/j6/supports.sql` — Joomla 6 support tables (48 KB)
- `utilities/com_joomlareset/sql/j6/extensions.sql` — Joomla 6 extension tables (72 KB)
- `utilities/com_joomlareset/language/en-GB/com_joomlareset.ini` — Admin UI strings (36 keys); prefixed `COM_JOOMLARESET_*` (HEADING, WARNING_TEXT, BUTTON, FINAL_CONFIRM, ERROR_NOT_CONFIRMED, SUCCESS, ERROR_FAILED, STEP_1–5, DESTROY_*, PRESERVE_*, UNSUPPORTED_*)
- `utilities/com_joomlareset/language/en-GB/com_joomlareset.sys.ini` — System strings (3 keys); used during install/uninstall

### Architecture & Data Flow

#### MVC Structure
- **Service Provider** (`services/provider.php`): Registers MVCFactory and ComponentDispatcherFactory via Joomla DI container.
- **Dispatcher** (`src/Dispatcher/Dispatcher.php`): Custom dispatcher that overrides `checkAccess()` to enforce Super Admin (`core.admin`). Throws NotAllowed(403) if user lacks permission.
- **Controllers**: 
  - `DisplayController`: Minimal default view router (target: `reset`)
  - `ResetController`: Handles `task=reset.execute` POST; validates CSRF, authorization, and confirmation checkbox before calling model
- **Model** (`src/Model/ResetModel.php`): Inherits from `BaseDatabaseModel`; performs all reset logic
- **View** (`src/View/Reset/HtmlView.php`): Displays form and info; calls `model->getResetInfo()` to populate template

#### Request Flow

1. **Initial page load** (`?option=com_joomlareset`):
   - Dispatcher checks `core.admin`; throws 403 if denied
   - DisplayController routes to `reset` view
   - HtmlView calls `model->getResetInfo()` (retrieves Joomla major version, first Super User, table count, supported flag)
   - Template renders info table + destruction/preservation alerts + unchecked confirmation checkbox (button disabled) + form
   - Client-side JS (DOMContentLoaded) attaches change listener to checkbox → enables button when checked
   - Form has inline CSRF token via `HTMLHelper::_('form.token')`

2. **Form submission** (POST to `?option=com_joomlareset&task=reset.execute`):
   - ResetController::execute() validates:
     - CSRF token (Session::checkToken('post'))
     - User is Super Admin
     - POST `confirm_reset == 1`
   - Browser shows final `confirm()` dialog (JS, custom message via language key)
   - If confirmed, calls `model->resetDatabase()`
   - Model returns success/error; controller redirects with message

#### 7-Phase Reset Logic (ResetModel::resetDatabase())

1. **Preserve admin user**: Queries `#__users` + `#__user_usergroup_map` to find first Super User (group_id = 8); clones object + stores group IDs
2. **Drop all tables**: Gets all tables matching db prefix via `SHOW TABLES LIKE 'prefix_%'`; disables foreign keys, drops each table with `DROP TABLE IF EXISTS`, re-enables foreign keys
3. **Recreate core tables**: Loads and executes bundled SQL for version (j5/ or j6/): base.sql, supports.sql, extensions.sql in order; replaces `#__` placeholder with actual prefix
4. **Populate #__schemas**: Scans disk for migration dirs (glob patterns: `*/sql/updates/mysql/` in components/, plugins/, modules/); extracts latest migration version per extension; inserts into `#__schemas` so Joomla's "Database → Fix" tool won't replay migrations
5. **Clear checked_out flags**: UPDATEs 9 core tables (menu, content, categories, modules, contact_details, newsfeeds, banners, fields, tags) setting checked_out and checked_out_time to NULL (ignores errors if table missing)
6. **Restore admin user**: Re-INSERTs admin user record with original id/password/params; re-maps to original groups via `#__user_usergroup_map`; UPDATEs default category created_user_id (replaces placeholder 42); INSERTs asset entry for user
7. **Re-register component**: INSERTs `#__extensions` row (com_joomlareset, enabled, client_id 1); deletes PSR-4 autoload cache file (`/cache/autoload_psr4.php`) so Joomla regenerates it; INSERTs menu item to admin menu using nested-set arithmetic (calculates lft/rgt, expands root node)

#### Key Model Methods

- `getResetInfo()` → array: Retrieves UI display data (Joomla version, major version, first Super User, table count, supported flag)
- `resetDatabase()` → array: Executes all 7 phases; returns `['success' => bool, 'error' => string]`
- `getFirstSuperUser()` → ?object: Queries group_id = 8; returns cloned user object or null
- `getUserGroupMap(userId)` → array: Returns group IDs for user (defaults to [8] if none found)
- `getJoomlaTables(prefix)` → array: SHOW TABLES matching prefix
- `executeSqlFile(filePath, prefix)` → void: Reads file, replaces #__ with prefix, splits into statements, executes each (disables FK checks)
- `populateSchemas()` → void: Scans migration dirs on disk; inserts extension_id + latest version into #__schemas
- `findMigrationDirs()` → array: Globs 5 locations (admin components, site components, plugins, admin modules, site modules); returns [type, element, folder, client_id, dir]
- `getLatestMigrationVersion(dir)` → ?string: Finds highest version_compare of *.sql files in dir
- `clearCheckedOut()` → void: UPDATEs 9 core tables
- `restoreAdminUser(admin, groupIds)` → void: INSERTs user + group mappings + asset entry; UPDATEs default category
- `registerSelf(prefix)` → void: INSERTs #__extensions row + menu item; deletes cache file
- `splitSql(sql)` → array: Parses SQL dump handling quoted strings, escapes, comments (both `--` and `/* */`), returns statements

### Safety & Access Control

1. **Dispatcher-level**: Enforces Super Admin (`core.admin`) — throws NotAllowed(403) on access
2. **Controller-level**:
   - CSRF token validation (Session::checkToken('post'))
   - Authorization re-check (user->authorise('core.admin'))
   - Confirmation flag validation (POST `confirm_reset` must be 1)
3. **Client-level**:
   - Checkbox must be checked to enable button (JS toggle)
   - Final JS confirm() dialog ("FINAL WARNING: This will destroy ALL data...")
4. **Manifest**: Admin menu icon is "warning" class (visual cue)

### Admin UI (tmpl/reset/default.php)

- **Alert blocks**: Unsupported version (red alert if not J5/J6); destruction list (articles, categories, menus, modules, users, media refs, config); preservation list (admin user + password, this component, files)
- **Info table**: 4 rows — Joomla version (badge bg-info), SQL set (Joomla 5 or 6), admin user + email, table count + destroyed count
- **Form**: 
  - Bootstrap form-check with checkbox (id="confirm_reset", name="confirm_reset", value="1", checked=unchecked by default)
  - Label: dangerous text in text-danger
  - Submit button (id="reset-button", class="btn btn-danger btn-lg", disabled by default)
  - Inline CSRF token (form.token)
  - Action: POST to `index.php?option=com_joomlareset&task=reset.execute`
- **Layout**: Two-column grid (col-lg-8 + col-lg-4); main card on left (bg-danger header), info card on right (6-step breakdown)
- **JavaScript** (vanilla, inline):
  - DOMContentLoaded listener: attaches change event to checkbox; sets button disabled = !checkbox.checked
  - Form submit listener: shows `confirm()` dialog; prevents submission if user cancels

### Database Behavior

- **Prefix detection**: `Factory::getApplication()->get('dbprefix', 'jos_')` — respects custom prefix
- **Foreign key checks**: Disabled during drop/recreate to avoid cascade conflicts
- **SQL dialect**: MySQL/MariaDB; uses InnoDB, utf8mb4 collation
- **Tables recreated**: ~80+ core Joomla tables from bundled SQL (base, supports, extensions sets)
- **User IDs**: Preserved admin user retains original ID; auto-increment seed varies per SQL file
- **Asset tree**: Root asset rebuilt; new user asset inserted; category assets regenerated with preserved admin ID
- **Nested set**: Menu tree lft/rgt values rebuilt during re-registration (script.php calls Table::rebuild() in preflight)

### Joomla Version Differences (J5 vs J6)

- **Supported versions**: Only 5 and 6 (checked at runtime via Version::MAJOR_VERSION)
- **SQL sets**: Separate j5/ and j6/ directories; loaded based on major version
- **Schema differences**: J6 has schema updates vs J5; both use utf8mb4
- **Autoload cache**: J5 and J6 both cache PSR-4 in `/cache/autoload_psr4.php`
- **Menu nested-set**: Rebuilt via Joomla's native Table::rebuild() in all versions

### Language Keys (COM_JOOMLARESET_*)

All user-facing strings are translatable; key prefixes:
- `HEADING`, `WARNING_TEXT` — Main heading and alert text
- `JOOMLA_VERSION`, `SQL_SET`, `ADMIN_USER`, `TABLE_COUNT` — Info table labels
- `TABLES_DROPPED` — "tables will be dropped and recreated"
- `WILL_DESTROY`, `DESTROY_ARTICLES`, `DESTROY_CATEGORIES`, `DESTROY_MENUS`, `DESTROY_MODULES`, `DESTROY_USERS`, `DESTROY_MEDIA_REFS`, `DESTROY_CONFIG` — Destruction list
- `WILL_PRESERVE`, `PRESERVE_ADMIN`, `PRESERVE_EXTENSION`, `PRESERVE_FILES` — Preservation list
- `CONFIRM_CHECKBOX` — Checkbox label
- `BUTTON` — "RESET DATABASE"
- `FINAL_CONFIRM` — JavaScript confirm dialog text
- `ERROR_NOT_CONFIRMED`, `SUCCESS`, `ERROR_FAILED` — Messages after form submission
- `HOW_IT_WORKS`, `STEP_1` through `STEP_5` — Info sidebar
- `UNSUPPORTED_TITLE`, `UNSUPPORTED_DESC` — Unsupported version alert

### Gotchas & Conventions

1. **Super Admin only**: Dispatcher throws 403; no alternative auth mechanism. Trying to access as non-admin redirects with "JERROR_ALERTNOAUTHOR"
2. **Confirmation is mandatory**: Both checkbox (server-side) and confirm() dialog (client-side) must pass; UI button disabled until checkbox checked
3. **CSRF token must match**: Session::checkToken('post') in ResetController; HTMLHelper::_('form.token') rendered in template
4. **Namespace**: PSR-4 `Severdia\Component\JoomlaReset` → must match joomlareset.xml and files; cache file deletion (`autoload_psr4.php`) forces regeneration
5. **Menu tree repair**: script.php preflight() calls Table::rebuild() to ensure nested-set integrity before installer adds menu item; this is critical because reset drops and recreates #__menu
6. **First Super User only**: Model finds first Super User (group_id = 8, sorted by id ASC); if multiple admins exist, only the primary is preserved
7. **Group mappings**: All original groups for the admin user are preserved (query #__user_usergroup_map), not just Super Admin
8. **Placeholder ID 42**: Default category created_user_id is set to 42 during reset; must be updated to admin ID post-restore
9. **Migration scanning**: Globs 5 locations on disk; scans for version_compare-sortable filenames; misses extensions not following Joomla naming (rare)
10. **FK checks disabled**: Required because drop order may not respect foreign keys; re-enabled after recreate
11. **SQL parsing**: Custom splitSql() handles quoted strings, escapes, `--` and `/* */` comments; may fail on complex multi-line comments (edge case)
12. **No rollback**: Exception caught and returned as error; database state unknown if exception occurs mid-reset; backup advised
13. **Files on disk untouched**: Only database affected; orphaned extension files (uninstalled before reset) remain

### Where to Look to Fix X

- **Change what info displays on the form** → `src/Model/ResetModel.php::getResetInfo()` (returns array), then `tmpl/reset/default.php` (renders fields)
- **Modify reset phases or logic** → `src/Model/ResetModel.php::resetDatabase()` and its private phase methods (phases 1–7)
- **Add a new validation check (e.g., backup required)** → `src/Controller/ResetController.php::execute()` (add check before model call)
- **Block a specific Joomla version** → `src/Model/ResetModel.php::resetDatabase()` (modify version check line 54: `in_array((int) $major, [5, 6], true)`)
- **Change SQL files used per version** → `sql/j5/` and `sql/j6/` directories; line 86 reads `base.sql`, `supports.sql`, `extensions.sql` in order
- **Alter UI layout or styling** → `tmpl/reset/default.php` (Bootstrap-5 classes, form structure)
- **Modify checkbox behavior** → inline `<script>` at end of `tmpl/reset/default.php` (DOMContentLoaded listeners)
- **Change error/success messages** → `language/en-GB/com_joomlareset.ini` (language keys) or `src/Controller/ResetController.php` (redirect messages use Text::_() keys)
- **Add extra tables to clear checked_out** → `src/Model/ResetModel.php::clearCheckedOut()` (add table name to $tables array line 302)
- **Require additional access level beyond Super Admin** → `src/Dispatcher/Dispatcher.php::checkAccess()` (add new permission check)
- **Skip restore of a user column** → `src/Model/ResetModel.php::restoreAdminUser()` (modify $columns array or values at lines 455–482)
- **Change admin menu icon** → `joomlareset.xml` line 21: `<menu img="class:warning">` (change to different Joomla icon class)
- **Debug reset failure** → Catch block on line 112 returns error message; wrap individual phases in try-catch to pinpoint failure; check MySQL error log if FK issues occur

---

## 10. Master "Where to fix X" index

| I want to change… | Start here |
|---|---|
| Overall page HTML / `<head>` / meta | `tpl_atomic/index.php` |
| Component-only view (modals, print, embeds) | `tpl_atomic/component.php` (keep in sync with index.php asset logic) |
| 403 / 404 error pages | `tpl_atomic/error.php` (+ positions `error-403`/`error-404`) |
| Offline / maintenance page | `tpl_atomic/offline.php` |
| Add / rename a template setting | `tpl_atomic/templateDetails.xml` (field) + read it in `index.php`/`component.php` (+ `language/en-GB/tpl_atomic.ini` for the label) |
| Add / change a module position | `templateDetails.xml` `<position>` + render logic in `index.php` + update `docs/atomic-positions.html` |
| Bootstrap / Font Awesome / jQuery / font source | the integer-mapped params in `templateDetails.xml`; load logic in `index.php` head; custom field types in `html/fields/` |
| Google-font family/link mapping | `tpl_atomic/helper.php` |
| Web-asset registration / load order / weights | `tpl_atomic/joomla.asset.json` (+ `useStyle`/`useScript` calls in index.php) |
| Core layout / Joomla-Bootstrap fix / breakpoints / z-index | `tpl_atomic/media/css/atomic.css` (then regenerate `atomic.min.css`) |
| Colors / glassmorphism / gradients / design tokens | `tpl_atomic/media/css/atomicstyles.css` (CSS custom properties; regenerate `.min`) |
| Per-site custom CSS/JS | `tpl_atomic/css/template.css`, `tpl_atomic/js/template.js` (enable via settings) |
| Light/dark/auto theme behavior | inline head IIFE in `index.php` + `media/js/themeswitcher.min.js` + `html/layouts/header/styleswitcher.php` |
| Module wrapper markup (card/row/none…) | `tpl_atomic/html/layouts/chromes/*.php` |
| Menu markup (horizontal/tabs/vertical) | `tpl_atomic/html/mod_menu/*.php` |
| Mobile offcanvas menu | `html/layouts/chromes/mobilemenu.php` + `mobilemenupanel.php` (position `mobilemenu`) |
| Install-time defaults / Joomla-version detection | `tpl_atomic/install.php` |
| Favicons | `templateDetails.xml` `loadfavicons` + `media/favicons/` (+ index.php head) |
| Social / OpenGraph / Twitter cards | `socialtitle`/`socialthumb*` params + head logic in `index.php`; art in `source/preview/` |
| Demo content on new installs | `plg_sampledata_atomic/src/Extension/Atomic.php` |
| What ships / package order | `pkg_atomic/pkg_atomic.xml` + `build_package.sh` |
| Release / update feed / beta channel | `docs/update.xml`, `docs/update-beta.xml`, `betachannel` param, version bumps across all manifests |
| Reset a dev site to fresh install | `utilities/com_joomlareset/src/Model/ResetModel.php` (⚠️ destructive) |

## 11. Conventions & gotchas

- **Two files must stay in sync:** `index.php` and `component.php` both build the asset/head logic; changes to fonts/Bootstrap/theme handling usually belong in **both**.
- **Minified CSS is what ships.** After editing `atomic.css` / `atomicstyles.css`, regenerate `*.min.css` before building (the old CLAUDE.md includes a Python minifier snippet; no Node required).
- **All manifests + the two update feeds must be version-bumped together** for a release; the package is what users install.
- **Integer-mapped settings** (`bootstrapsource` 0–14, `fontawesome` 0–6, `headerfont`/`bodyfont` 0–13, `bodymenu` 0–6, `loadfavicons` 0–2) — always cross-check the map in §5 before changing load logic.
- **Language prefixes:** template strings use `TPL_ATOMIC_` (front-end `tpl_atomic.ini`, admin `tpl_atomic.sys.ini`); the reset component uses `COM_JOOMLARESET_`.
- **`media/` is shared and overwritten on update; `templates/atomic/css|js/` is per-site** — never put site-specific edits in `atomic.css`/`atomicstyles.css`.
- **Joomla version differences (J4/J5/J6):** Bootstrap-source and Font-Awesome params expose per-version "core" options; `install.php` picks a sensible default per detected version; `com_joomlareset` ships parallel `sql/j5/` and `sql/j6/` reinstall SQL selected at runtime.
- **`utilities/`** is the dev-utility directory (not part of the shipped template). Its two `README.md` files are duplicates.
- **`com_joomlareset` is DESTRUCTIVE** — it drops all tables matching the Joomla prefix and recreates only core tables, preserving only the primary Super Admin (Super-Admin-gated `Dispatcher`, CSRF + confirmation). It is a dev/maintenance tool, **not** part of the shipped template package. Uninstall third-party extensions first (their tables are lost, files orphaned).
- **Cassiopeia migration:** the 9 compat positions + `casspositions` param let a site swap from Joomla's default template without reassigning modules.

---

<sub>Generated by an automated multi-agent review (7 subsystem readers + completeness critic) cross-checked against direct reads of `templateDetails.xml` (params + all 30 positions), the version manifests, `docs/update*.xml`, and `source/`. It corrects the prior CLAUDE.md's position count (21 → 30) and adds the previously-undocumented `com_joomlareset` component, sample-data plugin internals, update-channel mechanism, and `source/` assets. Confirm any "(verify)" claim against the code before relying on it.</sub>

