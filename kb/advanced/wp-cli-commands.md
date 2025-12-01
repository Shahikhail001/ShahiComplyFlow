# WP-CLI Commands

## Overview
ComplyFlow provides WP-CLI commands for automation, scripting, and advanced management.

## Usage
All commands are prefixed with `wp complyflow`.

## Key Commands
- **scan run --url=<url>**: Run an accessibility scan for a specific URL.
- **scan schedule list**: List scheduled scans.
- **consent sync**: Synchronize consent logs.
- **dsr list**: List outstanding DSR requests.
- **settings export**: Export plugin settings as JSON.
- **cache clear**: Clear plugin cache.

## Tips
- Use `wp help complyflow <subcommand>` for detailed usage.
- Combine with cron jobs for scheduled automation.
- WP-CLI commands require SSH or terminal access to your server.