# Release Packaging Checklist

Complete this checklist before submitting ComplyFlow to CodeCanyon or distributing a new release.

---

## 1. Build Artifacts

- [ ] `npm run build` executed; assets present in `assets/dist/`
- [ ] Source maps included for JS/CSS
- [ ] PHP autoloader optimized (optional: `composer dump-autoload -o`)

---

## 2. Documentation Bundle

- [ ] `README.txt` (WordPress/CodeCanyon format)
- [ ] `docs/USER-GUIDE.md`
- [ ] `docs/API-REFERENCE.md`
- [ ] `docs/INSTALLATION.md`
- [ ] `docs/VIDEO-SCRIPT.md`
- [ ] `docs/DEMO-SETUP.md`
- [ ] `docs/TESTING-MATRIX.md`
- [ ] `docs/CODE-QUALITY-REPORT.md`
- [ ] PDF exports created (if required by marketplace)

---

## 3. Licensing & Legal

- [ ] `LICENSE.txt` (GPL v2 or later)
- [ ] Third-party library attributions included (e.g., Chart.js)
- [ ] Verify no proprietary assets bundled without permission

---

## 4. Testing Sign-Off

- [ ] PHP 8.0 – 8.3 matrix completed
- [ ] WordPress 6.4 – 6.7 matrix completed
- [ ] Theme/plugin compatibility documented in `COMPATIBILITY.md`
- [ ] QA sign-off recorded in `CODE-QUALITY-REPORT.md`

---

## 5. Screenshot & Media Assets

- [ ] Minimum 5 PNG screenshots optimized
- [ ] Thumbnail/preview image (590 × 300) created
- [ ] Video tutorial recorded and hosted (YouTube/Vimeo)

Store final media assets in `docs/assets/` prior to packaging.

---

## 6. Packaging Steps

1. Ensure repository clean and version tagged (e.g., `v1.0.0`).
2. Remove dev-only folders from release archive (`tests/`, `docs/` optional).
3. Create distribution ZIP containing:
   - `complyflow.php`
   - `assets/`
   - `includes/`
   - `languages/`
   - `templates/`
   - `README.txt`
   - `LICENSE.txt`
4. Validate ZIP by installing on a fresh WordPress instance.

---

## 7. Submission Checklist

- [ ] Fill out CodeCanyon item details using `documentation/CODECANYON-LISTING.md`
- [ ] Upload screenshots and preview video
- [ ] Provide support contact email and response time
- [ ] Confirm 6-month support statement and change log

---

## 8. Post-Release Tasks

- [ ] Update changelog and push to repository
- [ ] Notify subscribers/customers
- [ ] Schedule support coverage for launch week

Keep this file updated with marketplace changes or additional partner requirements.
