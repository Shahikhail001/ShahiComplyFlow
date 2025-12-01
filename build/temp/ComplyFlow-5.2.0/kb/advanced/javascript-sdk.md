# JavaScript SDK

## Overview
The JavaScript SDK provides frontend utilities for managing consent, cookies, and compliance events.

## Key Features
- **Consent Status Management**: Check and update user consent status in real time.
- **Cookie Helpers**: Read, set, and delete cookies with helper functions.
- **Event Dispatching**: Listen for and trigger compliance-related events (e.g., consent given, scan completed).
- **Integration Utilities**: Easily integrate with custom scripts or third-party tools.

## Usage Example
```js
// Check consent status
ComplyFlowSDK.getConsentStatus();

// Listen for consent changes
ComplyFlowSDK.on('consentChanged', (status) => {
  // Custom logic here
});
```

## Tips
- Use the SDK to build custom banners, analytics, or integrations.
- See developer documentation for full API details.