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
            'limit' => $args['limit'] ?? 50,
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
        $items = $order->get_items(['line_item','fee','shipping']);

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
        $fields = ['_line_total' => $item->get_total()];

        // Only line items (products) have subtotal
        if (method_exists($item, 'get_subtotal')) {
            $fields['_line_subtotal'] = $item->get_subtotal();
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

    protected function too_many_decimals($v): bool {
        if (!is_numeric($v)) return false;
        return round((float)$v,2) !== (float)$v;
    }
}
