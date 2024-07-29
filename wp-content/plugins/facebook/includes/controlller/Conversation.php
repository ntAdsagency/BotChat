<?php
require_once plugin_dir_path( __FILE__ ) . '../db-config.php';

function getConversations($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_conversations';
    $conversations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE pageid = %s ORDER BY created_time DESC",
            $data['pageid']
        ),
        ARRAY_A
    );
    return $conversations;
}
function UpdateConversations($dataArray) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'facebook_conversations';
    $results = []; 
    foreach ($dataArray as $data) {
        $existing_account = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE conversationid = %s",
                $data['conversationid']
            )
        );
        if ($existing_account) {
            $sql = $wpdb->prepare(
                "UPDATE $table_name 
                 SET pageid = %s, userid = %s, url = %s,created_time =%s, participants = %s, message = %s, message_count = %s, unread_count = %s
                 WHERE conversationid = %s",
                $data['pageid'], 
                $data['userid'], 
                $data['url'], 
                $data['created_time'],
                $data['participants'], 
                $data['message'],         
                $data['message_count'], 
                $data['unread_count'], 
                $data['conversationid']
            );
            $result = $wpdb->query($sql);
        } else {
            $sql = $wpdb->prepare(
                "INSERT INTO $table_name (pageid, conversationid, userid, url,created_time, participants, message, message_count, unread_count) 
                 VALUES (%s, %s, %s,%s, %s, %s, %s, %s, %s)",
                $data['pageid'], 
                $data['conversationid'], 
                $data['userid'], 
                $data['url'], 
                $data['created_time'], 
                $data['participants'], 
                $data['message'], 
                $data['message_count'], 
                $data['unread_count']
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



 