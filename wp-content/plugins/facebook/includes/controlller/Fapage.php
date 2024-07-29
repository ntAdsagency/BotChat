<?php
require_once plugin_dir_path( __FILE__ ) . '../db-config.php';

// Lấy danh sách các mục từ bảng wp_my_items
function get_fanpages($admin_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_fanpages'; 
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE admin_id = %d", 
            $admin_id
        ),
    );
    return $results;
}

function fanpagesave($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_fanpages';
    connect_db();
    $result = array();
    foreach ($data as $page) {
        $page_id = sanitize_text_field($page['id']);
        $fanpage_name = sanitize_text_field($page['name']);
        $accessToken = sanitize_text_field($page['accessToken']);
        $thumb = sanitize_text_field($page['pictureUrl']);
        $admin_id = sanitize_text_field($page['admin_id']);
        $wpdb->replace(
            $table_name,
            array(
                'page_id' => $page_id,
                'fanpage_name' => $fanpage_name,
                'accessToken' => $accessToken,
                'thumb' => $thumb,
                'admin_id' => $admin_id
            ),
            array('%s', '%s', '%s', '%s', '%d')
        );
        if ($wpdb->rows_affected > 0) {
            $updated_fanpage = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_name WHERE admin_id = %s and page_id = %d", $admin_id,$page_id),
                ARRAY_A
            );
            $result[] = $updated_fanpage;
        }
    }

    return $result;
}
 