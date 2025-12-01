# Module Development

## Overview
Extend ComplyFlow by creating custom modules using the provided architecture and interfaces.

## Key Concepts
- **ModuleInterface**: All modules must implement this interface for consistency.
- **Custom Module Creation**: Create a new class in `includes/Modules/YourModule/` and register it in the ModuleManager.
- **Dependency Injection**: Use constructor injection for services and repositories.
- **PSR-4 Autoloading**: Follow the PSR-4 standard for class and file structure.

## Steps
1. Create a new module directory and PHP class implementing `ModuleInterface`.
2. Register your module in the ModuleManager.
3. Use dependency injection for accessing core services.
4. Add hooks, filters, and settings as needed.

## Tips
- Review existing modules for best practices.
- Use the SettingsRepository for configuration.
- Document your module for maintainability.