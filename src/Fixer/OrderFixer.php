<?php
namespace Dki\WcOrderHealthcheck\Fixer;

use WC_Order;

class OrderFixer {
    public function fix_order(WC_Order $order, array $issues): bool {
        return false;
    }
}
