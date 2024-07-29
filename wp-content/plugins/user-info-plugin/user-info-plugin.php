<?php
/*
Plugin Name: User Info Plugin
Description: A simple plugin to display user information.
Version: 1.0
Author: Your Name
*/

// Register shortcode to display user information
function user_info_shortcode() {
    // Check if user is logged in
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
      

        $user_info = '';
        $user_info .= '<div class="user-info">';
        $user_info .= '<p><strong>Username:</strong> ' . $current_user->user_login . '</p>';
        $user_info .= '<p><strong>Email:</strong> ' . $current_user->user_email . '</p>';
        $user_info .= '<p><strong>Display Name:</strong> ' . $current_user->display_name . '</p>';
        $user_info .= '</div>';
        return $user_info;
    } else {
        return 'Please log in to view user information.';
    }
}
add_shortcode('user_info', 'user_info_shortcode');
