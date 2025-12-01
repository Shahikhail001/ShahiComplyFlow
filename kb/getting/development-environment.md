# Development Environment

## Setting Up the Environment

### Local Development Tools
- **Local by Flywheel**: A user-friendly local WordPress development tool.
- **Docker with wp-env**: A containerized WordPress environment for advanced users.

### Installing Dependencies

Run the following commands in the plugin directory:

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Building Assets

Compile the plugin's assets:

```bash
npm run build
```

### Watching for Changes

For development, use the following command to enable hot reloading:

```bash
npm run dev
```

## Code Quality Tools

### PHP Tools
- **PHPCS**: Enforces WordPress Coding Standards.
- **PHPStan**: Performs static analysis to catch errors.

### JavaScript Tools
- **ESLint**: Lints JavaScript files for errors.
- **Prettier**: Formats code for consistency.

## File Structure Overview

```
complyflow/
├── assets/             # Frontend and backend assets
├── includes/           # Core plugin functionality
├── templates/          # Frontend templates
├── languages/          # Translation files
├── complyflow.php      # Main plugin file
└── composer.json       # PHP dependencies
```