# DKI WooCommerce Order Healthcheck

Contributors: dkindeklima

Tags: woocommerce, orders, precision, rounding, healthcheck

Requires at least: 5.8

Tested up to: 6.4

Requires PHP: 8.0

Stable tag: 1.0.0

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Analyze WooCommerce orders for monetary value precision issues and rounding problems.

## Description

DKI WooCommerce Order Healthcheck is a diagnostic tool that scans your WooCommerce orders for monetary values with excessive decimal precision (more than 2 decimal places). This can help identify and fix data quality issues that may cause accounting discrepancies or display problems.

### Features 

* Scans all order statuses (pending, processing, completed, on-hold, cancelled, refunded, failed)
* Checks line item fields: totals, subtotals, and tax amounts
* Checks order-level fields: order total, subtotal, tax, shipping, and discounts
* Checks coupon/discount amounts and percentages
* Displays clickable links to orders for easy access
* Shows current problematic values and suggested rounded values
* Scans up to 100 most recent orders by default

### Fields Scanned

**Line Item Fields:**
* Line total (_line_total)
* Line subtotal (_line_subtotal)
* Line tax (_line_tax)
* Line subtotal tax (_line_subtotal_tax)

**Order-Level Fields:**
* Order total
* Order subtotal
* Total tax
* Shipping total
* Shipping tax
* Discount total

**Coupon Fields:**
* Discount amount
* Discount percentage (for percentage-based coupons)

## Installation

1. Upload the plugin files to `/wp-content/plugins/dki-wc-order-healthcheck/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and active
4. Navigate to WooCommerce > Order Healthcheck

## Usage

1. Go to WooCommerce > Order Healthcheck in your WordPress admin
2. Click "Scan recent orders" button
3. Review the results table showing any precision issues found
4. Click on Order IDs to open orders in a new tab for manual review
5. The table shows:
   - Order ID (clickable link)
   - Item ID (0 indicates order-level field)
   - Field name with the issue
   - Current value with excessive precision
   - Suggested rounded value (2 decimal places)

## Frequently Asked Questions

### How many orders are scanned?

By default, the plugin scans the 100 most recent orders across all statuses. This limit is set to balance thoroughness with performance.

### What order statuses are included?

All WooCommerce order statuses are scanned: pending, processing, on-hold, completed, cancelled, refunded, and failed. Refund objects are excluded.

### What does "too many decimals" mean?

Monetary values should have exactly 2 decimal places (e.g., 10.50). Values like 10.501 or 10.5000001 have excessive precision and may indicate rounding errors or data quality issues.

### Does this plugin fix the issues automatically?

Currently, this plugin only detects and reports issues. The fix functionality (OrderFixer) is a placeholder for future development. Issues must be corrected manually.

### What does Item ID 0 mean?

Item ID 0 indicates the issue is at the order level (order total, tax, shipping, etc.) rather than a specific line item.

## Changelog 

= 1.0.0 =
* Initial release
* Scans 100 most recent orders
* Detects precision issues in line items, order totals, and discounts
* Displays results with clickable order links
* Checks all order statuses

## Technical Details

### Requirements
* WordPress 5.8+
* WooCommerce 6.0+
* PHP 8.0+

### Architecture
* PSR-4 autoloading
* Object-oriented design
* Uses WooCommerce APIs (wc_get_orders, WC_Order, WC_Order_Item)
* Nonce protection for admin forms
* Requires manage_woocommerce capability

## Privacy

This plugin does not:
* Collect or transmit any data externally
* Store any additional data in the database
* Track users or orders
* Use cookies

All scanning happens locally on your server.