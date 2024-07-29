function checkLoginState() {
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
}

function statusChangeCallback(response) {
    if (response.status === 'connected') {
        console.log(response);
        document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.authResponse.userID + '!';
        getUserInfo(response.authResponse.accessToken);
    } else {

        document.getElementById('status').innerHTML = 'Please log into this app.';
    }
}

function getUserInfo(accessToken) {
    FB.api('/me', {
        fields: 'id,name,picture',
        access_token: accessToken
    }, function(response) {
        document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';
        document.getElementById('user-info').innerHTML = '<img src="' + response.picture.data.url + '" /><br>' + 'Name: ' + response.name;
        localStorage.setItem("accessToken", accessToken)
        $(".loginfacebook").css("display", "none");
        FB.api('/me/accounts', {
            access_token: accessToken
        }, function(response) {
            if (response.data) {
                let pages = '';
                response.data.forEach(page => {
                    console.log(page);
                    FB.api(`/${page.id}/picture`, {
                        access_token: accessToken,
                        redirect: false,
                        type: 'large'
                    }, function(picResponse) {
                        localStorage.setItem("login", true)
                        let pagePicUrl = picResponse.data.url;
                        pages += ` <div class="list-link col-lg-6 col-md-4 col-sm-12" data-id="` + page.id + `" data-token="` + page.access_token + `">
                                         <div class="imgfanpage"><img src="` + pagePicUrl + `" /></div>
                                         <div>
                                             <p>` + page.name + `</p>
                                             <p>ID: <span>` + page.id + `</span></p>
                                             <button onclick="loadMessages('` + page.id + `','` + page.access_token + `')">Xem tin nhắn</button>
                                         </div>
                                     </div>`;

                        // Update the list after each picture is fetched
                        $('.list').html(pages);
                    });
                });
            } else {
                $('.list').html('<p>No fanpages found.</p>');
            }
        });
    });
}
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

function loadMessages(id, token) {
    var pageId = id; 
    var accessToken =localStorage.getItem("accessToken");  
    var apiEndpoint = 'https://graph.facebook.com/v20.0/' + pageId + '/posts';
    var params = {
        access_token: accessToken,
        fields: 'id,message,comments.limit(10){id,message}', 
        limit: 10  
    };
    $.ajax({
        url: apiEndpoint,
        type: 'GET',
        dataType: 'json',
        data: params,
        success: function(response) {       
            console.log(response);           
            var posts = response.data;
            var html = '';
            posts.forEach(function(post) {
                html += '<div>';
                html += '<p><strong>Post ID:</strong> ' + post.id + '</p>';
                html += '<p><strong>Message:</strong> ' + (post.message || 'No message') + '</p>';
                if (post.comments && post.comments.data.length > 0) {
                    html += '<ul>';
                    post.comments.data.forEach(function(comment) {
                        html += '<li><strong>Comment ID:</strong> ' + comment.id + '</li>';
                        html += '<li><strong>Message:</strong> ' + (comment.message || 'No message') + '</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p>No comments</p>';
                }

                html += '</div>';
            });

            $('#posts').html(html);
        },
        error: function(error) {
            console.error('Error fetching data:', error);
            $('#posts').html('<p>Error fetching data</p>');
        }
    });
}

 
$('#get-items-button').on('click', function(e) {  
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'my_plugin_get_items'
        },
        success: function(response) {
            console.log(response);
            // Xử lý và hiển thị dữ liệu
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
});
 
// Thêm mục mới
$('#add-item-button').on('click', function(e) {
    e.preventDefault();

    var data = {
        action: 'my_plugin_add_item',
        data: {
            // Dữ liệu mục mới
        }
    };

    $.ajax({
        url: my_ajax_object.ajax_url,
        type: 'POST',
        data: data,
        success: function(response) {
            console.log(response);
            // Xử lý sau khi thêm mục mới
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
});

// Cập nhật mục
$('#update-item-button').on('click', function(e) {
    e.preventDefault();

    var item_id = 1; // ID của mục cần cập nhật
    var data = {
        action: 'my_plugin_update_item',
        item_id: item_id,
        data: {
            // Dữ liệu cập nhật
        }
    };

    $.ajax({
        url: my_ajax_object.ajax_url,
        type: 'POST',
        data: data,
        success: function(response) {
            console.log(response);
            // Xử lý sau khi cập nhật
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
});

// Xóa mục
$('#delete-item-button').on('click', function(e) {
    e.preventDefault();

    var item_id = 1; // ID của mục cần xóa
    var data = {
        action: 'my_plugin_delete_item',
        item_id: item_id
    };

    $.ajax({
        url: my_ajax_object.ajax_url,
        type: 'POST',
        data: data,
        success: function(response) {
            console.log(response);
            // Xử lý sau khi xóa
        },
        error: function(errorThrown) {
            console.log(errorThrown);
        }
    });
});
 
 

