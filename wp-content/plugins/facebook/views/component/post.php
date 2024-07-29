<div class="mescontent">
    <div class="container">
        <div id="facebook-posts-container" style="display:flex;flex-wrap: wrap;overflow-y: auto; height: 100%;"> 
        </div>

    </div>
    <div style="display:none">
        <div class="contact-list users-container col-2">
        </div>
        <div class="chat col-8">
            <div class="chat-header">
            </div>
            <div class="chat-body">
                <h3 class="checkmess"><i class="bi bi-chat-left-text"></i>Xin chọn 1 hội thoại từ danh sách bên trái</h3>
            </div>
            <div class="chat-footer" style="display: none;">
                <div class="controllmess">
                    <button class="mescontroll btn btn-outline-danger" style="display: none;"><i class="bi bi-chat-left-text"></i></button>
                    <button class="productcontroll btn btn-outline-primary" style="display: block;"><i class="bi bi-bag-fill"></i></button>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control mesdata" placeholder="Nhập nội dung tin nhắn" aria-label="Nhập nội dung tin nhắn" aria-describedby="basic-addon1">
                    <span class="input-group-text sendMessage" style="color: #3974ee;" id="basic-addon1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                            <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"></path>
                        </svg>
                    </span>
                </div>
                <div class="product-group" style="display: none;">
                    <h3>Danh sách sản phẩm của bạn</h3>
                    <div class="container mt-5">
                        <div class="row list-product">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-panel col-2">
            <div class="tabs">
                <div class="tab active">Thông tin</div>
                <div class="tab">Tạo đơn</div>
            </div>
            <div class="info-content">
                <div class="no-notes">Chưa có ghi chú</div>
                <div class="orders">Đơn hàng (0)</div>
                <div class="no-orders">Chưa có đơn hàng nào được tạo</div>
            </div>
        </div>
    </div>
</div>





<script>
    function post1() {
        console.log(page.accessToken);
        getFacebookPosts(page.id, page.accessToken);
    }

    function getFacebookPosts(pageId, accessToken) {
        $.ajax({
            url: `https://graph.facebook.com/${pageId}?fields=posts{message,attachments{media,url,title}}`,
            type: 'GET',
            data: {
                access_token: accessToken
            },
            success: function(response) {
                console.log(response);
                var data = response.posts.data;
                var attachments=``;
                $.each(data, function(key, item) {
                    if(item.attachments){
                        $.each(item.attachments.data,function(key1,item1){
                            if(item1.media.image){
                                attachments+=`<img src="${item1.media.image.src}" alt="Post Image" class="img-fluid rounded">`;                               
                            }
                        })
                    }

                    var postHtml = `
                        <div class="card mb-4"  style="overflow-y: auto; max-height: 200px;">
                            <div class="card-body">
                                <div class="post-header d-flex align-items-center">
                                    <img src="${page.pictureUrl}" alt="${page.name}" class="profile-picture rounded-circle mr-3">
                                    <div class="post-info">
                                        <h5 class="page-name mb-0">${page.name}</h5>
                                        <small class="text-muted">Posted on: ${item.created_time}</small>
                                    </div>
                                </div>
                                <div class="post-content mt-3">
                                    <p>${item.message}</p>
                                    <div style="display:flex">
                                      `+attachments+`  
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#facebook-posts-container').append(postHtml);
                })
            },
            error: function(error) {
                console.error('Error fetching posts:', error);
            }
        });
    }

    // function loadcoment() {
    //     FB.api('/me/accounts', {
    //         access_token: page.accessToken,
    //     }, function(response) {
    //         if (response.data) {
    //             let pages = '';
    //             response.data.forEach(page => {
    //                 console.log(page);
    //                 FB.api(`/${page.id}/picture`, {
    //                     access_token: accessToken,
    //                     redirect: false,
    //                     type: 'large'
    //                 }, function(picResponse) {
    //                     localStorage.setItem("login", true)
    //                     let pagePicUrl = picResponse.data.url;
    //                     pages += ` <div class="list-link col-lg-6 col-md-4 col-sm-12" data-id="` + page.id + `" data-token="` + page.access_token + `">
    //                                          <div class="imgfanpage"><img src="` + pagePicUrl + `" /></div>
    //                                          <div>
    //                                              <p>` + page.name + `</p>
    //                                              <p>ID: <span>` + page.id + `</span></p>
    //                                              <button onclick="loadMessages('` + page.id + `','` + page.access_token + `')">Xem tin nhắn</button>
    //                                          </div>
    //                                      </div>`;

    //                     // Update the list after each picture is fetched
    //                     $('.list').html(pages);
    //                     console.log(pages);
    //                 });
    //             });
    //         } else {
    //             $('.list').html('<p>No fanpages found.</p>');
    //         }
    //     });

    // }
</script>