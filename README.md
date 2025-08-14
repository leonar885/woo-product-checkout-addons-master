# Woo Product & Checkout Addons Master (WPCAM)

A lightweight plugin scaffold that provides per-product checkout add-ons stored as JSON forms. Includes a pricing engine, conditions evaluator, cart persistence and basic admin wiring.

- Textdomain: `wpcam`
- Namespace: `Wooqui\\WPCAM`

Usage

1. Install in a WordPress + WooCommerce site.
2. Create `WPCAM Forms` in the admin and attach them to products via the product edit screen metabox.
3. The form will render on the product page and add chosen addons to cart.

Testing

The repo includes both lightweight tests and PHPUnit tests that run on GitHub Actions.

### Running Tests Locally

#### Windows (PowerShell)
Use the provided helper script that automatically locates PHP and Composer:
```powershell
scripts/run-tests.ps1
```

Options:
- `-Help` - Show help information
- `-LightweightOnly` - Run only the simple test runner
- `-PhpUnitOnly` - Run only PHPUnit tests
- No options - Run both test suites

#### Manual Setup (All Platforms)
If you have PHP and Composer installed:

1. Install dependencies:
   ```bash
   composer install
   ```

2. Run lightweight tests:
   ```bash
   php tests/run_pricing_tests.php
   ```

3. Run PHPUnit tests:
   ```bash
   vendor/bin/phpunit -c phpunit.xml
   ```

Notes

This is an initial scaffold. Important TODOs:

- Implement admin form builder UI
- Replace eval-based formula evaluation with a safe parser
- Add more field templates and accessibility improvements

