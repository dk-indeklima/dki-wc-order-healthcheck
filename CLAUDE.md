# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a WordPress/WooCommerce plugin that detects and fixes monetary value issues in WooCommerce orders, specifically focusing on rounding precision problems (values with more than 2 decimal places).

## Architecture

The plugin follows a simple object-oriented architecture with three main components:

1. **Plugin** (`src/Plugin.php`) - Bootstrap class that initializes the plugin and wires up dependencies. Creates the OrderScanner and AdminPage instances, and hooks into WordPress admin menu system.

2. **OrderScanner** (`src/Scanner/OrderScanner.php`) - Core scanning logic that:
   - Fetches WooCommerce orders via `wc_get_orders()`
   - Iterates through order items (line items, fees, shipping)
   - Detects fields (`_line_total`, `_line_subtotal`) with excessive decimal precision
   - Returns array of issues with suggested rounded values

3. **AdminPage** (`src/Admin/AdminPage.php`) - WooCommerce admin UI integration:
   - Registers submenu under WooCommerce menu
   - Renders scan form with nonce protection
   - Displays results in a WordPress-style table
   - Shows order ID, item ID, field name, current value, and suggested rounded value

4. **OrderFixer** (`src/Fixer/OrderFixer.php`) - Placeholder for fix functionality (currently returns false, not implemented yet)

## Autoloading

Custom PSR-4 style autoloader in main plugin file (`dki-wc-order-healthcheck.php:11-19`) maps the `Dki\WcOrderHealthcheck\` namespace to `src/` directory.

## WooCommerce Integration

- Plugin only initializes if WooCommerce is active (checked in `dki_wc_order_healthcheck_bootstrap`)
- Uses WooCommerce APIs: `wc_get_orders()`, `wc_get_order_statuses()`, `WC_Order`, `WC_Order_Item`
- Admin page requires `manage_woocommerce` capability

## Development Commands

This is a standard WordPress plugin. No build, lint, or test commands are configured yet.

To test the plugin:
1. Install in WordPress plugins directory
2. Activate the plugin
3. Navigate to WooCommerce > Order Healthcheck in WordPress admin
4. Click "Scan recent orders" to analyze last 50 orders
