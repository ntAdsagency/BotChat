<?php
require_once plugin_dir_path( __FILE__ ) . '../db-config.php';

// Lấy danh sách các mục từ bảng wp_my_items
function get_account() {
    global $wpdb;
    connect_db();

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wp_facebook_accounts" );
  
    return $results;
}
function saveAppId($appId) {
    global $wpdb;
    connect_db();
    $table_name = $wpdb->prefix . 'wp_facebook_accounts';
    $result = $wpdb->query(
        $wpdb->prepare(
            "UPDATE $table_name SET AppId = $appId",
        )
    ); 
    return $result;
}

// insert or update account 
function UpdateAccountFacebook($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_accounts';

    // Check if the account with the given AppId exists
    $existing_account = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE userID = %s",
            $data['userID']
        )
    );

    // If the account exists, update it
    if ($existing_account) {
        $sql = $wpdb->prepare(
            "UPDATE $table_name 
             SET Name = %s, thumb = %s, accessToken = %s, userID = %s,AppId =%s
             WHERE userID = %s",
            $data['Name'], $data['Url'], $data['accessToken'], $data['userID'], $data['AppId'], $data['userID']
        );
        $result = $wpdb->query($sql);
    } 
    else {
        $sql = $wpdb->prepare(
            "INSERT INTO $table_name (Name, AppId, thumb, accessToken, userID) 
             VALUES (%s, %s, %s, %s, %s)",
            $data['Name'], $data['AppId'], $data['Url'], $data['accessToken'], $data['userID']
        );
        $result = $wpdb->query($sql);
    }

    // Check the result and return appropriate response
    if ($result === false) {
        echo $wpdb->last_error;
        return new WP_Error('db_error', 'Database error: ' . $wpdb->last_error);
    }
    
    return $result;
}



 