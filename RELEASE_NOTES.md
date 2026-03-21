# Release Notes

## [2.0.0] - 2026-03-22

### Breaking changes

- **PHP**: Minimum supported version is now **8.3** (was 8.2). Upgrade your runtime before updating this package.

### Added

- **Laravel 13** support. The package declares compatibility with **Laravel 11.x, 12.x, and 13.x** via `illuminate/*` constraints.

### Changed

- Bumped **spatie/laravel-package-tools** to a release that supports Laravel 13.
- **Development dependencies** refreshed for the new stack: Pest 4, `pest-plugin-laravel` 4.1, Orchestra Testbench 9–11, Livewire 3.7+/4 (dev), PHPUnit 12 / Pest 4 test tooling, PHPStan 2, Laravel Pint.
- **CI** (GitHub Actions): tests run on **PHP 8.3, 8.4, and 8.5** against **Laravel 11, 12, and 13**, including `prefer-lowest` / `prefer-stable` with minimum Laravel patch versions required by the Pest Laravel plugin.
- **phpunit.xml** uses the PHPUnit 12 schema.
- **`composer.json` `analyse` script**: PHPStan runs with `--memory-limit=512M` for reliable analysis in CI and locally.

### Upgrade guide

1. Ensure the application runs **PHP ≥ 8.3**.
2. Use **Laravel 11.45.2+**, **12.52.0+**, or **13.x** (or rely on Composer to resolve compatible framework versions).
3. Run `composer update gowelle/laravel-beem-africa` (or your root constraint for this package).

---

## [1.11.0] - 2026-01-15

### Added

- **Dark Mode Support**: Full dark mode integration for all UI components
  - **Vue Components**:
    - `BeemCheckoutButton.vue`
    - `BeemOtpVerification.vue`
    - `BeemSmsForm.vue`
  - **Livewire Components**:
    - `beem-checkout.blade.php`
    - `beem-otp-verification.blade.php`
    - `beem-sms-form.blade.php`
  - Supports both system preference (`prefers-color-scheme: dark`) and class-based (`.dark`) toggling strategies.
  - Custom color palette for dark environments (Slate/Neutral tones with Beem accents).

- **Mobile Friendliness**: Enhanced responsive design
  - Optimized layouts for mobile viewports.
  - Improved touch targets and spacing for mobile interaction.
  - Verified component visibility and usability on small screens.
