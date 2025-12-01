# ComplyFlow User Guide

Welcome to ComplyFlow — an all-in-one compliance and accessibility suite for WordPress. This guide walks through every major feature, from setup to daily operations.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Accessibility Module](#accessibility-module)
4. [Consent Manager](#consent-manager)
5. [Data Subject Rights (DSR) Portal](#data-subject-rights-dsr-portal)
6. [Cookie Inventory](#cookie-inventory)
7. [Legal Documents](#legal-documents)
8. [Analytics & Reporting](#analytics--reporting)
9. [Advanced Settings](#advanced-settings)
10. [Integrations](#integrations)
11. [Notifications & Emails](#notifications--emails)
12. [Role-Based Access](#role-based-access)
13. [Troubleshooting](#troubleshooting)
14. [Support & Resources](#support--resources)

---

## Getting Started

### Onboarding Wizard

Upon activation, ComplyFlow launches an onboarding wizard:

1. **Organization Profile** — enter company name, address, and primary contact.
2. **Compliance Goals** — choose regulations (GDPR, CCPA, etc.) to tailor defaults.
3. **Feature Selection** — enable modules as needed (Accessibility, Consent, DSR).
4. **Demo Data (Optional)** — insert sample data for testing and screenshots.

You can rerun the wizard from `ComplyFlow → Settings → Onboarding`.

### Key Concepts

- **Modules** — feature bundles that can be enabled individually.
- **Scans** — automated accessibility audits scheduled hourly, daily, or weekly.
- **Consent Categories** — necessary, functional, analytics, and marketing.
- **Requests** — DSR submissions tracked through statuses.

---

## Dashboard Overview

Navigate to `ComplyFlow → Dashboard` to view:

- **Compliance Score** — aggregated grade across modules.
- **Recent Activity Feed** — latest scans, consent changes, and DSR statuses.
- **Quick Actions** — shortcuts to run scans, review requests, and launch setup.
- **Module Cards** — at-a-glance metrics for accessibility, consent, and cookies.

Filters allow switching between 7-day, 30-day, and 90-day intervals.

---

## Accessibility Module

### Running Scans

1. Go to `ComplyFlow → Accessibility → Scanner`.
2. Choose pages, posts, or custom post types to scan.
3. Click **Run Scan** or schedule recurring scans.

### Viewing Results

- Results display severity (Critical, Serious, Moderate, Minor).
- Click any issue to expand remediation guidance.
- Export PDF reports for stakeholders.

### Scheduled Scans

Configure schedules under `ComplyFlow → Accessibility → Schedule`.

- Frequencies: hourly, daily, weekly.
- Email notifications to administrators.

---

## Consent Manager

### Banner Configuration

`ComplyFlow → Consent → Banner`

- Customize colors, typography, and placement.
- Localize banner text and button labels per region.
- Toggle cookie wall or soft opt-in modes.

### Geo-Targeting

`ComplyFlow → Consent → Geo Rules`

- Define behavior for EU, California, Brazil, Canada, and custom regions.
- Map regions to consent categories.

### Consent Logs

`ComplyFlow → Consent → Logs`

- View individual consent entries with timestamps and anonymized IPs.
- Filter by region, device, or categories accepted.
- Export CSV for audits.

---

## Data Subject Rights (DSR) Portal

### Public Portal

- Shortcode: `[complyflow_dsr_portal]`
- Template override: `templates/dsr-portal.php`

Visitors can submit requests for:

- Access, Rectification, Erasure, Portability, Restriction, Object, Automated Decision

### Workflow

1. **Pending** — request logged, email verification sent.
2. **Verified** — requester confirmed email.
3. **In Progress** — admin reviewing.
4. **Completed / Rejected** — final disposition.

Admins can attach files, notes, and export data bundles per request.

---

## Cookie Inventory

`ComplyFlow → Cookie Inventory`

- Automatically detects cookies across pages.
- Categorize cookies and assign purposes.
- Generate reports per domain or script.

Integrates with the consent banner to enforce categories.

---

## Legal Documents

### Policy Generator

`ComplyFlow → Legal → Questionnaire`

- Answer dynamic questions about data collection practices.
- Generate Privacy Policy, Cookie Policy, and Terms of Service.

### Snippets Library

`ComplyFlow → Legal → Snippets`

- Reusable clauses for marketing, analytics, and third-party services.
- Insert snippets into Gutenberg blocks or shortcodes.

---

## Analytics & Reporting

`ComplyFlow → Analytics`

- **Compliance Score** trends over time.
- **Audit Trail** listing system changes.
- **Report Exporter** for PDF and CSV summaries.

---

## Advanced Settings

- **Script Blocking** — add custom scripts to the blocklist.
- **Performance** — enable caching, configure batch sizes.
- **Developer Hooks** — toggle REST API endpoints and logging verbosity.

---

## Integrations

- **WooCommerce** — exports customer orders in DSR requests.
- **Page Builders** — compatibility with Elementor, Beaver Builder, Divi, WPBakery.
- **Third-Party Scripts** — built-in presets for Google Analytics, Meta Pixel, TikTok, etc.

Refer to `API-REFERENCE.md` for available hooks and filters.

---

## Notifications & Emails

`ComplyFlow → Settings → Notifications`

- Enable email alerts for new DSR requests and scan completions.
- Customize email templates with placeholders (`{{site_name}}`, `{{request_id}}`).

---

## Role-Based Access

Permissions align with WordPress capabilities:

| Capability | Description |
|------------|-------------|
| `manage_complyflow` | Full access to all modules |
| `audit_complyflow` | View-only access to dashboard and reports |
| `manage_complyflow_consent` | Manage consent settings and logs |
| `manage_complyflow_dsr` | Process DSR requests |

Roles can be assigned via `Users → Roles` or a role editor plugin.

---

## Troubleshooting

| Issue | Resolution |
|-------|------------|
| Banner not showing | Verify banner enabled and no conflicts with cache plugins |
| Scans stuck in pending | Check WP Cron and server schedulers |
| WooCommerce data missing | Ensure WooCommerce integration toggled on |
| Translation missing | Run `wp i18n make-pot` to regenerate POT file |

---

## Support & Resources

- Email: `support@complyflow.com`
- Documentation: `docs/` folder (this guide plus developer references)
- Knowledge Base: `https://support.complyflow.com`
- Changelog: `CHANGELOG.md`

Thank you for choosing ComplyFlow to power your compliance journey!
