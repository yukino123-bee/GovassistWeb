# Security Hardening Rollback Guide

This guide applies to the security hardening introduced on July 28, 2026. No
database migrations or dependency changes were made, so rollback does not
require a database restore or `composer install`.

## Before rollback

1. Put the application in maintenance mode: `php artisan down`.
2. Capture the current changes: `git diff > security-hardening-backup.patch`.
3. Record the failing request, response status, and recent application log
   entries so the incompatible hardening can be isolated.

## Selective rollback

Prefer reverting only the affected behavior:

- Response headers: `app/Http/Middleware/AddSecurityHeaders.php` and
  `bootstrap/app.php`.
- Login, registration, verification, or inquiry throttling:
  `app/Providers/AppServiceProvider.php` and `routes/web.php`.
- Guest inquiry session ownership or upload ownership checks:
  `app/Http/Controllers/ResidentController.php`.
- Filesystem configuration cleanup:
  `app/Http/Controllers/ResidentController.php` and
  `app/Http/Controllers/FacilitatorController.php`.

If this work is committed as one isolated commit, use
`git revert <security-hardening-commit>` and deploy the resulting revert commit.
That is safer than rewriting shared Git history.

## Restore service

Run:

```bash
vendor/bin/pint --format agent
php artisan test --compact
php artisan optimize:clear
php artisan up
```

Verify login, guest inquiry creation and reply, resident document upload, and
facilitator document review.

## Security actions that must not be undone

- Do not restore the `/run-migrations` web route. Run deployments through the
  trusted deployment process with `php artisan migrate --force`.
- Do not put real credentials back in `.env.example`.
- Do not restore credential fallbacks in `scripts/deploy.cjs`. Configure
  `FTP_USERNAME`, `FTP_PASSWORD`, `PRODUCTION_APP_URL`, `PRODUCTION_DB_HOST`,
  `PRODUCTION_DB_DATABASE`, `PRODUCTION_DB_USERNAME`, and
  `PRODUCTION_DB_PASSWORD` in the deployment environment.
- Rotate both database passwords and the FTP password that were previously
  present in tracked files. Removing a leaked secret from the current files
  does not invalidate it or remove it from Git history.
