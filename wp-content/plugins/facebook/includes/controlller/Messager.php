<?php
require_once plugin_dir_path( __FILE__ ) . '../db-config.php';

function getMessages($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_messages';
    $messages = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE conversationid = %s ORDER BY created_time ASC",
            $data['conversationid']
        ),
        ARRAY_A
    );
    return $messages;
}
function UpdateMessages($dataArray) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_messages';
    $results = []; 
    foreach ($dataArray as $data) {
        $existing_account = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %s",
                $data['id']
            )
        );
        if ($existing_account) {
            $sql = $wpdb->prepare(
                "UPDATE $table_name 
                 SET sender_id = %s, recipient_id = %s, message = %s, attachments = %s,created_time = %s
                 WHERE id = %s",
                $data['sender_id'], 
                $data['recipient_id'], 
                $data['message'], 
                $data['attachments'], 
                $data['created_time'], 
                $data['id']
            );
            $result = $wpdb->query($sql);
        } else {
            $sql = $wpdb->prepare(
                "INSERT INTO $table_name (conversationid,id,sender_id, recipient_id, message, attachments, created_time) 
                 VALUES (%s, %s, %s, %s, %s, %s, %s)",
                     $data['conversationid'],
                     $data['id'],
                     $data['sender_id'], 
                     $data['recipient_id'], 
                     $data['message'], 
                     $data['attachments'], 
                     $data['created_time']
            );
            $result = $wpdb->query($sql);
        }
        if ($result === false) {
            echo $wpdb->last_error;
            return new WP_Error('db_error', 'Database error: ' . $wpdb->last_error);
        }
        $results[] = $result; 
    }

    return $results;
}



 