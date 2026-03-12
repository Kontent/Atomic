# Atomic Template for Joomla

Atomic is a barebones, open-source Joomla template designed as a starting point for custom site builds. One template, many designs — users customize via CSS and template settings without modifying core files.

## Repository Structure

```
tpl_atomic/              Template (installs to templates/atomic/)
├── index.php            Main template — ~100 params, all layout logic
├── component.php        Component-only rendering (mirrors index.php settings)
├── error.php            Error pages (403/404 with module positions)
├── offline.php          Maintenance mode page
├── helper.php           Google Font utilities (getGoogleFontFamily, getGoogleFontLink, isGoogleFont)
├── install.php          Post-install script (auto-configures Bootstrap source per Joomla version)
├── templateDetails.xml  All settings, positions, update servers
├── html/
│   ├── fields/          Custom admin fields (bootstrapsource.php, fontawesome.php)
│   ├── layouts/chromes/ Module wrappers: default, card, column, row, none, mobilemenu
│   ├── layouts/header/  styleswitcher.php (theme toggle dropdown)
│   ├── mod_custom/      Custom module layouts (default, contentonly, modulesuffix)
│   ├── mod_menu/        Menu overrides (default, horizontal, horizontaltabs, vertical)
│   └── mod_tags_popular/ Tag module override with alias-based CSS classes
├── css/template.css     User-editable CSS (loaded when enabled in settings)
├── js/template.js       User-editable JS (loaded when enabled in settings)
├── media/               Shared assets (installs to media/templates/site/atomic/)
│   ├── css/atomic.css        Core layout & Joomla/Bootstrap fixes (always load this)
│   ├── css/atomicstyles.css  Decorative styles: glassmorphism, gradients, design tokens
│   ├── css/*.min.css         Minified versions (served to users)
│   ├── js/atomic.js          Optional template JS
│   └── js/themeswitcher.min.js  Light/dark/auto theme switcher
└── language/            en-GB translation strings

plg_sampledata_atomic/   Sample data plugin (demo content, modules, menus)
pkg_atomic/              Package manifest (bundles template + plugin)
build_package.sh         Build script — produces 3 ZIPs in ZIP/
```

## CSS Architecture (Two-Layer System)

**`atomic.css`** — Core layout. Handles Joomla/Bootstrap incompatibilities, header/footer structure, dropdown behavior, mobile menu, pagination, responsive breakpoints. Should always be enabled. Has no visual opinion.

**`atomicstyles.css`** — Decorative layer. Design tokens (CSS custom properties), glassmorphism, gradients, hero styling, glassmorphic navigation, sidebar cards, login form, tag badges. Can be disabled for plain Bootstrap.

**User customization goes in `css/template.css`** (loaded last when enabled). Never edit atomic.css or atomicstyles.css for site-specific changes.

### Key CSS Custom Properties (defined in atomicstyles.css)

```
--page-bg-start/mid/end     Page background gradient
--glass-bg/border/blur/radius   Glassmorphism
--accent-primary/secondary/tertiary/warm   Color palette
--accent-start/end           Gradient endpoints
--text-primary/secondary     Text colors
--card-shadow                Box shadow
--nav-hover-bg               Navigation hover state
--transition-speed           Animation duration
--atomic-header-font         Header font family (set via inline <style>)
--atomic-body-font           Body font family (set via inline <style>)
--atomic-header-background-color   Header background (set via inline <style>)
```

### CSS Loading Order

1. Bootstrap (source depends on setting: Joomla core, CDN, Bootswatch, or custom)
2. Google Fonts (if configured)
3. Inline `<style>` with CSS custom properties for fonts and header background
4. Font Awesome / Bootstrap Icons (if enabled)
5. `atomic.min.css` (if enabled — layout fixes)
6. `atomicstyles.min.css` (if enabled — decorative)
7. `template.css` (if enabled — user customizations)

### Z-Index Stack

```
1070  Joomla edit button tooltips
1050  Alert bar
1040  Header (and all its dropdowns)
1     Main content area, Footer
0     Body pseudo-elements (ambient orbs)
```

## Template Parameters

Settings are in `templateDetails.xml`. Major groups:

- **Joomla Settings** — Generator tag, Cassiopeia compat, front-end editing toggle, beta channel
- **CSS Settings** — Bootstrap source (0=None, 1=J4, 2=CDN, 3=J5, 4=J6, 5=Custom, 6-14=Bootswatch), atomic.css, atomicstyles.css, custom CSS, body menu alias, theme switcher, data-bs-theme, user group data attribute
- **JavaScript** — jQuery (off/Joomla/CDN/slim/custom), atomic.js, custom JS
- **Fonts & Icons** — Header/body font (system/Google/custom), Font Awesome (0=None, 1=J4, 2=FA7 CSS CDN, 3=FA7 JS CDN, 4-5=Custom, 6=J5/6), Bootstrap Icons, favicons
- **Layout** — Container (fixed/fluid), header/body/footer columns (e.g. "6-6", "4-4-4", "2-8-2"), logo, sticky header, header background color, copyright
- **Features** — Google Analytics, ScrollReveal, error page search
- **Metadata** — Social media thumbnails (Google, Facebook, Twitter)
- **Custom Code** — Four injection points (after/before head/body)

## Layout System

Header, body, and footer each support configurable column ratios as dash-separated strings (e.g. "6-6" = two equal columns, "4-4-4" = three columns, "12" = single column).

Body columns control sidebar visibility:
- `leftbody` position → left sidebar
- `rightbody` position → right sidebar
- Columns collapse when no modules are assigned

## Module Positions (21 total)

`alert` → `header` / `header-center` / `header-right` → `topmenu` → `hero` → `leftbody` | `breadcrumbs` → `abovebody` → `main-top` → [component] → `main-bottom` → `belowbody` | `rightbody` → `footer` / `footer-center` / `footer-right` → `debug`

Also: `mobilemenu` (offcanvas), `error-403`, `error-404`

## Build Process

```bash
bash build_package.sh
```

Produces in `ZIP/`:
- `pkg_atomic_VERSION.zip` — Full package (install this)
- `tpl_atomic_VERSION.zip` — Standalone template
- `plg_sampledata_atomic_VERSION.zip` — Standalone sample data

**After editing CSS**: regenerate minified files before building. No Node.js required — use Python:

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

## Common Tasks

**Add a new module position**: Add `<position>name</position>` to templateDetails.xml, then add the rendering logic in index.php at the appropriate location.

**Create a template override**: Place files in `tpl_atomic/html/com_COMPONENT/VIEW/` or `tpl_atomic/html/mod_MODULE/` following Joomla's override conventions.

**Add a new module chrome**: Create `tpl_atomic/html/layouts/chromes/NAME.php`. Use `style="NAME"` in module assignments.

**Customize for a specific site**: Enable custom CSS in settings, edit `tpl_atomic/css/template.css`. Override CSS custom properties from atomicstyles.css to change the entire design.

## Conventions

- Template language strings use prefix `TPL_ATOMIC_` in `language/en-GB/en-GB.tpl_atomic.ini`
- System language strings use prefix `TPL_ATOMIC_` in `language/en-GB/en-GB.tpl_atomic.sys.ini`
- Bootstrap source values and Font Awesome values are integer-mapped (see parameters section above)
- The `media/` folder is shared across all site instances; `templates/atomic/` (css/, js/) is per-site customizable
- Minified CSS files must be regenerated after any changes to source CSS
- All three ZIPs must be rebuilt after any template changes
