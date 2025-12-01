# Demo Site Setup Guide

This guide explains how to provision the public demo at `demo.complyflow.com`. It covers hosting prerequisites, WordPress installation, data seeding, and ongoing maintenance.

---

## 1. Infrastructure Requirements

- **Domain:** `demo.complyflow.com`
- **Hosting:** VPS or managed WordPress host with PHP 8.2 and HTTPS
- **SSL Certificate:** Use Let’s Encrypt or host-provided certificates
- **Storage:** ≥ 2 GB free
- **Backups:** Daily automated backups recommended

---

## 2. Initial Provisioning

1. Create DNS `A` record for `demo.complyflow.com` pointing to the server IP.
2. Provision server or WordPress instance. On first run, install WordPress 6.7.
3. Secure admin credentials (use password manager; avoid `admin` username).
4. Install SSL certificate and force HTTPS.

---

## 3. WordPress Configuration

- Set site title to “ComplyFlow Demo”.
- Configure permalink structure to `/%postname%/`.
- Install required plugins: ComplyFlow, WooCommerce (optional demo), Classic Editor (if desired).
- Switch theme to Twenty Twenty-Four.

---

## 4. Installing ComplyFlow

1. Upload the latest release ZIP of ComplyFlow via the admin or FTP.
2. Activate the plugin.
3. Run the onboarding wizard and choose **Demo Mode** when prompted.

---

## 5. Demo Data Seeding

### 5.1 Accessibility Content

- Import the `demo-content/pages.xml` file provided with the repos (create placeholder pages if not available).
- Ensure at least 10 pages with mixed accessibility issues.
- Run initial accessibility scan and keep results.

### 5.2 Consent & Cookies

- Enable sample trackers (Google Analytics, Meta Pixel) in the banner settings.
- Use the seed script `wp complyflow consent seed --count=50` if available, or manually trigger consent events via frontend preview.

### 5.3 DSR Requests

- Submit dummy requests through the DSR portal using test emails.
- Update statuses manually to showcase workflow progression.

### 5.4 Legal Policies

- Complete questionnaire with fictional company details.
- Publish generated Privacy Policy, Cookie Policy, and Terms pages.

---

## 6. UX Polish

- Set homepage to the “Compliance Dashboard Overview” page.
- Configure menu links to key demo pages (Dashboard, Accessibility, Consent, DSR Portal, Legal).
- Add a banner or notice explaining the sandbox nature of the site.

---

## 7. Access Control

- Create `demo_manager` account with `manage_complyflow` capability for internal use.
- Disable account registrations to prevent spam.
- Install a security plugin (e.g., Wordfence) to block brute force attempts.

---

## 8. Maintenance Plan

| Task | Frequency | Notes |
|------|-----------|-------|
| Update WordPress & Plugins | Weekly | Automate when possible |
| Refresh Demo Data | Monthly | Reset scans, consent logs, DSR requests |
| Backup Site | Daily | Store off-site |
| Security Review | Monthly | Check for unauthorized changes |
| Performance Check | Quarterly | Lighthouse + GTmetrix |

---

## 9. Reset Procedure

If the demo becomes unstable:

1. Take a fresh backup.
2. Export data you want to keep (if any).
3. Reinstall WordPress and re-run steps above.
4. Update DNS TTL if migrating between hosts.

---

Keep credentials and deployment notes in a secure document shared with the support team. Rotate passwords every 90 days.
