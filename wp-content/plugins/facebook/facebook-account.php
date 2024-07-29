<?php
/*
Plugin Name: FBMessageHub
Description: Created by nt041, the goal is to help with marketing and sales on Facebook messages.
Version: 1.0
Author: Facebook
*/
function enqueue_my_styles1()
{
    wp_enqueue_style('main-style', plugins_url('./assets/css/index.css', __FILE__), array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts1', 'enqueue_my_styles1');
if (!defined('ABSPATH')) {
    exit; // Thoát nếu truy cập trực tiếp
}
require_once plugin_dir_path(__FILE__) . 'includes/db-config.php';
include_once plugin_dir_path(__FILE__) . 'includes/webhook.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'views/index.php';
require_once plugin_dir_path(__FILE__) . 'views/login.php';
require_once plugin_dir_path(__FILE__) . 'views/fanpage.php';



add_action('init', 'handle_webhook');
function handle_webhook()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['webhook'])) {
        include_once plugin_dir_path(__FILE__) . 'includes/webhook.php';
        exit;
    }
}

function facebbok_admin_menu()
{
    add_menu_page(
        'FBMessageHub', // Tiêu đề trang
        'FBMessageHub', // Tiêu đề menu
        'manage_options', // Capability
        'FBMessageHub-Index', // Slug của menu
        'facebook_index_page',
        'dashicons-facebook-alt', // Icon (tùy chọn)
        6 // Vị trí trong menu
    );
    add_submenu_page(
        'facebook-message-hub',    // Slug của trang cha (ở đây là slug của trang chính)
        'Login FBMessageHub',      // Tiêu đề trang
        'Login',                   // Tiêu đề menu
        'manage_options',          // Capability cần thiết để truy cập
        'FBMessageHub-Login', // Slug của trang login
        'login_FBMessageHub'      // Callback function để hiển thị nội dung trang
    );
    add_submenu_page(
        'facebook-message-hub',    // Slug của trang cha (ở đây là slug của trang chính)
        'fanpage FBMessageHub',      // Tiêu đề trang
        'fanpage',                   // Tiêu đề menu
        'manage_options',          // Capability cần thiết để truy cập
        'FBMessageHub-fanpage', // Slug của trang login
        'Fanpage'      // Callback function để hiển thị nội dung trang
    );
}
add_action('admin_menu', 'facebbok_admin_menu');

// dang ký js ajax
add_action( 'wp_enqueue_scripts', 'enqueue_ajax_scripts' );

function enqueue_ajax_scripts() {
    wp_enqueue_script( 'mainfacebook', get_template_directory_uri() . '/js/mainfacebook.js', array(), '1.0', true );
    wp_enqueue_script( 'ajax2', get_template_directory_uri() . '/js/ajax2.js', array(), '1.0', true );
    wp_enqueue_script( 'ajax3', get_template_directory_uri() . '/js/ajax3.js', array(), '1.0', true );
}





function scriptjs(){
    ?>
       <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<?
}

function script()
{
    ?>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
    <script>    
          window.fbAsyncInit = function() {
            FB.init({
                appId: '1403855930299830',
                cookie: true,
                xfbml: true,
                version: 'v19.0'
            });
            FB.AppEvents.logPageView();
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        };
        localStorage.setItem("appid", "1403855930299830");

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        checkFacebookTokenExpiration();
        setInterval(checkFacebookTokenExpiration, 60 * 1000);
        function checkFacebookTokenExpiration() {
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    var authResponse = response.authResponse;
                    var currentTime = new Date().getTime() / 1000;
                    if (authResponse.expiresAt / 1000 < currentTime) {
                        console.log("Access token đã hết hạn");
                        redirectToLogin();
                    } else {
                        console.log("Access token còn hiệu lực");
                    }
                    if (authResponse.data_access_expiration_time < currentTime) {
                        console.log("Data access đã hết hạn");
                        redirectToLogin();
                    } else {
                        console.log("Data access còn hiệu lực");
                    }
                } else {
                    console.log("Người dùng chưa đăng nhập hoặc đã đăng xuất");
                    redirectToLogin();
                }
            });
        }

        function redirectToLogin() {
            window.location.href = 'https://eshop.theme.trueads.vn/wp-admin/admin.php?page=FBMessageHub-Login';
        }
    </script>
<?php
}
?>