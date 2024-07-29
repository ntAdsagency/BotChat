<?php

function facebook_index_page()
{

?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link  rel="stylesheet" href="<?php echo plugins_url('assets/css/style.css', __DIR__); ?>">
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
                        <img class="userthumb" src="" alt="User">
                        <span class="namefb"></span>
                    </button>
                    <ul class="dropdown-menu setingmenu">
                        <li class="dropdown-item" onclick="logoutFacebook()"><span>Đăng Xuất</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mainindex">
            <div class="sidebarindex">
                <div class="search-bar">
                    <div class="contentsearch">
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="Tìm kiếm" aria-describedby="addon-wrapping">
                        </div>
                        <div class="col-4 inputconnet">
                            <button class="btn btn-outline-primary">Kết nối</button>
                            <button class="btn btn-outline-primary">Gộp page</button>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="active domaincount btn btn-outline-primary"></button>
                    <button class="btn btn-outline-primary">Facebook (<span class="countdomain"></span>)</button>
                    <button class="Connettoken btn btn-outline-primary">Reconnect Fanpage</button>
                </div>
            </div>
            <div class="content">
                <div class="card-container">

                </div>
            </div>
        </div>
    </section>

   

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
    <script src="<?php echo plugins_url('assets/js/mainfacebook.js', __DIR__); ?>"></script>
    <script>
        let userLogin = JSON.parse(sessionStorage.getItem("fbAuthResponse"));
        let admin_id;
        let accessToken;
        try {
            admin_id = userLogin.userLogin.userID;
            accessToken = userLogin.accessToken;
        } catch (ex) {
            if (userLogin.userLogin.AppId == null || accessToken == null) {
                window.location.href = '<?php echo admin_url('admin.php?page=FBMessageHub-Login'); ?>';
            }
        }

        // callfanpage(admin_id);
        // async function callfanpage(admin_id) {
        //     $.ajax({
        //         url: '<?php echo admin_url('admin-ajax.php'); ?>',
        //         type: 'GET',
        //         data: {
        //             action: 'get_fanpages',
        //             data:admin_id
        //         },
        //         success: function(response) {
        //             console.log("load fanpage", response)
        //             if (response.data.length <1) {
        //                 loadfanpage(localStorage.getItem("accessToken"));
        //             } else {
        //                 htmlpage(response.data);
        //             }
        //         },
        //         error: function(errorThrown) {
        //             console.log(errorThrown);
        //         }
        //     });
        // }
        async function savefanpage(datas) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'updateOrAdd_fanpages',
                    data: datas
                },
                success: function(response) {
                    console.log("save fapage", response);
                    htmlpage(response.data);
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
        loadfanpage(accessToken);
        async function loadfanpage(accessToken) {
            let fanpage = [];
            try {
                let response = await fetch(`https://graph.facebook.com/v20.0/me/accounts?access_token=${accessToken}`);
                let data = await response.json();
                if (data.data) {
                    for (let page of data.data) {
                        let picResponse = await fetch(`https://graph.facebook.com/v20.0/${page.id}/picture?access_token=${accessToken}&redirect=false&type=large`);
                        let picData = await picResponse.json();
                        let pagePicUrl = picData.data.url;
                        let fanpageObject = {
                            id: page.id,
                            name: page.name,
                            accessToken: page.access_token,
                            pictureUrl: pagePicUrl,
                            admin_id: admin_id,
                            check: false
                        };
                        fanpage.push(fanpageObject);
                    }
                    savefanpage(fanpage);
                }  
            } catch (error) {
                console.error('Error:', error);
                document.querySelector('.card-container').innerHTML = '<p>Error loading fanpages.</p>';
            }
        }

        function htmlpage(data) {
            let fanpage = [];
            let countdomain = 0;
            let pages = '';
            for (let page of data) {
                pages += `
                            <div class="card shadow-sm bg-body-tertiary rounded col-lg-4 col-md-6 col-sm-12" onclick="detailFanpage('${page.page_id}', '${page.accessToken}', '${page.fanpage_name}', '${page.thumb}')">
                                <div class="viewfanpage" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 4px; margin-right: 20px;">
                                    <img src="${page.thumb}" style="width: 50px;" />
                                </div>
                                <div>
                                    <h3>${page.fanpage_name}</h3>
                                    <p>Facebook: ${page.page_id}</p>
                                </div>
                            </div>`;
                countdomain++;

                let fanpageObject = {
                    id: page.page_id,
                    name: page.fanpage_name,
                    accessToken: page.accessToken,
                    pictureUrl: page.thumb,
                    check: false
                };
                fanpage.push(fanpageObject);
            }
            localStorage.setItem("listFanpage", JSON.stringify(fanpage));
            $('.card-container').html(pages);
            $('.domaincount').text('Tất cả (' + countdomain + ')');
            $('.countdomain').text(countdomain);

        }

        function detailFanpage(page_id, accessToken, fanpage_name, thumb) {
            var listFanpage = JSON.parse(localStorage.getItem("listFanpage"));
            $.each(listFanpage, function(key, item) {
                item.check == false;
                if (item.id == page_id)
                    item.check = true;
            })
            localStorage.setItem("listFanpage", JSON.stringify(listFanpage));
            window.location.href = '<?php echo admin_url('admin.php?page=FBMessageHub-fanpage'); ?>';
        }
        $(".Connettoken").on('click', function() {
            loadfanpage(localStorage.getItem("accessToken"));
            location.reload();
        })
    </script>
    </div>
<?php } ?>