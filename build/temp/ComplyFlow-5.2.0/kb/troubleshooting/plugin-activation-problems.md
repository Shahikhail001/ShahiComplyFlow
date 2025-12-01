# Plugin Activation Problems

## Problem
The plugin fails to activate or deactivates immediately after activation.

## Solution
- Check for PHP and WordPress version requirements.
- Review error messages in the WordPress admin or debug log (`wp-content/debug.log`).
- Deactivate all other plugins and try activating ComplyFlow again to rule out conflicts.
- Ensure all dependencies are installed (Composer, NPM, built assets).

## Tips
- Enable WP_DEBUG in `wp-config.php` for more detailed error messages.
- Contact support with error logs if the issue persists.