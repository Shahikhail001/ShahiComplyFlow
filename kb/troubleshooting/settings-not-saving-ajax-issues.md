# Settings Not Saving (AJAX Issues)

## Problem
Changes in plugin settings are not saved, or no confirmation appears after clicking Save.

## Solution
- Ensure JavaScript is enabled in your browser.
- Check for JavaScript errors in the browser console (F12 → Console tab).
- Make sure all plugin files are up to date and assets are built (`npm run build`).
- If using a caching or security plugin, clear cache and whitelist AJAX URLs.
- Verify that the WordPress admin AJAX endpoint (`admin-ajax.php`) is accessible.

## Tips
- Try saving settings in a different browser or incognito mode.
- Check for plugin conflicts by disabling other plugins temporarily.