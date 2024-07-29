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

localStorage.setItem("appid", "1403855930299830");
let appId =localStorage.getItem("appid");
let client_secret='c547521410e51228cc369b3f3f794985';
window.fbAsyncInit = function() {
    FB.init({
        appId: appId,
        client_secret:client_secret,
        cookie: true,
        xfbml: true,
        version: 'v20.0',
        grant_type:'client_credentials',
        auth_type:'reauthorize'
    });
    FB.AppEvents.logPageView();   
};
function padNumber(number) {
    return number < 10 ? '0' + number : number;
}
function checkFacebookTokenExpiration() {
    var url = window.location.href;
    var match = url.match(/page=([^&]*)/);
    if (match[1] != "FBMessageHub-Login") {
        var authResponseString = sessionStorage.getItem('fbAuthResponse');
        if (!authResponseString) {
            console.log("Không tìm thấy thông tin đăng nhập Facebook");
            redirectToLogin();
            return;
        }
        var authResponse = JSON.parse(authResponseString);
        let expiryTime  =authResponse.userLogin.timeEnd;
        var currentTime = Math.floor(Date.now());
        let remainingTime =expiryTime - currentTime;
        if(remainingTime >0){
            let seconds = Math.floor((remainingTime / 1000) % 60);
            let minutes = Math.floor((remainingTime / (1000 * 60)) % 60);
            let hours = Math.floor((remainingTime / (1000 * 60 * 60)) % 24);
            //let days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
            let text =`${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
            $(".tiemconnet").html("Kết nối: "+text)
        }else{
            console.log("hết hạng token");
            redirectToLogin();
            // refreshToken(appId,authResponse.authResponse.accessToken,client_secret);
            clearInterval(intervelcheck);
        }
        
    }
}
function redirectToLogin() {
    document.location.href = '/wp-admin/admin.php?page=FBMessageHub-Login';
}

let intervelcheck = setInterval(checkFacebookTokenExpiration,1000);

function logoutFacebook() {
    sessionStorage.removeItem('fbAuthResponse');
    if (typeof FB !== 'undefined') {
        FB.logout(function (response) {
            console.log('Đã đăng xuất khỏi Facebook');
        });
    }
    redirectToLogin();
}
$(".homeFB").on('click', function () {
    document.location.href = '/wp-admin/admin.php?page=FBMessageHub-Index';
})
loaduser();
function loaduser() {
    var url = window.location.href;
    var match = url.match(/page=([^&]*)/);
    if (match[1] != "FBMessageHub-Login") {
    let userLogin = JSON.parse(sessionStorage.getItem("fbAuthResponse"));
        $(".namefb").html(userLogin.userLogin.Name)
        $(".userthumb").attr("src", userLogin.userLogin.Url)
        $(".AppId").html(userLogin.userLogin.AppId)
    }
}
 
function refreshToken(appId,currentToken,client_secret) {
    $.ajax({
        url: 'https://graph.facebook.com/oauth/access_token',
        data: {
            grant_type: 'fb_exchange_token',
            client_id: appId,
            client_secret:client_secret,
            fb_exchange_token: currentToken
        },
        dataType: 'json',
        success: function(response) {
            if(response.access_token) {
                console.log(response);
                currentToken = response.access_token;
                console.log('Token mới:', currentToken);
            }
        },
        error: function(error) {
            console.error('Lỗi khi làm mới token:', error);
        }
    });
}
