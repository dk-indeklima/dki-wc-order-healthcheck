<?php
namespace Dki\WcOrderHealthcheck\Scanner;

use WC_Order;
use WC_Order_Item;

class OrderScanner {
    public function scan(array $args=[]): array {
        // Get all statuses and strip 'wc-' prefix
        $statuses = array_map(function($status) {
            return str_replace('wc-', '', $status);
        }, array_keys(wc_get_order_statuses()));

        $orders = wc_get_orders([
            'limit' => $args['limit'] ?? 100,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => $statuses,
            'return' => 'objects',
            'type' => 'shop_order', // Exclude refunds
        ]);
        $results = [];
        foreach ($orders as $order) {
            $issues = $this->scan_order($order);
            if ($issues) {
                $results[] = ['order_id'=>$order->get_id(),'issues'=>$issues];
            }
        }
        return $results;
    }

    protected function scan_order(WC_Order $order): array {
        $issues = [];

        // Check order-level monetary fields
        $issues = array_merge($issues, $this->scan_order_totals($order));

        // Check order items
        $items = $order->get_items(['line_item','fee','shipping','coupon']);

        if (!is_array($items) && !is_iterable($items)) {
            return $issues;
        }

        foreach ($items as $item_id=>$item) {
            if (!$item instanceof \WC_Order_Item) {
                continue;
            }
            $issues = array_merge($issues, $this->scan_item($item_id,$item));
        }
        return $issues;
    }

    protected function scan_item(int $item_id, WC_Order_Item $item): array {
        $issues = [];
        $fields = [];

        // Handle coupon items (discounts)
        if ($item->get_type() === 'coupon') {
            // Check discount amount
            $fields['discount_amount'] = $item->get_discount();

            // Check if discount is percentage-based (stored in meta)
            $discount_percent = $item->get_meta('coupon_data');
            if ($discount_percent && isset($discount_percent['discount_type']) && $discount_percent['discount_type'] === 'percent') {
                if (isset($discount_percent['amount'])) {
                    $fields['discount_percent'] = $discount_percent['amount'];
                }
            }
        } else {
            // All items have total
            $fields['_line_total'] = $item->get_total();

            // Line items (products) have additional fields
            if (method_exists($item, 'get_subtotal')) {
                $fields['_line_subtotal'] = $item->get_subtotal();
                $fields['_line_tax'] = $item->get_total_tax();
                $fields['_line_subtotal_tax'] = $item->get_subtotal_tax();
            }
        }

        foreach ($fields as $k=>$v) {
            if ($this->too_many_decimals($v)) {
                $issues[] = [
                    'item_id'=>$item_id,'field'=>$k,
                    'value'=>(float)$v,'rounded'=>round((float)$v,2)
                ];
            }
        }
        return $issues;
    }

    protected function scan_order_totals(WC_Order $order): array {
        $issues = [];
        $fields = [
            'order_total' => $order->get_total(),
            'order_subtotal' => $order->get_subtotal(),
            'order_tax' => $order->get_total_tax(),
            'order_shipping' => $order->get_shipping_total(),
            'order_shipping_tax' => $order->get_shipping_tax(),
            'order_discount' => $order->get_total_discount(),
        ];

        foreach ($fields as $k=>$v) {
            if ($this->too_many_decimals($v)) {
                $issues[] = [
                    'item_id'=>0, // 0 indicates order-level field
                    'field'=>$k,
                    'value'=>(float)$v,
                    'rounded'=>round((float)$v,2)
                ];
            }
        }
        return $issues;
    }

    protected function too_many_decimals($v): bool {
        if (!is_numeric($v)) return false;
        return round((float)$v,2) !== (float)$v;
    }
}
