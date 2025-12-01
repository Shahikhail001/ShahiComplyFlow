# REST API

## Overview
ComplyFlow exposes a robust REST API for integration, automation, and advanced workflows. All endpoints are namespaced under `/wp-json/complyflow/v1/`.

## Authentication & Authorization
- Uses WordPress cookie + nonce for admin users.
- Supports application passwords or OAuth for headless and third-party integrations.
- All sensitive endpoints require proper capabilities (e.g., `manage_options`).

## Key Endpoints
- **/scan** (POST): Trigger accessibility scans.
- **/scan** (GET): List scan results.
- **/consent/logs** (GET): Retrieve consent logs.
- **/dsr/request** (POST): Submit DSR requests.
- **/dsr/request** (GET): List DSR requests.

## Rate Limiting & Security
- Nonce validation and permission callbacks on all endpoints.
- CORS headers and security best practices enforced.
- Rate limiting can be implemented via custom plugins or API gateways.

## Third-Party Integrations
- Use API endpoints to connect with external compliance, analytics, or automation tools.
- See API Reference for full endpoint list and parameters.