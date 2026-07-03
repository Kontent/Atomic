# Joomla Reset

**Version 1.1.0** | Joomla 5 & 6

Joomla Reset restores your website back to its original state after a fresh installation. All database tables are dropped and recreated. Your admin account will be restored, but no other content will be saved.

## Requirements

- Joomla 5.x or 6.x
- PHP 8.1+
- MySQL / MariaDB
- Super Admin access

## Installation

1. Download `com_joomlareset.zip`
2. In the Joomla admin, go to **System > Install > Extensions**
3. Upload and install the zip package
4. Access via **Components > Joomla Reset**

## What It Does

The reset process runs in six phases:

1. **Saves** your primary Super Admin user account and password
2. **Drops** ALL database tables matching the Joomla prefix
3. **Recreates** tables using the default Joomla installation SQL for your version
4. **Clears** checked-out flags on core tables
5. **Restores** your admin user with original credentials and group mappings
6. **Re-registers** the Joomla Reset component so it survives the reset

### What Gets Destroyed

- All articles, categories, and tags
- All custom categories
- All menus and menu items (reset to defaults)
- All module configurations (reset to defaults)
- All users except the primary Super Admin
- All media references in the database (files on disk are untouched)
- All component settings, custom fields, workflows, and template styles

### What Gets Preserved

- The primary Super Admin user (with original password)
- This component
- All files on disk (templates, media, images)

## Safety Features

- Super Admin access required
- Confirmation checkbox must be checked before the reset button activates
- JavaScript confirm dialog as a final warning
- CSRF token protection
- Unsupported Joomla versions are blocked with a warning

## Important Notes

- **Uninstall all third-party extensions before running the reset.** The reset drops all tables and recreates only the core Joomla tables. Third-party extension tables will be lost, and their files will remain orphaned on disk.
- **You will be logged out after the reset.** Log back in with your original admin credentials.
- **This cannot be undone.** Back up your database first if you need to preserve anything.

## File Structure

```
com_joomlareset/
├── joomlareset.xml              # Component manifest
├── language/en-GB/              # Language strings
├── services/provider.php        # DI container registration
├── sql/j5/                      # Joomla 5 installation SQL
├── sql/j6/                      # Joomla 6 installation SQL
├── src/
│   ├── Controller/              # DisplayController, ResetController
│   ├── Dispatcher/              # Access control (Super Admin only)
│   ├── Model/ResetModel.php     # Core reset logic
│   └── View/Reset/HtmlView.php  # Admin view
└── tmpl/reset/default.php       # Admin UI template
```

## License

GNU General Public License version 2 or later

## Author

Ron Severdia
