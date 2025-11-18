<?php
namespace Dki\WcOrderHealthcheck\Admin;

use Dki\WcOrderHealthcheck\Scanner\OrderScanner;

class AdminPage {
    protected OrderScanner $scanner;
    protected string $page_slug = 'dki-wc-order-healthcheck';

    public function __construct(OrderScanner $scanner) {
        $this->scanner = $scanner;
    }

    public function register_menu(): void {
        add_submenu_page(
            'woocommerce',
            'Order Healthcheck',
            'Order Healthcheck',
            'manage_woocommerce',
            $this->page_slug,
            [$this, 'render_page']
        );
    }

    public function render_page(): void {
        $results = [];
        $did_scan = false;

        if (isset($_POST['dki_wc_order_healthcheck_scan'])) {
            check_admin_referer('dki_wc_order_healthcheck_scan');
            $results = $this->scanner->scan(['limit' => 50]);
            $did_scan = true;
        }

        echo '<div class="wrap"><h1>DKI WooCommerce Order Healthcheck</h1>';
        echo '<form method="post">';
        wp_nonce_field('dki_wc_order_healthcheck_scan');
        submit_button('Scan recent orders','primary','dki_wc_order_healthcheck_scan');
        echo '</form>';

        if ($did_scan) { $this->render_results_table($results); }
        echo '</div>';
    }

    protected function render_results_table(array $results): void {
        if (empty($results)) { echo '<p>No issues found.</p>'; return; }

        echo '<table class="widefat striped"><thead><tr>
            <th>Order ID</th><th>Item ID</th><th>Field</th>
            <th>Current value</th><th>Suggested value</th>
        </tr></thead><tbody>';

        foreach ($results as $r) {
            $order_url = admin_url('post.php?post=' . $r['order_id'] . '&action=edit');
            foreach ($r['issues'] as $i) {
                echo '<tr>';
                echo '<td><a href="' . esc_url($order_url) . '" target="_blank">' . esc_html($r['order_id']) . '</a></td>';
                echo '<td>' . esc_html($i['item_id']) . '</td>';
                echo '<td>' . esc_html($i['field']) . '</td>';
                echo '<td>' . esc_html($i['value']) . '</td>';
                echo '<td>' . esc_html($i['rounded']) . '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
    }
}
