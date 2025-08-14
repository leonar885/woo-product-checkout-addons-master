# Woo Product & Checkout Addons Master (WPCAM)

A lightweight plugin scaffold that provides per-product checkout add-ons stored as JSON forms. Includes a pricing engine, conditions evaluator, cart persistence and basic admin wiring.

- Textdomain: `wpcam`
- Namespace: `Wooqui\\WPCAM`

## Features

✅ **Admin Form Builder**: Create dynamic forms with various field types
✅ **Multiple Field Types**: Text, Textarea, Number, and Checkbox fields
✅ **Advanced Pricing**: Fixed, percentage, per-character, multiplier, and formula-based pricing
✅ **Real-time Price Calculation**: Frontend JavaScript calculates pricing as users interact
✅ **Cart Integration**: Seamless integration with WooCommerce cart and checkout
✅ **Form Validation**: Client-side and server-side validation with security measures
✅ **Responsive Design**: Mobile-friendly forms and admin interface

## Usage

### Installation
1. Install in a WordPress + WooCommerce site
2. Navigate to **WPCAM** in your WordPress admin menu

### Creating Forms
1. Go to **WPCAM** → **Add New Form**
2. Enter a form title
3. Add fields using the "Add Field" button
4. Configure each field:
   - **Field Type**: Choose from text, textarea, number, or checkbox
   - **Field Name**: Auto-generated from label, or set manually
   - **Label**: Display name for the field
   - **Default Value**: Pre-filled value (optional)
   - **Required**: Make field mandatory
   - **Pricing Method**: Choose how this field affects product price

### Pricing Methods
- **No additional cost**: Field doesn't affect price
- **Fixed amount**: Adds a fixed price (e.g., +$5.00)
- **Percentage**: Adds percentage of base price (e.g., 10% of product price)
- **Per character**: Multiplies by character count (e.g., $0.50 per character)
- **Multiply by value**: Multiplies field value by rate (e.g., quantity × $2.00)
- **Custom formula**: Use formulas like `{base_price} * 0.1 + {value}`

### Attaching Forms to Products
1. Edit any WooCommerce product
2. Look for "WPCAM Forms" metabox in the sidebar
3. Select the form you want to attach
4. Save the product

### Form Display
Forms automatically appear on product pages above the "Add to Cart" button for products with attached forms.

## Testing

The repo includes a PHPUnit workflow that runs on GitHub Actions. To run locally you'll need PHP and Composer installed:

```bash
composer install
vendor/bin/phpunit -c phpunit.xml
```

Or run the standalone tests:
```bash
php tests/run_pricing_tests.php
```

## API Reference

### Pricing Formulas
Use these variables in custom formulas:
- `{base_price}` - Product base price
- `{quantity}` - Cart item quantity  
- `{value}` - Current field value
- `{field_name}` - Any other field value by name

Supported functions:
- `min(a, b)` - Minimum value
- `max(a, b)` - Maximum value  
- `round(n)` - Round to nearest integer
- `floor(n)` - Round down
- `ceil(n)` - Round up

### REST API
- `GET /wp-json/wpcam/v1/forms/{id}` - Get form data (admin only)

## Security Features

- Nonce verification for all form submissions
- Server-side validation of required fields and data types
- Field sanitization and validation against form schema
- Safe formula evaluation with blacklisted functions
- Input sanitization throughout

## Development Notes

This is an initial scaffold with room for enhancement:

### Completed Features
- ✅ Admin form builder UI with drag-and-drop field management
- ✅ Multiple field types with proper templates
- ✅ Advanced pricing engine with formula support
- ✅ Real-time price calculation and validation
- ✅ Comprehensive test suite
- ✅ Security improvements and validation
- ✅ Responsive admin and frontend styling

### Future Enhancements
- Form conditions and logic (show/hide fields based on other field values)
- Additional field types (select, radio, file upload)
- Form templates and presets
- Advanced conditional pricing
- Email notifications and form submissions
- Integration with popular form builders
- Multi-language support improvements
- Advanced styling options

## Changelog

### v0.1.0
- Initial scaffold with basic functionality
- Added comprehensive admin form builder
- Implemented real-time pricing calculation
- Added security validation and sanitization
- Created responsive frontend templates
- Added comprehensive test coverage

## Support

For issues and feature requests, please use the GitHub repository issues page.

