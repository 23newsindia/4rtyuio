<?php
class MACP_CSS_Test_Ajax {
    private $css_optimizer;
    
    public function __construct() {
        $this->css_optimizer = new MACP_CSS_Optimizer();
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('wp_ajax_macp_test_unused_css', [$this, 'handle_test_request']);
    }

    public function handle_test_request() {
        // Verify nonce
        if (!check_ajax_referer('macp_admin_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }

        // Get and validate URL
        $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : home_url('/');
        
        try {
            $results = $this->css_optimizer->test_unused_css($url);
            wp_send_json_success($results);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}
