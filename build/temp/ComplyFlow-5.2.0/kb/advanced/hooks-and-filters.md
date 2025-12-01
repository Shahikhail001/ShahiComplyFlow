# Hooks and Filters

## Overview
Extend and customize ComplyFlow using WordPress hooks and filters.

## Core Lifecycle Hooks
- `complyflow_activated`: Fires after plugin activation.
- `complyflow_deactivated`: Runs during deactivation.
- `complyflow_init`: Earliest hook after plugin loads.

## Module-Specific Hooks
- `complyflow_scan_issues`: Inspect or modify scan issues.
- `complyflow_scanned_cookies`: Modify detected cookies before saving.
- `complyflow_export_user_data`: Append data to DSR exports.

## Settings & Data Filters
- `complyflow_settings_tabs`: Add or modify settings tabs.
- `complyflow_settings_fields`: Register additional settings fields.
- `complyflow_after_score`: Access latest compliance score array.

## Tips
- Use hooks to add features, modify data, or integrate with other plugins.
- See API Reference for a full list of available hooks and arguments.