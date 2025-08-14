# WPCAM Usage Examples

## Example 1: Simple Text Customization

**Scenario**: Allow customers to add custom text to a product (e.g., engraving)

1. Create a new form: "Product Customization"
2. Add a text field:
   - **Label**: "Custom Text"
   - **Field Name**: "custom_text"
   - **Required**: Yes
   - **Pricing Method**: Per character
   - **Pricing Value**: 0.25 (25¢ per character)

**Result**: Customer pays $2.00 extra for "JOHN DOE" (8 characters × $0.25)

## Example 2: Size Selection with Fixed Pricing

**Scenario**: Offer size upgrades with different prices

1. Create form: "Size Options"
2. Add checkbox field:
   - **Label**: "Upgrade to Large Size"
   - **Pricing Method**: Fixed amount
   - **Pricing Value**: 5.00

**Result**: Checking the box adds $5.00 to the product price

## Example 3: Complex Formula Pricing

**Scenario**: Bulk discount based on quantity and base price

1. Create form: "Bulk Options"
2. Add number field:
   - **Label**: "Bulk Quantity"
   - **Field Name**: "bulk_qty"
   - **Pricing Method**: Custom formula
   - **Pricing Value**: `{value} > 10 ? {base_price} * 0.1 : 0`

**Result**: Orders over 10 units get 10% discount

## Example 4: Multi-field Form

**Scenario**: Gift wrap service with multiple options

1. Create form: "Gift Services"
2. Add checkbox: "Gift Wrap" (Fixed: $3.00)
3. Add textarea: "Gift Message" (Per character: $0.10)
4. Add checkbox: "Express Gift Delivery" (Percentage: 15%)

**Result**: Complex pricing based on selected options

## Example 5: Conditional Logic (Future Enhancement)

```json
{
  "fields": [
    {
      "name": "gift_wrap",
      "type": "checkbox",
      "label": "Add Gift Wrap",
      "pricing": {"method": "fixed", "value": 3.00}
    },
    {
      "name": "gift_message", 
      "type": "textarea",
      "label": "Gift Message",
      "show_if": {"gift_wrap": true},
      "pricing": {"method": "per_char", "value": 0.10}
    }
  ]
}
```

## Integration with Themes

### Customizing Form Display Location

```php
// In your theme's functions.php
remove_action( 'woocommerce_before_add_to_cart_button', [Wooqui\WPCAM\Render::class, 'render_form'] );
add_action( 'woocommerce_after_single_product_summary', function() {
    if ( class_exists( 'Wooqui\\WPCAM\\Render' ) ) {
        $render = new Wooqui\WPCAM\Render();
        $render->render_form();
    }
}, 15 );
```

### Custom Field Templates

Create custom field templates by copying `templates/field-text.php` to your theme:
`your-theme/wpcam/field-custom.php`

### CSS Customization

```css
/* Override default styles */
.wpcam-form {
    background: #fff;
    border: 2px solid #your-brand-color;
    border-radius: 10px;
}

.wpcam-field label {
    color: #your-text-color;
    font-weight: bold;
}
```