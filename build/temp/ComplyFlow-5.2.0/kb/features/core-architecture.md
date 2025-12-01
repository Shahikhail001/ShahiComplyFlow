
# Core Architecture

## What is the Core Architecture?
ComplyFlow is built on a modular, enterprise-grade architecture that ensures performance, extensibility, and security. Understanding the core structure helps users and site administrators make the most of the plugin and troubleshoot or extend it as needed.

## Key Components

### 1. Main Plugin Class (`Plugin.php`)
- **Singleton Pattern**: Only one instance of the plugin runs, preventing conflicts.
- **Hook Management**: Centralizes all WordPress actions and filters for easy management.
- **Module Initialization**: Loads and manages all feature modules (Consent, Accessibility, DSR, etc.).
- **Internationalization**: Loads translation files for multi-language support.
- **REST API & WP-CLI**: Registers custom REST API endpoints and WP-CLI commands for automation and integration.

### 2. Module Manager (`ModuleManager.php`)
- **Dynamic Module Registration**: Enables or disables modules as needed.
- **Dependency Management**: Ensures modules load in the correct order and with required dependencies.
- **Capability Checks**: Only users with the right permissions can access or configure modules.
- **Versioning**: Each module can be versioned and updated independently.

### 3. Loader (`Loader.php`)
- **Action/Filter Registration**: Handles all WordPress hooks in a single place.
- **Priority Management**: Controls the order in which hooks are executed.

### 4. Caching (`Cache.php`)
- **WordPress Transients**: Stores temporary data for performance.
- **Object Caching**: Supports Redis/Memcached for high-traffic sites.
- **Cache Groups**: Organizes cache by type (settings, scans, consent, etc.).
- **TTL Management**: Controls how long data is cached (15 min to 24 hours).
- **Cache Statistics**: Tracks cache hits/misses for optimization.

### 5. Settings Repository (`SettingsRepository.php`)
- **Centralized Settings**: All plugin settings are stored and retrieved from one place.
- **Validation/Sanitization**: Ensures all settings are safe and valid.
- **Import/Export**: Easily move settings between sites or environments.
- **Option Caching**: Settings are cached for fast access.

### 6. Activator/Deactivator
- **Database Table Creation**: Sets up all required tables on activation.
- **Default Settings**: Initializes plugin with safe defaults.
- **Cleanup**: Removes or resets data on deactivation.
- **Version Checks**: Ensures compatibility with WordPress and PHP versions.

## How It Benefits You
- **Performance**: Optimized for speed and low resource usage.
- **Security**: Follows WordPress best practices for data handling and permissions.
- **Extensibility**: Easily add or remove features as your needs change.
- **Reliability**: Modular design means one feature can be updated or fixed without affecting others.

## Practical Tips
- Use the settings import/export feature to quickly configure multiple sites.
- If you experience issues, check the plugin’s cache and try flushing it from the Advanced settings tab.
- Advanced users can use WP-CLI and REST API endpoints for automation and integration with other tools.

## Related Documentation
- See the Admin Settings and Advanced/Developer documentation for more on customizing and extending the plugin.