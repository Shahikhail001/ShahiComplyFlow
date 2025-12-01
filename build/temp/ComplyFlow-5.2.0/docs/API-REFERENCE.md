# ComplyFlow Developer API Reference

This reference summarizes the primary hooks, filters, REST routes, CLI commands, and utility classes available to developers extending ComplyFlow. Each entry lists the file location and suggested use cases. Review the source files for full context and the latest arguments.

> **Version**: 1.0.0 (November 2025)  
> **Text Domain**: `complyflow`

---

## 1. Core Lifecycle Hooks

| Hook | Type | Declared In | Description |
|------|------|-------------|-------------|
| `complyflow_activated` | Action | `includes/Core/Activator.php` | Fires after plugin activation. Use to seed custom data. |
| `complyflow_deactivated` | Action | `includes/Core/Deactivator.php` | Runs during deactivation before cleanup. |
| `complyflow_init` | Action | `includes/Core/Plugin.php` | Earliest hook after ComplyFlow loads. Ideal for bootstrapping integrations. |
| `complyflow_load_dependencies` | Action | `includes/Core/Plugin.php` | Fires when shared services and modules register. |
| `complyflow_init_modules` | Action | `includes/Core/Plugin.php` | Passes the module manager instance. Enables enabling/disabling modules programmatically. |
| `complyflow_modules_registered` | Action | `includes/Core/ModuleManager.php` | All modules registered but not initialized. |
| `complyflow_modules_initialized` | Action | `includes/Core/ModuleManager.php` | Modules initialized and ready. |
| `complyflow_module_loaded` | Action | `includes/Core/ModuleManager.php` | Fires for each module when loaded. Arguments: `$id`, `$module_instance`. |
| `complyflow_module_enabled` | Filter | `includes/Core/ModuleManager.php` | Filter module enabled state. Arguments: `$enabled`, `$module_id`. |

---

## 2. Admin Settings Hooks

| Hook | Type | Declared In | Description |
|------|------|-------------|-------------|
| `complyflow_settings_tabs` | Filter | `includes/Admin/Settings.php` | Modify or add settings tabs. |
| `complyflow_settings_sections` | Filter | `includes/Admin/Settings.php` | Extend settings sections within a tab. |
| `complyflow_settings_fields` | Filter | `includes/Admin/Settings.php` | Register additional settings fields. |

Settings renderers live under `includes/Admin/views/` for reference.

---

## 3. Accessibility Module

| Hook | Type | Declared In | Description |
|------|------|-------------|-------------|
| `complyflow_scan_issues` | Filter | `includes/Modules/Accessibility/Scanner.php` | Inspect or modify issues before they are persisted. Args: `$issues`, `$url`. |
| `complyflow_scheduled_scans_activated` | Action | `includes/Modules/Accessibility/ScheduledScanManager.php` | Fires when recurring scans enabled. Args: `$schedule`. |
| `complyflow_scheduled_scans_completed` | Action | Same file | Triggered after scheduled scan finishes. Args: `$results`. |
| `complyflow_notification_sent` | Action | Same file | Fires after scan notification email sent. Args: `$url`, `$recipients`, `$result`. |

---

## 4. Consent & Cookies

| Hook | Type | Declared In | Description |
|------|------|-------------|-------------|
| `complyflow_scanned_cookies` | Filter | `includes/Modules/Cookie/CookieScanner.php` | Modify detected cookies before saving. Args: `$cookies`, `$url`. |
| `complyflow_after_score` | Action | `includes/Modules/DevTools/Hooks.php` | Provides latest compliance score array. |

Additional consent hooks register within the consent banner JavaScript; see `assets/src/js/consent-banner.js` for events dispatched to the frontend.

---

## 5. DSR Module

| Hook | Type | Declared In | Description |
|------|------|-------------|-------------|
| `complyflow_export_user_data` | Filter | `includes/Modules/DSR/DataExporter.php` | Append data to the export bundle. Args: `$user_data`, `$email`. |

---

## 6. REST API Endpoints

ComplyFlow registers REST routes under the namespace `complyflow/v1`. Review `includes/API/RestController.php` and module-specific controllers for routes.

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/complyflow/v1/scan` | `POST` | Trigger on-demand accessibility scans. |
| `/complyflow/v1/consent/logs` | `GET` | Retrieve paginated consent logs (admin). |
| `/complyflow/v1/dsr/request` | `POST` | Submit a DSR request from the frontend portal. |

Authentication requirements follow WordPress REST conventions: cookie + nonce for admin, app passwords or OAuth via third-party plugins for headless scenarios.

---

## 7. CLI Commands

Registered in `includes/CLI/CommandRegistry.php` and implemented per module.

| Command | Description |
|---------|-------------|
| `wp complyflow scan run --url=<url>` | Run an accessibility scan for a URL. |
| `wp complyflow scan schedule list` | List scheduled scans. |
| `wp complyflow consent sync` | Synchronize consent logs. |
| `wp complyflow dsr list` | List outstanding DSR requests. |
| `wp complyflow settings export` | Export plugin settings as JSON. |

Run `wp help complyflow <subcommand>` for detailed arguments.

---

## 8. Translation Helpers

- Text domain: `complyflow`
- POT file: `languages/complyflow.pot`
- Load translations in `includes/Core/Plugin.php` (`load_plugin_textdomain`).

To generate updated translation templates:

```bash
wp i18n make-pot . languages/complyflow.pot
```

---

## 9. Extending Modules

Each module extends `ComplyFlow\Core\Module` (see `includes/Core/Module.php`) and is registered via `ModuleManager`.

### Enabling/Disabling Modules Programmatically

```php
add_filter('complyflow_module_enabled', function ($enabled, $module_id) {
    if ('consent' === $module_id && defined('DISABLE_CONSENT_MODULE')) {
        return false;
    }
    return $enabled;
}, 10, 2);
```

### Registering Custom Modules

```php
add_action('complyflow_modules_registered', function (\ComplyFlow\Core\ModuleManager $manager) {
    $manager->register('my-module', new \MyVendor\ComplyFlow\MyModule());
});
```

---

## 10. Utilities & Services

- **`ComplyFlow\Core\Cache`** — wrapper around WordPress transients/object cache.
- **`ComplyFlow\Database\Repository`** — base class for CRUD repositories.
- **`ComplyFlow\Modules\Analytics\ComplianceScore`** — calculates compliance metrics.
- **`ComplyFlow\Modules\Analytics\AuditTrail`** — records admin actions.

Consult the PHPDoc blocks in each class for method signatures.

---

## 11. Contributing & Testing

- Run unit tests: `composer run test`
- Static analysis: `composer run phpstan`
- Coding standards: `composer run phpcs`

Pull requests should include updated documentation where hooks or endpoints change.

---

For questions or suggestions on the developer API, contact `dev@complyflow.com` or open an issue in the project tracker.
