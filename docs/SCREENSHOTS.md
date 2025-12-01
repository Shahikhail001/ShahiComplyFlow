# Screenshot Production Checklist

This document summarizes the screenshot requirements for the CodeCanyon listing and links to detailed capture guidelines.

---

## 1. Required Images

| # | Title | Description | Data Prep |
|---|-------|-------------|----------|
| 1 | Dashboard Overview | ComplyFlow dashboard with compliance score, activity feed, widgets | Compliance score ~78/100, recent activity populated |
| 2 | Accessibility Scanner | Scan results screen showing severity filters and remediation tips | Example scan with mixed severity issues |
| 3 | Consent Banner (EU) | Frontend banner with EU copy, buttons, and preferences modal | Geo target: EU, highlight category toggles |
| 4 | DSR Portal | Frontend DSR form with request type dropdown and multi-step flow | Portal populated with demo branding |
| 5 | Settings Panel | Admin settings tabs visible (General, Consent, Accessibility) | Key toggles enabled

Optional extras: Cookie inventory, Legal generator, Analytics dashboard, Audit trail.

---

## 2. Capture Specifications

- Resolution: 1920 × 1080 (Full HD)
- Format: PNG, optimized via TinyPNG or similar
- File size: 200–400 KB target, 500 KB max
- Browser: Chrome latest, zoom 100%, UI chrome hidden
- Theme: Twenty Twenty-Four (default) unless otherwise noted

Refer to `documentation/SCREENSHOT-GUIDE.md` for granular cropping instructions and data seeding steps.

---

## 3. Workflow

1. Complete demo data seeding (see `DEMO-SETUP.md`).
2. Switch WordPress to Twenty Twenty-Four theme.
3. Set screen resolution to 1920 × 1080 and enter full-screen mode (F11).
4. Capture each screenshot using the guide above.
5. Optimize images and rename sequentially: `01-dashboard.png`, `02-accessibility.png`, etc.
6. Place optimized PNGs in `docs/assets/screenshots/` (create folder if needed).

---

## 4. Status Tracking

| Image | Captured | Optimized | Notes |
|-------|----------|-----------|-------|
| Dashboard | ☐ | ☐ |  |
| Accessibility | ☐ | ☐ |  |
| Consent Banner | ☐ | ☐ |  |
| DSR Portal | ☐ | ☐ |  |
| Settings Panel | ☐ | ☐ |  |

Store final screenshots alongside the CodeCanyon submission package.
