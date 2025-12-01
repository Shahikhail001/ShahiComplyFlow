# Database and Storage

## Overview
ComplyFlow uses custom database tables and the repository pattern for efficient, scalable data management.

## Key Concepts
- **Custom Table Schemas**: Tables for consent, scans, DSR requests, cookies, and trackers.
- **Repository Pattern**: Each table has a repository class for CRUD operations.
- **Query Optimization**: Indexed columns and prepared statements for performance and security.
- **Data Migration & Cleanup**: Automated cleanup via scheduled tasks; migration scripts for upgrades.

## Tips
- Backup your database before major updates.
- Use repository classes for all data access.
- Review table schemas in `includes/Database/` for details.