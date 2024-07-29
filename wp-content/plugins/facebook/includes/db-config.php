<?php
function connect_db() {
    global $wpdb;
    $db_host = DB_HOST;
    $db_name = DB_NAME;
    $db_user = DB_USER;
    $db_password = DB_PASSWORD;
    $wpdb = new wpdb( $db_user, $db_password, $db_name, $db_host );
    if ( $wpdb->error ) {
        die( 'no data: ' . $wpdb->error );
    }
   
}
function disconnect_db() {
    global $wpdb;
    $wpdb = null;
}