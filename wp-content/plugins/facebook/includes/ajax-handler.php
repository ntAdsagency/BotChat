<?php
require_once plugin_dir_path( __FILE__ ) . './controlller/Account.php';
require_once plugin_dir_path(__FILE__) .'./controlller/Fapage.php';
require_once plugin_dir_path(__FILE__) .'./controlller/Product.php';
require_once plugin_dir_path(__FILE__) .'./controlller/User.php';
require_once plugin_dir_path(__FILE__) .'./controlller/Conversation.php';
require_once plugin_dir_path(__FILE__) .'./controlller/Messager.php';

// Đăng ký các action cho Ajax
add_action( 'wp_ajax_get_account', 'my_plugin_ajax_get_items' );
add_action( 'wp_ajax_nopriv_get_account', 'my_plugin_ajax_get_items' );

add_action( 'wp_ajax_save_appid_account', 'save_appid' );
add_action( 'wp_ajax_nopriv_save_appid_account', 'save_appid' );

add_action( 'wp_ajax_UpdateAccountFacebook_account', 'update_account' );
add_action( 'wp_ajax_nopriv_UpdateAccountFacebook_account', 'update_account' );

//account
function my_plugin_ajax_get_items() {
    $items = get_account();
    wp_send_json_success( $items );
}
function save_appid() {
    $appId = $_POST['Appid']; 
    $result = saveAppId($appId);
    wp_send_json_success($result);
}
function update_account() {  
    $data=$_POST['data'];
    $result= UpdateAccountFacebook($data);
    wp_send_json_success($result);
}
// fanpage

add_action( 'wp_ajax_get_fanpages', 'get_fanpage' );
add_action( 'wp_ajax_nopriv_get_fanpages', 'get_fanpage' );

add_action( 'wp_ajax_updateOrAdd_fanpages', 'updateOrAdd_fanpage' );
add_action( 'wp_ajax_nopriv_updateOrAdd_fanpages', 'updateOrAdd_fanpage' );

function get_fanpage(){
    $admin_id =$_GET['data'];
    $data= get_fanpages($admin_id);
    wp_send_json_success($data);
}
function updateOrAdd_fanpage(){
    $data = $_POST['data'];
    $result= fanpagesave($data);
    wp_send_json_success($result);
}
// product
add_action( 'wp_ajax_get_list_products', 'get_list_product');
add_action( 'wp_ajax_nopriv_get_list_products', 'get_list_product');

function get_list_product(){
    $data= get_list_productIs();
    wp_send_json_success($data);
}
// user
add_action( 'wp_ajax_get_user', 'get_user');
add_action( 'wp_ajax_nopriv_get_user', 'get_user');
function get_user(){
    $data = $_GET['data'];
    $result = getUser($data);
    wp_send_json_success($result);
}
add_action( 'wp_ajax_Update_UserFacebook', 'Update_UserFacebook');
add_action( 'wp_ajax_nopriv_Update_UserFacebook', 'Update_UserFacebook');
function Update_UserFacebook(){
    $data = $_POST['data'];
    $result = UpdateUserFacebook($data);
    wp_send_json_success($result);
}

// wp_facebook_conversations
add_action( 'wp_ajax_get_Conversations', 'get_Conversations');
add_action( 'wp_ajax_nopriv_get_Conversations', 'get_Conversations');
function get_Conversations(){
    $data = $_GET['data'];
    $result = getConversations($data);
    wp_send_json_success($result);
}

add_action( 'wp_ajax_Update_Conversations', 'Update_Conversations');
add_action( 'wp_ajax_nopriv_Update_Conversations', 'Update_Conversations');
function Update_Conversations(){
    $dataArray = $_POST['data'];
    $result = UpdateConversations($dataArray);
    wp_send_json_success($result);
}

// call messager

add_action( 'wp_ajax_get_Messages', 'get_Messages');
add_action( 'wp_ajax_nopriv_get_Messages', 'get_Messages');
function get_Messages(){
    $data = $_GET['data'];
    $result = getMessages($data);
    wp_send_json_success($result);
}
add_action( 'wp_ajax_Update_Messages', 'Update_Messages');
add_action( 'wp_ajax_nopriv_Update_Messages', 'Update_Messages');
function Update_Messages(){
    $dataArray = $_POST['data'];
    $result = UpdateMessages($dataArray);
    wp_send_json_success($result);
}

