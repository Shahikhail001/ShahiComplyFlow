# ComplyFlow Testing Matrix

Use this matrix to validate ComplyFlow across supported environments prior to release. Record pass/fail status and notes for each combination.

---

## 1. Environment Grid

### 1.1 WordPress & PHP Versions

| WP \ PHP | 8.0 | 8.1 | 8.2 | 8.3 |
|-----------|-----|-----|-----|-----|
| 6.4 | ☐ | ☐ | ☐ | ☐ |
| 6.5 | ☐ | ☐ | ☐ | ☐ |
| 6.6 | ☐ | ☐ | ☐ | ☐ |
| 6.7 | ☐ | ☐ | ☐ | ☐ |

Add rows as new WordPress releases become available.

### 1.2 Theme Compatibility

| Theme | Version | Status | Notes |
|-------|---------|--------|-------|
| Twenty Twenty-Four | Latest | ☐ |  |
| Astra | Latest | ☐ |  |
| GeneratePress | Latest | ☐ |  |
| Divi | Latest | ☐ |  |
| Elementor Hello | Latest | ☐ |  |

### 1.3 Plugin Compatibility

| Plugin | Version | Scenario | Status | Notes |
|--------|---------|----------|--------|-------|
| WooCommerce | 8.x | Checkout consent, DSR exports | ☐ |  |
| WooCommerce | 9.x | Same as above | ☐ |  |
| Contact Form 7 | Latest | Consent + form submissions | ☐ |  |
| WP Rocket | Latest | Cached banner rendering | ☐ |  |

---

## 2. Test Suites

### 2.1 Automated

| Tool | Command | Status | Notes |
|------|---------|--------|-------|
| PHPUnit | `composer run test` | ☐ |  |
| PHPStan | `composer run phpstan` | ☐ |  |
| PHPCS | `composer run phpcs` | ☐ |  |

### 2.2 Manual Functional Tests

| ID | Area | Steps | Status | Notes |
|----|------|-------|--------|-------|
| F-01 | Activation | Install, activate, verify dashboard loads | ☐ |  |
| F-02 | Accessibility Scan | Run scan, review issues, export PDF | ☐ |  |
| F-03 | Consent Banner | Accept/decline, change preferences, log entries | ☐ |  |
| F-04 | DSR Workflow | Submit request, process statuses, export data | ☐ |  |
| F-05 | Cookie Inventory | Run detection, categorize cookies, sync with banner | ☐ |  |
| F-06 | Legal Generator | Complete questionnaire, publish policies | ☐ |  |
| F-07 | Integrations | Verify WooCommerce data export | ☐ |  |
| F-08 | Localization | Switch language (e.g., es_ES) and review strings | ☐ |  |
| F-09 | Uninstall | Deactivate and uninstall, confirm data removal | ☐ |  |

### 2.3 Performance & Security

| ID | Check | Tool/Method | Status | Notes |
|----|-------|-------------|--------|-------|
| P-01 | Frontend load impact | Lighthouse / WebPageTest | ☐ |  |
| P-02 | Database query count | Query Monitor | ☐ |  |
| S-01 | XSS & Escaping | Manual code review | ☐ |  |
| S-02 | CSRF protection | Verify nonces on forms | ☐ |  |
| S-03 | SQL Injection | Confirm prepared statements | ☐ |  |

---

## 3. Regression Checklist

Run these checks after every significant update:

- [ ] Accessibility scans execute without fatal errors.
- [ ] Consent banner respects geo rules across major browsers.
- [ ] DSR exports include WooCommerce orders when enabled.
- [ ] Cookie categories persist after cache flush.
- [ ] Legal documents regenerate with updated questionnaire answers.
- [ ] REST API endpoints respond with expected authentication requirements.

---

## 4. Sign-Off

| Reviewer | Role | Date | Approval |
|----------|------|------|----------|
|  | QA Lead |  | ☐ |
|  | Product Owner |  | ☐ |

Store completed matrices in the release folder for audit purposes.
