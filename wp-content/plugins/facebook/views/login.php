<?php
$image_url = plugins_url('assets/image.jpg', __FILE__);
function login_FBMessageHub()
{
?>

    <title>FBMessageHub</title>
    <!-- Option 1: Include in HTML -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .section-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-content p {
            font-size: 18px;
            line-height: 1.6;
        }
    </style>

    <section class="container section-content">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-4">FBMessageHub</h2>
                <p class="text-p">
                    Chào mừng đến với FBMessageHub – ứng dụng kết nối trực tiếp đến Facebook để gửi tin nhắn sản phẩm trên các Fanpage. Với FBMessageHub, bạn có thể dễ dàng tương tác và tiếp cận khách hàng thông qua các thông điệp sản phẩm chuyên nghiệp, giúp tăng cường chiến lược marketing của bạn trên mạng xã hội lớn nhất thế giới. Hãy khám phá và trải nghiệm ngay hôm nay!
                </p>
                <fb:login-button scope="
                	email, catalog_management, 
                    pages_manage_cta, 
                    pages_show_list, 
                    ads_management, 
                    ads_read, 
                    business_management, 
                    pages_messaging, leads_retrieval,
                    page_events, pages_read_engagement,
                    pages_manage_metadata, pages_read_user_content,
                    pages_manage_ads, pages_manage_posts, 
                    pages_manage_engagement, public_profile
                " onlogin="checkLoginState()"></fb:login-button>
               
            </div>
            <div class="col-lg-6">
                <?php
                $image_url = plugins_url('../assets/img/facebookmaket.png', __FILE__);
                echo '<img src="' . esc_url($image_url) . '" class="img-fluid" />';
                ?>

            </div>
            <div id="fb-root"></div>
    </section>

    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="<?php echo plugins_url('assets/js/mainfacebook.js', __DIR__); ?>"></script>
    <script>
        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        }
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                    var authResponse = response.authResponse;
                    var currentTime = new Date().getTime() / 1000;
                    if (authResponse.expiresAt / 1000 < currentTime) {
                        console.log("Access token đã hết hạn");
                        redirectToLogin();
                    } else {
                        sessionStorage.setItem('fbAuthResponse', JSON.stringify(authResponse));
                        getUserInfo(response.authResponse.accessToken);
                    }
                    if (authResponse.data_access_expiration_time < currentTime) {
                        redirectToLogin();
                    }  
                } else {
                    console.log("Người dùng chưa đăng nhập hoặc đã đăng xuất");
                    redirectToLogin();
            }
        }
        function SaveAccount(datas) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'UpdateAccountFacebook_account',
                    data: datas
                },
                success: function(response) {
                     
                    try {
                        var authResponseString = sessionStorage.getItem('fbAuthResponse');
                        if (!authResponseString) {
                            console.log("Không tìm thấy thông tin đăng nhập Facebook");
                            return;
                        }
                        var authResponse = JSON.parse(authResponseString);
                        var timeEnd =authResponse.userLogin.timeEnd;
                        var currentTime = Math.floor(Date.now());
                        if(timeEnd>currentTime){
                            window.location.href = '<?php echo admin_url('admin.php?page=FBMessageHub-Index'); ?>';
                        } 

                    } catch (error) {
                        
                    }
                   
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        function getUserInfo(accessToken) {
            var fbAuthResponse=JSON.parse(sessionStorage.getItem("fbAuthResponse")); 
            FB.api('/me', {
                fields: 'id,name,picture',
                access_token: accessToken
            }, function(response) {
                let data = {
                    userID: response.id,
                    Name: response.name,
                    Url: response.picture.data.url,
                    AppId: localStorage.getItem("appid"),
                    accessToken: accessToken,
                    timeEnd: Date.now() +(fbAuthResponse.expiresIn*1000)
                }
                fbAuthResponse.userLogin=data;
                sessionStorage.setItem("fbAuthResponse", JSON.stringify(fbAuthResponse));              
                SaveAccount(data);
            });
        }
      
    </script>
<?php
}

?>