<?php
$image_url = plugins_url('assets/image.jpg', __FILE__);
function Fanpage()
{
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php echo plugins_url('assets/css/style.css', __DIR__); ?>">
    <section class="fanpage shadow-lg bg-body-tertiary rounded">
        <div class="header">
            <div>
                <h4 class="homeFB">FBMessageHub</h4>
                <p>AppId:<span class="AppId"></span> <span class="tiemconnet">Kết nối:</span></p>
                <div style="display:none" class="search">
                    <input type="text" placeholder="Tìm kiếm">
                </div>
            </div>
            <div class="user">
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <img class="pagethumb" src="" alt="User"><span class="pagename"></span>
                    </button>
                    <ul class="dropdown-menu listFanpagemenu">
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <img class="userthumb" src="" alt="User">
                        <span class="namefb"></span>
                    </button>
                    <ul class="dropdown-menu setingmenu">
                        <li class="dropdown-item" onclick="logoutFacebook()"><span>Đăng Xuất</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="containerFanpage">
            <div class="sidebar">
                <div class="sidebarcontext">
                    <div class="menufanpage">
                        <div class="menu-item" data-check="1"><i class="bi bi-chat-left-text"></i></div>
                        <div class="menu-item" data-check="2"><i class="bi bi-person-circle"></i></div>
                        <div class="menu-item" data-check="3"><i class="bi bi-chat"></i></div>
                        <div class="menu-item" data-check="4"><i class="bi bi-envelope"></i></div>
                        <div class="menu-item" data-check="5"><i class="bi bi-gear"></i></div>
                    </div>
                </div>
            </div>
            <div class="main">
                <div class="content" style="height: 100%;" data-check="1">
                    <?php include plugin_dir_path(__FILE__) . './component/mes.php'; ?>
                </div>
                <div class="content spm-content-section" style="display:none" data-check="2">
                    <?php include plugin_dir_path(__FILE__) . './component/post.php'; ?>
                </div>
                <div class="content" style="display:none" data-check="3">
                    conten3
                </div>
                <div class="content" style="display:none" data-check="4">
                    conten4
                </div>
                <div class="content" style="display:none" data-check="5">
                    conten5
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://connect.facebook.net/en_US/sdk.js"></script>
    <script src="<?php echo plugins_url('assets/js/mainfacebook.js', __DIR__); ?>"></script>
    <script>
        let userIds = [];
        var htmlfanpage = '';
        let listFanpage = JSON.parse(localStorage.getItem("listFanpage"));
        let page;
        var menu_items = $(".content");
        $.each(listFanpage, function(key, item) {
            let cla = '';
            if (item.check) {
                page = item;
                cla = 'disabled';
            }
            htmlfanpage += `<li class="dropdown-item ${cla}" onclick="detailFanpage('${item.id}')"><img class="" src="${item.pictureUrl}" alt="${item.name}"><span>${item.name}</span></li>`;
            $(".listFanpagemenu").html(htmlfanpage);
        })
        $(".pagethumb").attr("src", page.pictureUrl);
        $(".pagename").text(page.name);

        checkmenuload(1);
        get_product();

        function checkmenuload(data_id) {
            $.each(menu_items, function(key, item) {
                if ($(item).attr("data-check") == data_id) {
                    $(item).css("display", "block");
                } else {
                    $(item).css("display", "none");
                }
            })
        }


        $(".menu-item").on("click", function() {
            var data_id = $(this).attr("data-check");
            checkmenuload(data_id);
        })

        window.fbAsyncInit = function() {
            FB.init({
                appId: localStorage.getItem("appid"),
                cookie: true,
                xfbml: true,
                version: 'v12.0'
            });
            FB.AppEvents.logPageView();
        };
        $(".productcontroll").on('click', function() {
            $(".mescontroll").css("display", "block");
            $(".productcontroll").css("display", "none");
            $(".product-group").css("display", "block");
            $(".input-group").css("display", "none");
        })
        $(".mescontroll").on('click', function() {
            $(".mescontroll").css("display", "none");
            $(".productcontroll").css("display", "block");
            $(".product-group").css("display", "none");
            $(".input-group").css("display", "flex");
        })

        var appid = localStorage.getItem("appid");
        var Storage = JSON.parse(sessionStorage.getItem("fbssls_" + appid + ""));
        
        // var eventSource = new EventSource('/wp-json/webhook-handler/v1/sse');
        // eventSource.onmessage = function(event) {
        //     //var webhookData = JSON.parse(event.data);
        //     //console.log(event.data);
        //     if (event.data != '') {
        //         var jsonString = event.data;

        //         // Tách các đối tượng JSON từ chuỗi
        //         var jsonObjects = jsonString.match(/({.*?}(?=\{|$))/g);


        //         // Lặp qua từng đối tượng JSON và xử lý
        //         jsonObjects.forEach(function(jsonObject) {
        //             var data = JSON.parse(jsonObject);

        //             // Trích xuất thông tin từ dữ liệu và hiển thị lên HTML
        //             var object = data.object;
        //             var entry = data.entry[0];
        //             var time = entry.time;
        //             var id = entry.id;
        //             var messaging = entry.messaging[0];
        //             var senderId = messaging.sender.id;
        //             var recipientId = messaging.recipient.id;
        //             var timestamp = messaging.timestamp;
        //             var messageId = messaging.message.mid;
        //             var messageText = messaging.message.text;
        //             // Tạo HTML để hiển thị thông tin
        //             htmlContent += '<p><strong>Object:</strong> ' + object + '</p>' +
        //                 '<p><strong>Time:</strong> ' + time + '</p>' +
        //                 '<p><strong>ID:</strong> ' + id + '</p>' +
        //                 '<p><strong>Sender ID:</strong> ' + senderId + '</p>' +
        //                 '<p><strong>Recipient ID:</strong> ' + recipientId + '</p>' +
        //                 '<p><strong>Timestamp:</strong> ' + timestamp + '</p>' +
        //                 '<p><strong>Message ID:</strong> ' + messageId + '</p>' +
        //                 '<p><strong>Message Text:</strong> ' + messageText + '</p>';
        //             console.log(htmlContent);
        //         });
        //     }
        // };
        // eventSource.onerror = function() {
        //     console.error('Error occurred while connecting to SSE.');
        // };

        function detailFanpage(page_id) {
            var listFanpage = JSON.parse(localStorage.getItem("listFanpage"));
            $.each(listFanpage, function(key, item) {
                item.check = false;
                console.log(item.id, page_id)
                if (item.id == page_id)
                    item.check = true;
            })
            localStorage.setItem("listFanpage", JSON.stringify(listFanpage));
            location.reload();
        }

        function get_product() {
            let hostname = location.hostname;
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'get',
                data: {
                    action: 'get_list_products',
                },
                success: function(response) {
                    let html = ``;
                    $.each(response.data, function(key, item) {
                        html += ` 
                                <div class="col-md-4">
                                    <div class="card">
                                        <img style="max-height: 100px;" src="https://` + hostname + `/wp-content/uploads/` + item.url + `" class="card-img-top" alt="${item.product_name}">
                                        <div class="card-body">
                                            <h5 class="card-title shorten-text" title="${item.product_name}">${item.product_name}</h5>
                                            <p class="card-text shorten-text" title="${item.product_description}">${item.product_description}</p>
                                            <p class="card-text"><strong>${item.product_price}</strong></p>
                                            <button href="#" onclick="SendMesProduct('${item.product_name}','${item.product_price}','${item.product_description}','https://` + hostname + `/product/` + item.post_name + `','https://` + hostname + `/wp-content/uploads/` + item.url + `')" class="btn btn-primary send-message">Gửi</button>
                                        </div>
                                    </div>
                                </div>`
                    })

                    $(".list-product").html(html);
                    $('.shorten-text').each(function() {
                        var text = $(this).text();
                        if (text.length > 15) {
                            $(this).text(text.substring(0, 15) + '...');
                        }
                    });
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
        async function getuserid(id) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'get',
                    data: {
                        action: 'get_user',
                        data: {
                            user_id: id,
                        }
                    },
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(errorThrown) {
                        reject(errorThrown);
                    }
                });
            })

        }
        var datatest = {
            username: "nguyen1",
            email: "nguyen1@gmail.com",
            phone_number: "0828932779",
            user_id: "1",
            url: "aaaaaaaaaa"
        }
        //updateuser(datatest);
        function updateuser(data) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    action: 'Update_UserFacebook',
                    data: data
                },
                success: function(response) {
                    if (response.data) {
                        //load data
                    } else {
                        //check facebook
                        // luu thong tin
                    }

                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        async function getConversations(pageid) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'get',
                    data: {
                        action: 'get_Conversations',
                        data: {
                            pageid: pageid,
                        }
                    },
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(errorThrown) {
                        reject(errorThrown);
                    }
                });
            })
        }
    </script>
    <script src="<?php echo plugins_url('assets/js/mes.js', __DIR__); ?>"></script>
<?php
}

?>