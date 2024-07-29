<?php
require_once plugin_dir_path( __FILE__ ) . '../db-config.php';

function getUser($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_users';

    $user = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %s",
            $data['user_id']
        ),
        ARRAY_A
    );
    
    return $user;
}
// insert or update account 
function UpdateUserFacebook($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_users';

    $existing_account = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %s",
            $data['user_id']
        )
    );

    if ($existing_account) {
        $sql = $wpdb->prepare(
            "UPDATE $table_name 
             SET username = %s, email = %s, phone_number = %s, user_id = %s,url =%s
             WHERE user_id = %s",
            $data['username'], $data['email'], $data['phone_number'], $data['user_id'], $data['url'],$data['user_id']
        );
        $result = $wpdb->query($sql);
    } 
    else {
        $sql = $wpdb->prepare(
            "INSERT INTO $table_name (username,email,phone_number,user_id,url) 
             VALUES (%s, %s, %s, %s, %s)",
             $data['username'], $data['email'], $data['phone_number'], $data['user_id'], $data['url']
        );
        $result = $wpdb->query($sql);
    }
    if ($result === false) {
        echo $wpdb->last_error;
        return new WP_Error('db_error', 'Database error: ' . $wpdb->last_error);
    }
    
    return $result;
}



 