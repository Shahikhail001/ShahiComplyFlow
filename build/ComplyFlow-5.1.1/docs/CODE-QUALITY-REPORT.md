# Code Quality & QA Report Template

Use this template to log results from automated and manual quality checks prior to release.

---

## 1. Overview

- **Release Version:** 1.0.0
- **Audit Date:** `YYYY-MM-DD`
- **Auditor:** `Name`

---

## 2. Automated Checks

### 2.1 Composer Dependencies

```bash
composer install
```

| Check | Command | Status | Notes |
|-------|---------|--------|-------|
| Coding Standards | `composer run phpcs` | ☐ |  |
| Static Analysis | `composer run phpstan` | ☐ |  |
| Unit Tests | `composer run test` | ☐ |  |
| Test Coverage | `composer run test-coverage` | ☐ | Output stored in `coverage/` |

Record any violations and their resolutions below.

### 2.2 Node Tooling

```bash
npm install
npm run build
```

Document warnings (e.g., Tailwind content warnings) and confirm final assets generated.

---

## 3. Manual QA Summary

| Area | Result | Notes |
|------|--------|-------|
| Accessibility scans | ☐ Pass |  |
| Consent banner behavior | ☐ Pass |  |
| DSR request lifecycle | ☐ Pass |  |
| Cookie inventory detection | ☐ Pass |  |
| Legal document generation | ☐ Pass |  |

---

## 4. Security Review

1. Input sanitization verified for settings forms.
2. Nonce protection applied to admin actions.
3. Prepared statements used for custom queries.
4. Escaping functions used in templates.
5. File uploads (if any) validated.

Use this section to note findings and remediations.

---

## 5. Performance Benchmarks

| Metric | Tool | Baseline | Release | Notes |
|--------|------|----------|---------|-------|
| Dashboard load time | Query Monitor | 2.0s |  |  |
| Frontend impact | Lighthouse | 50ms |  |  |
| DB queries per page | Query Monitor | 15 |  |  |

---

## 6. Sign-Off

| Role | Name | Date | Approval |
|------|------|------|----------|
| QA Lead |  |  | ☐ |
| Engineering Manager |  |  | ☐ |

Attach logs, screenshots, and relevant reports when filing this document for release.
