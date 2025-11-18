<?php
namespace Dki\WcOrderHealthcheck;

use Dki\WcOrderHealthcheck\Admin\AdminPage;
use Dki\WcOrderHealthcheck\Scanner\OrderScanner;

class Plugin {
    protected AdminPage $admin_page;
    protected OrderScanner $scanner;

    public function __construct() {
        $this->scanner = new OrderScanner();
        $this->admin_page = new AdminPage($this->scanner);
    }

    public function init(): void {
        add_action('admin_menu', [$this->admin_page, 'register_menu']);
    }
}
