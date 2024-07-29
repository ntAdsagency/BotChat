<?php

/**
 * Plugin Name: My Webhook Plugin
 * Description: Handle webhook requests in WordPress.
 * Version: 1.0
 * Author: Your Name
 */

add_action('init', 'webhook_handler');
$webhook_data;
function webhook_handler()
{
    if (isset($_GET['webhook'])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $verify_token = "123456";
            if ($_GET['hub_verify_token'] === $verify_token) {
                echo $_GET['hub_challenge'];
                exit;
            } else {
                echo 'Invalid verify token';
                exit;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $webhook_data = file_get_contents('php://input');  
            file_put_contents(__DIR__ . '/webhook.log', print_r($webhook_data, true), FILE_APPEND); 
            http_response_code(200);
        }
    }
}  
add_action('rest_api_init', function () {
    register_rest_route('webhook-handler/v1', '/sse', array(
        'methods' => 'GET',
        'callback' => 'sse_handler',
    ));
});
function sse_handler() {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    $log_file = __DIR__ . '/webhook.log';
    $last_modified_time = filemtime($log_file);
    $last_event_id = null;

    while (true) {
        clearstatcache();
        if (file_exists($log_file) && filemtime($log_file) > $last_modified_time) {
            $log_content = file_get_contents($log_file);
            $last_modified_time = filemtime($log_file);
            if($log_content!=''){
                $sse_data = $log_content;
                echo "data: $sse_data\n\n";
                ob_flush();
                flush();
               // file_put_contents($log_file, '');
            }
        } else {
            $sse_data = "";
            echo "data: $sse_data\n\n";
            ob_flush();
            flush();
        }
        sleep(5);
    }
}
