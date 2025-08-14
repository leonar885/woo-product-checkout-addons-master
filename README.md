# Woo Product & Checkout Addons Master (WPCAM)

A lightweight plugin scaffold that provides per-product checkout add-ons stored as JSON forms. Includes a pricing engine, conditions evaluator, cart persistence and basic admin wiring.

- Textdomain: `wpcam`
- Namespace: `Wooqui\\WPCAM`

Usage

1. Install in a WordPress + WooCommerce site.
2. Create `WPCAM Forms` in the admin and attach them to products via the product edit screen metabox.
3. The form will render on the product page and add chosen addons to cart.

Testing

The repo includes a PHPUnit workflow that runs on GitHub Actions. To run locally you'll need PHP and Composer installed:

- composer install
- vendor/bin/phpunit -c phpunit.xml

Notes

This is an initial scaffold. Important TODOs:

- Implement admin form builder UI
- Replace eval-based formula evaluation with a safe parser
- Add more field templates and accessibility improvements

