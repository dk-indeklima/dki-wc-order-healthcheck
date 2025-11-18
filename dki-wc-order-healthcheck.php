<?php
/**
 * Plugin Name: DKI WooCommerce Order Healthcheck
 * Description: Analyze WooCommerce orders for abnormal monetary values and rounding issues.
 */

if (!defined('ABSPATH')) { exit; }

define('DKI_WC_ORDER_HEALTHCHECK_PLUGIN_DIR', plugin_dir_path(__FILE__));

spl_autoload_register(function ($class) {
    $prefix = 'Dki\\WcOrderHealthcheck\\';
    $base_dir = DKI_WC_ORDER_HEALTHCHECK_PLUGIN_DIR . 'src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) { return; }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) { require $file; }
});

function dki_wc_order_healthcheck_bootstrap() {
    if (!class_exists('WooCommerce')) { return; }
    $plugin = new \Dki\WcOrderHealthcheck\Plugin();
    $plugin->init();
}
add_action('plugins_loaded', 'dki_wc_order_healthcheck_bootstrap');
