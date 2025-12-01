# Compatibility & Integration Notes

This document tracks platform compatibility, third-party integrations, and known issues. Keep it updated with each release.

---

## 1. Supported Platforms

| Platform | Version Range | Notes |
|----------|---------------|-------|
| WordPress Core | 6.4 – 6.7 | Verified in testing matrix |
| PHP | 8.0 – 8.3 | Requires `ext-json`, `ext-mbstring` |
| Database | MySQL 5.7+, MariaDB 10.4+ | Uses standard WP schema |

---

## 2. Themes

| Theme | Status | Notes |
|-------|--------|-------|
| Twenty Twenty-Four | ✅ Fully supported | Default styling reference |
| Astra | ✅ Compatible | Use customizer to match consent banner branding |
| GeneratePress | ✅ Compatible | No known conflicts |
| Divi | ⚠️ Requires testing | Ensure builder scripts allowed by consent module |
| Elementor Hello | ✅ Compatible | Consent banner integrates with Elementor popups |

Add new findings as they arise.

---

## 3. Plugins / Integrations

| Plugin | Status | Notes |
|--------|--------|-------|
| WooCommerce | ✅ Supported | DSR exports include orders, customers, reviews |
| Contact Form 7 | ✅ Supported | Form submissions included in data exports |
| Gravity Forms | ⚠️ Pending | Requires connector add-on (planned) |
| WP Rocket | ✅ Compatible | Exclude consent scripts from delay settings |
| Yoast SEO | ✅ Compatible | SEO analysis unaffected |

---

## 4. REST API Consumers

- Headless frontends can consume REST routes with OAuth or App Passwords.
- Ensure consent state is stored client-side via provided JS helpers.

---

## 5. Multi-Language Support

- Text domain `complyflow` loaded on `plugins_loaded`.
- `.pot` file located at `languages/complyflow.pot`.
- Compatible with WPML and Polylang (test translation of consent banner strings).

---

## 6. Multisite Considerations

- Network activation supported.
- Each site manages its own consent logs and DSR requests.
- Global settings available via `ComplyFlow → Network Settings` when network-activated.

---

## 7. Known Issues

| ID | Description | Workaround | Status |
|----|-------------|------------|--------|
| CF-101 | Consent banner hidden by some cache plugins | Disable HTML minification or add exclusion | Monitoring |
| CF-137 | Accessibility scan timeouts on very large sites | Increase PHP max execution time to 180s | Planned fix |

---

## 8. Future Integrations

- HubSpot CRM sync (planned)
- Salesforce marketing consent connector
- Dedicated Gravity Forms DSR data bridge

Document new integrations in this file whenever they are released.
