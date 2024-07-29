let handledConversations = [];
let isLoading = false;
let hostname=`https://`+location.hostname+`/wp-admin/admin-ajax.php`;
let countmessages=0;
let scrollmess=false;
checkmes();
 
async function checkmes(){
    try {
        let data= await getConversations(page.id);
        if(!data.data.length){
            loadUserMes2(page.id, page.accessToken,null);
        }else{
            htmlUserMesDatabase(data);
        }
        let inter =setInterval(intervelnoti,4000);
    } catch (error) {
       console.log(error) ;
    }
}
function gettoken() {
    let lists = JSON.parse(localStorage.getItem("listFanpage"));
    let selectedItem = undefined;
    $.each(lists, function(key, item) {
        if (item.check) {
            selectedItem = item;
            return false; 
        }
    });
    return selectedItem;
}
function intervelnoti () {
    console.log("check");
    var userClickMes = JSON.parse(localStorage.getItem('userClickMes'));
    var conversationId = userClickMes.conversationId;
    var userId = userClickMes.userId;
    getNewMessages(page.id, page.accessToken, conversationId, userId);
}
//notifycation
async function getNewMessages(pageId, pageAccessToken, conversationId, userId) {
    let convertationB = [];
    $(".conversation").each(function() {
        convertationB.push({
            id: $(this).attr("data-conver"),
            mescount: $(this).attr("data-count")
        });
    });
    let idMap = new Map(convertationB.map(item => [item.id, item]));
   
    var api=`https://graph.facebook.com/v20.0/${pageId}/conversations?fields=updated_time,message_count,unread_count,messages.limit(1){message,attachments,from,created_time},participants&access_token=${pageAccessToken}`;
    try {
        let response = await fetch(api);
        let data = await response.json();
        if (data.data) {
            for (let conversation of data.data) {
                let matchedItem = idMap.get(conversation.id);
                if (matchedItem) {
                   if(matchedItem.mescount!=conversation.message_count){
                       htmluserOneMes(conversation,pageId);
                       var userClickMes = JSON.parse(localStorage.getItem('userClickMes'));
                       if(userClickMes.conversationId==conversation.id){
                            var mesis= conversation.message_count-matchedItem.mescount;
                            if(mesis==1){
                                htmlmess(conversation.messages.data,userClickMes.userId,userClickMes.conversationId,false);
                            }else{
                                getMesfacebook(userClickMes.conversationId,pageAccessToken,mesis,userClickMes.userId,true);
                            }
                           
                       }
                   }
                }
                else{
                    htmluserOneMes(conversation,pageId);
                }
              
                
            }
        }
    } catch (error) {
        console.error('Error:', error);
        document.querySelector('.messages-container').innerHTML = '<p>Error loading conversations.</p>';
    }
}





async function getConversations(pageid) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: hostname,
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

async function Update_Conversations(data){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: hostname,
            type: 'post',
            data: {
                action: 'Update_Conversations',
                data: data
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
// scroll user
var $scrollableDiv = $('.users-container');
var $scrollchat = $('.chat-content');
$scrollableDiv.scroll(function() {
    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 50) {
        if(!isLoading){
            isLoading =true;
            var fbAuthResponse = JSON.parse(sessionStorage.getItem("fbAuthResponse"));
            loadUserMes2(page.id, page.accessToken,fbAuthResponse.next);
        }
    }
});
// scroll mes
$('.chat-content').scroll(function() {
    if ($(this).scrollTop() ===0) {
        let user = JSON.parse(localStorage.getItem("userClickMes"));
        console.log("chayj vo day ",scrollmess)
        if(!scrollmess){
            if(user.mesload<user.message_count){
                scrollmess=true;
                let page =gettoken();
                getMesfacebook(user.conversationId,page.accessToken,user.message_count-user.mesload,user.userId,false);
            } 
        }
    }
});
 
function truncateText(text, maxlen) {
    if (text.length > maxlen) {
        return text.substring(0, maxlen) + '...';
    } else {
        return text;
    }
}
$(".fanpage").click(function () {
    if (localStorage.getItem("userClickMes")) {
        $(".chat-footer").css("display", "flex");
    } else {
        $(".chat-footer").css("display", "none");
    }
})
$(".sendMessage").on('click', function () {
    sendMessage();
})

// usercall mes
// call mess
function htmlmess(datames, userId,conversationid,inhtml) {
    let dataarr=[];
    let page=gettoken();
    scrollmess =true;
    if(inhtml==false){
        datames=datames.reverse();
    }
    $(".chat-load").css("display","block");
    $.each(datames, function (key, item) {
        let user = JSON.parse(localStorage.getItem("userClickMes"));
        user.mesload+=1;
        localStorage.setItem("userClickMes", JSON.stringify(user));
        console.log("load 1",user.mesload);
        let message =null;
        let attachments=null;
        let recipient_id=null;
        var htmlmes = ``;
        var date = new Date(item.created_time);
        var formattedDate = date.toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
        var formattedTime = date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        if (item.attachments) {
            attachments=JSON.stringify(item.attachments.data);
            var mes = ``;
            var meitem = item.attachments.data;
            if (meitem[0].generic_template) {
                var generric = meitem[0].generic_template;
                let hrefbutton = ``;
                $.each(generric.cta, function (key, itemgen) {
                    hrefbutton += `<a href="${itemgen.url}" class="btn btn-primary" style="width: 100%;" target="_blank">${itemgen.title}</a>`
                })
                mes += `<img src="${generric.media_url}" alt="${generric.title}"  style="width: 100%;"/>
                            <h4 style="width: 100%;" >${generric.title}</h4>
                            <p style="width: 100%;" >${generric.subtitle}</p>
                            ${hrefbutton}
                        `;
            } else {
                $.each(meitem, function (key, item2) {
                    if (item2.mime_type == "image/jpeg") {
                        mes += `<img src="${item2.image_data.url}" alt="${item2.name}" style="width: 100%;" />`;
                    }
                    if (item2.mime_type == "video/mp4") {
                        mes += `<video style="width: 100%;"  src="${item2.video_data.url}" type="video/mp4" alt="${item2.name}"></video> `;
                    }
                })
            }
            if (item.from.id == userId) {
                recipient_id=page.id;
                htmlmes += `<div class="message messages-container">                   
                            <div class="message-contentproduct">${mes} </div>
                            <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                        </div>
                        `
            } else {
                recipient_id=userId;
                htmlmes += `<div class="message sent">
                             <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                             <div class="message-contentproduct">${mes} </div>
                        </div>
                        `
            }
          
        }
        if (item.from.id == userId) {
            recipient_id=page.id;
        } else {
            recipient_id=userId;
        }
        if (item.message) {
            message=item.message;
            if (item.from.id == userId) {
                htmlmes += `<div class="message messages-container">                   
                            <p class="message-content">${item.message} </p>
                            <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                        </div>
                        `
            } else {
                htmlmes += `<div class="message sent">
                             <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                            <p class="message-content">${item.message}</p>
                        </div>
                        `
            }
        }
        let mesdata={
            id:item.id,
            sender_id:item.from.id, 
            recipient_id:recipient_id, 
            message:message, 
            attachments:attachments, 
            created_time:formatDateToMySQL(date), 
            conversationid:conversationid,        
        }
        dataarr.push(mesdata);
        var chatBody = $('.chat-content');
        chatBody.append(htmlmes);
        $('.chat-content').scrollTop($('.chat-content')[0].scrollHeight);
    })
    try {
        console.log("up",dataarr);
       Update_Messages(dataarr);
       scrollmess=false;
       setTimeout(
        ()=>{
            $(".chat-load").css("display","none");
        },500
       )
      
    } catch (error) {
        console.log(error)
    }

}
function htmlmessDatabase(datames,userId) {
    $(".chat-load").css("display","block");
    $.each(datames, function (key, item) {
        try {
            let user = JSON.parse(localStorage.getItem("userClickMes"));
            user.mesload+=1;
            localStorage.setItem("userClickMes", JSON.stringify(user));
            console.log("load 2",user.mesload);
            var htmlmes = ``;
            var date = new Date(item.created_time);
            var formattedDate = date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
            var formattedTime = date.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            if (item.attachments!="") {
                var mes = ``;
                item.attachments=item.attachments.replace(/\\\\/g, '\\').replace(/\\"/g, '"');
                let meitem= $.parseJSON(item.attachments);
                if (meitem.generic_template) {
                    var generric = meitem.generic_template;
                    let hrefbutton = ``;
                    $.each(generric.cta, function (key, itemgen) {
                        hrefbutton += `<a href="${itemgen.url}" class="btn btn-primary" style="width: 100%;" target="_blank">${itemgen.title}</a>`
                    })
                    mes += `<img src="${generric.media_url}" alt="${generric.title}"  style="width: 100%;"/>
                                <h4 style="width: 100%;" >${generric.title}</h4>
                                <p style="width: 100%;" >${generric.subtitle}</p>
                                ${hrefbutton}`;
                } else {
                    $.each(meitem, function (key, item2) {
                        if (item2.mime_type == "image/jpeg") {
                            mes += `<img src="${item2.image_data.url}" alt="${item2.name}" style="width: 100%;" />`;
                        }
                        if (item2.mime_type == "video/mp4") {
                            mes += `<video style="width: 100%;"  src="${item2.video_data.url}" type="video/mp4" alt="${item2.name}"></video> `;
                        }
                    })
                }
              
                if (item.sender_id == userId) {
                    htmlmes += `<div class="message messages-container">                   
                                <div class="message-contentproduct">${mes} </div>
                                <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                            </div>
                            `
                } else {
                    htmlmes += `<div class="message sent">
                                 <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                                 <div class="message-contentproduct">${mes} </div>
                            </div>
                            `
                }
            }
            if (item.message) {
                message=item.message;
                if (item.sender_id== userId) {
                    htmlmes += `<div class="message messages-container">                   
                                <p class="message-content">${item.message} </p>
                                <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                            </div>
                            `
                } else {
                    htmlmes += `<div class="message sent">
                                 <p class="message-time" >Ngày: ${formattedDate},Giờ: ${formattedTime}</p> 
                                <p class="message-content">${item.message}</p>
                            </div>
                            `
                }
            }
            var chatBody = $('.chat-content');
            localStorage.setItem("userClickMes", JSON.stringify(user));
            chatBody.append(htmlmes); 
            $('.chat-content').scrollTop($('.chat-content')[0].scrollHeight);
                       
        } catch (error) {
            console.log(error)
        }
       
    })
    $(".chat-load").css("display","none");
}

async function getMesfacebook(conversationId, accessToken,countnewmes,userId,inhtml) {
    let url = `https://graph.facebook.com/v12.0/${conversationId}/messages?fields=attachments,message,from,created_time&access_token=${accessToken}`;
    if(countnewmes){
        url = `https://graph.facebook.com/v12.0/${conversationId}/messages?fields=attachments,message,from,created_time&limit=${countnewmes}&access_token=${accessToken}`;
    }
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (data.data) {
            htmlmess(data.data,userId,conversationId,inhtml);
        } else {
            console.error('Error fetching messages:', data.error);
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

async function getMessages(conversationid) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: hostname,
            type: 'get',
            data: {
                action: 'get_Messages',
                data: {
                    conversationid: conversationid,
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
async function callMestoConversation(conversationId,accessToken,countnewmes){
    try {
        scrollmess= true;
        let mesuser = await getMessages(conversationId);
        let user= JSON.parse(localStorage.getItem("userClickMes"));
        console.log(mesuser.data.length);
        if(mesuser.data.length){
            htmlmessDatabase(mesuser.data,user.userId);
            console.log(" co mess")
            if(mesuser.data.length<countnewmes){
                getMesfacebook(conversationId,accessToken,countnewmes-mesuser.data.length,user.userId,false);
                
            }
        }else{
            console.log(" ko co mess")
           getMesfacebook(conversationId, accessToken,null,user.userId,true);
        }
        scrollmess= false;
    } catch (error) {
        console.log(error)
    }
 
}
async function Update_Messages(data){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: hostname,
            type: 'post',
            data: {
                action: 'Update_Messages',
                data: data
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
function userClickMes(conversationId, userId, name, url,unread_count,message_count){
    
    let page =gettoken(); 
    let data = {
        conversationId: conversationId,
        userId: userId,
        name: name,
        url: name,
        unread_count:unread_count,
        message_count:message_count,
        mesload:0
    }
    localStorage.setItem("userClickMes", JSON.stringify(data));
    let html = `
                <img class="chat-img" src="${url}" alt="${name}">
                <span class="chat-name">${name}</span>
                `;
    $(".chat-header").html(html);
    $(".contact").removeClass("active");
    $(".user" + userId).addClass("active");
    $('.checkmess').css("display","none");
    $('.chat-load').css("display","block");
    $('.chat-content').html('');
    scrollmess =true;
    var countnew = parseInt($(".user"+userId).attr("data-count"), 10);
    callMestoConversation(conversationId,page.accessToken,countnew);

}

function htmlUserMesDatabase(data){
    for (let conversation of data.data) {
        let messageSendersHtml = '';
        try {
            if (conversation.conversationid) {
                let mestext = ``;
                let participants = JSON.parse(conversation.participants.replace(/\\/g, ''));
                let messages =JSON.parse(conversation.message.replace(/\\/g, ''));
                if (messages.message) {
                    mestext = `<span class="contact-last-message lastmes${conversation.conversationid}">` + truncateText(messages.message, 12) + `</span>
                                         <span class="countmes${conversation.conversationid}"></span>`;
                    if (conversation.unread_count > 0) {
                        mestext = `<span class="lastmes${conversation.conversationid}">` + truncateText(messages.message, 12) + `</span>
                                <span class="countmes${conversation.conversationid}">(${conversation.unread_count})</span> `;
                    }
                } else {
                    mestext = `<span class="contact-last-message lastmes${conversation.conversationid}">Nội dung đính kèm</span>
                                         <span class="countmes${conversation.conversationid}"></span>`;
                    if (conversation.unread_count > 0) {
                        mestext = `<span class="lastmes${conversation.conversationid}">Nội dung đính kèm</span>
                                <span class="countmes${conversation.conversationid}">(${conversation.unread_count})</span> `;
                    }
                }
              
                messageSendersHtml = `
                       <div class="contact user${participants.data[0].id} conversation"  data-conver="${conversation.conversationid}"  data-countold="${conversation.message_count}"   data-count="${conversation.message_count}" onclick="userClickMes('${conversation.conversationid}',${participants.data[0].id},'${participants.data[0].name}','https://pancake.vn/api/v1/pages/${page.id}/avatar/${participants.data[0].id}',${conversation.unread_count},${conversation.message_count})">
                            <img src="" alt="${participants.data[0].name}" id="${participants.data[0].id}">
                            <div class="contact-info">
                                <span class="contact-name"> ` + truncateText(participants.data[0].name, 12) + `</span>
                                <div class="mescount">
                                ` + mestext + `                                                
                                </div>
                            </div>
                        </div>                                        
                        `;
                countmessages+=1;
                savestorage(countmessages)
                $('.users-container').append(messageSendersHtml);
            }
            
        } catch (error) {
            console.log(error)
        }
    }  
}
function savestorage(countmessages) {
    var fbAuthResponse = JSON.parse(sessionStorage.getItem("fbAuthResponse"));
    fbAuthResponse.next=countmessages;
    sessionStorage.setItem("fbAuthResponse", JSON.stringify(fbAuthResponse));
}
function savesMesClick(countmessages) {
    let user = JSON.parse(localStorage.getItem("userClickMes"));
    user.mesload=countmessages;
    localStorage.setItem("userClickMes", JSON.stringify(user));
}


async function htmlusermesNoti(data,pageId){
    $(".iconloadingUserMess").css("display","block");
    if (data.data) {
        let dataarr =[];
        for (let conversation of data.data) {
            let messageSendersHtml = '';
            if (conversation.id) {
                var conver={
                    pageid:pageId,
                    conversationid:conversation.id,
                    userid:conversation.participants.data[0].id,
                    url:'',
                    created_time: formatDateToMySQL(new Date(conversation.updated_time)),
                    participants: JSON.stringify(conversation.participants),
                    message:JSON.stringify(conversation.messages.data[0]),
                    message_count:conversation.message_count,
                    unread_count:conversation.unread_count
                }
                dataarr.push(conver);
                let mestext = ``;
                if (conversation.messages.data) {
                    var mesoption = conversation.messages.data[0];
                    if (mesoption.message) {
                        mestext = `<span class="contact-last-message lastmes${conversation.id}">` + truncateText(mesoption.message, 12) + `</span>
                                             <span class="countmes${conversation.id}"></span>`;
                        if (conversation.unread_count > 0) {
                            mestext = `<span class="lastmes${conversation.id}">` + truncateText(mesoption.message, 12) + `</span>
                                    <span class="countmes${conversation.id}">(${conversation.unread_count})</span> `;
                        }
                    } else {
                        mestext = `<span class="contact-last-message lastmes${conversation.id}">Nội dung đính kèm</span>
                                             <span class="countmes${conversation.id}"></span>`;
                        if (conversation.unread_count > 0) {
                            mestext = `<span class="lastmes${conversation.id}">Nội dung đính kèm</span>
                                    <span class="countmes${conversation.id}">(${conversation.unread_count})</span> `;
                        }
                    }
                }
                messageSendersHtml = `
                       <div class="contact user${conversation.participants.data[0].id} conversation"  data-conver="${conversation.id}" data-count="${conversation.message_count}"  onclick="userClickMes('${conversation.id}',${conversation.participants.data[0].id},'${conversation.participants.data[0].name}','https://pancake.vn/api/v1/pages/${page.id}/avatar/${conversation.participants.data[0].id}',${conversation.unread_count},${conversation.message_count})">
                            <img src="" alt="${conversation.participants.data[0].name}" id="${conversation.participants.data[0].id}">
                            <div class="contact-info">
                                <span class="contact-name"> ` + truncateText(conversation.participants.data[0].name, 12) + `</span>
                                <div class="mescount">
                                ` + mestext + `                                                
                                </div>
                            </div>
                        </div>                                        
                        `;
                countmessages+=1;
                savestorage(countmessages)
                $('.users-container').append(messageSendersHtml);
            }
        }
        console.log(dataarr);
        await Update_Conversations(dataarr);
    } else {
        document.querySelector('.messages-container').innerHTML = '<p>No conversations found.</p>';
    }
    setTimeout(function(){
        $(".iconloadingUserMess").css("display","none");
    },500);
    
}

function htmlloadFacebookmes(conversation,countold){
    let mestext = ``;
    if (conversation.messages.data) {
        var mesoption = conversation.messages.data[0];
        if (mesoption.message) {
            mestext = `<span class="contact-last-message lastmes${conversation.id}">` + truncateText(mesoption.message, 12) + `</span>
                                 <span class="countmes${conversation.id}"></span>`;
            if (conversation.unread_count > 0) {
                mestext = `<span class="lastmes${conversation.id}">` + truncateText(mesoption.message, 12) + `</span>
                        <span class="countmes${conversation.id}">(${conversation.unread_count})</span> `;
            }
        } else {
            mestext = `<span class="contact-last-message lastmes${conversation.id}">Nội dung đính kèm</span>
                                 <span class="countmes${conversation.id}"></span>`;
            if (conversation.unread_count > 0) {
                mestext = `<span class="lastmes${conversation.id}">Nội dung đính kèm</span>
                        <span class="countmes${conversation.id}">(${conversation.unread_count})</span> `;
            }
        }
    }
   return `
           <div class="contact user${conversation.participants.data[0].id} conversation" data-countold="${countold}"  data-conver="${conversation.id}" data-count="${conversation.message_count}"  onclick="userClickMes('${conversation.id}',${conversation.participants.data[0].id},'${conversation.participants.data[0].name}','https://pancake.vn/api/v1/pages/${page.id}/avatar/${conversation.participants.data[0].id}',${conversation.unread_count},${countold})">
                <img src="" alt="${conversation.participants.data[0].name}" id="${conversation.participants.data[0].id}">
                <div class="contact-info">
                    <span class="contact-name"> ` + truncateText(conversation.participants.data[0].name, 12) + `</span>
                    <div class="mescount">
                    ` + mestext + `                                                
                    </div>
                </div>
            </div>                                        
            `;
}

async function htmluserOneMes(conversation,pageId){
    $(".iconloadingUserMess").css("display","block");
    let dataarr =[];
    let messageSendersHtml = '';
    if (conversation.id) {
        var conver={
            pageid:pageId,
            conversationid:conversation.id,
            userid:conversation.participants.data[0].id,
            url:'',
            created_time: formatDateToMySQL(new Date(conversation.updated_time)),
            participants: JSON.stringify(conversation.participants),
            message:JSON.stringify(conversation.messages.data[0]),
            message_count:conversation.message_count,
            unread_count:conversation.unread_count
        }
        let converis = $(".conversation[data-conver='"+conversation.id+"']");
        let countold= converis.attr("data-countold");
        converis.css("display", "none");
        converis.empty();
        converis.remove();
        messageSendersHtml = htmlloadFacebookmes(conversation,countold);
        dataarr.push(conver);
        countmessages+=1;
        savestorage(countmessages)
        $('.users-container').prepend(messageSendersHtml);
    }
    await Update_Conversations(dataarr);
    setTimeout(function(){
        $(".iconloadingUserMess").css("display","none");
    },500);
    
}



async function htmlusermes(data,pageId){
    $(".iconloadingUserMess").css("display","block");
    if (data.data) {
        let dataarr =[];
        for (let conversation of data.data) {
            let messageSendersHtml = '';
            if (conversation.id) {
                var conver={
                    pageid:pageId,
                    conversationid:conversation.id,
                    userid:conversation.participants.data[0].id,
                    url:'',
                    created_time: formatDateToMySQL(new Date(conversation.updated_time)),
                    participants: JSON.stringify(conversation.participants),
                    message:JSON.stringify(conversation.messages.data[0]),
                    message_count:conversation.message_count,
                    unread_count:conversation.unread_count
                }
                messageSendersHtml = htmlloadFacebookmes(conversation,conversation.message_count);
                dataarr.push(conver);
                countmessages+=1;
                savestorage(countmessages)
                $('.users-container').append(messageSendersHtml);
            }
        }
        await Update_Conversations(dataarr);
    } else {
        document.querySelector('.messages-container').innerHTML = '<p>No conversations found.</p>';
    }
    setTimeout(function(){
        $(".iconloadingUserMess").css("display","none");
    },500);
}
async function loadUserMes2(pageId, pageAccessToken,countmessages) {
    var api=`https://graph.facebook.com/v20.0/${pageId}/conversations?fields=updated_time,message_count,unread_count,messages.limit(1){message,attachments,from,created_time},participants&access_token=${pageAccessToken}`;
    if(countmessages!=0){      
        var api=`https://graph.facebook.com/v20.0/${pageId}/conversations?fields=updated_time,message_count,unread_count,messages.limit(1){message,attachments,from,created_time},participants&pretty=${countmessages}&limit=${countmessages+25}&access_token=${pageAccessToken}`; 
    }
    try {
        console.log('&pretty=${'+countmessages+'}&limit=${'+countmessages+25+'}')
        let response = await fetch(api);
        let data = await response.json();
        htmlusermes(data,pageId);
        isLoading =false;
    } catch (error) {
        console.log(error.message);
    }  
}

function sendMessageWithAttachment(mediaType, attachmentId) {
    let message = $('.mesdata').val(); // Lấy nội dung tin nhắn từ input text
    let user = JSON.parse(localStorage.getItem("userClickMes"));
    let pageId = page.page_id;
    let pageAccessToken = page.accessToken;
    let messageData = {
        recipient: {
            id: user.userId
        },
        message: {
            attachment: {
                type: 'template',
                payload: {
                    template_type: 'media',
                    elements: [{
                        media_type: mediaType,
                        attachment_id: attachmentId
                    }]
                }
            }
        }
    };

    $.ajax({
        url: `https://graph.facebook.com/v20.0/${pageId}/messages?access_token=${pageAccessToken}`,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(messageData),
        success: function (response) {
            console.log(`Tin nhắn ${mediaType} đã được gửi thành công:`, response);
        },
        error: function (error) {
            console.error(`Có lỗi xảy ra khi gửi tin nhắn ${mediaType}:`, error.responseText);

        }
    });
}
$(".mesdata").keypress(function(event) {
    if (event.which === 13) {
      sendMessage();
    }
  });
async function sendMessage() {
    let page = gettoken();
    $(".chat-load").css("display","block");
    try {
        let user = JSON.parse(localStorage.getItem("userClickMes"));
        var mes = $(".mesdata").val();
        if(mes!=''){
            $(".mesdata").val('');
            let pageId = page.id;
            let pageAccessToken = page.accessToken;
            let recipientId = user.userId;
            let messageData = {
                recipient: {
                    id: recipientId
                },
                message: {
                    text: mes
                },
                tag: "CONFIRMED_EVENT_UPDATE"
            };
            $.ajax({
                url: `https://graph.facebook.com/v20.0/${pageId}/messages?access_token=${pageAccessToken}`,
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(messageData),
                success: function (response) {
                    console.log('Tin nhắn đã được gửi thành công:', response);
                },
                error: function (error) {
                    console.error('Có lỗi xảy ra khi gửi tin nhắn:', error.responseText);
                }
            });
        }

    } catch (error) {
        console.error('Có lỗi xảy ra khi gửi tin nhắn:', error.message);
    }
}

/// gui yeu cau san pham va mua hang
function SendMesProduct(name, price, description, post_name, url) {
    let page =gettoken();
    let user = JSON.parse(localStorage.getItem("userClickMes"));
    let pageId = page.id;
    var pageAccessToken = page.accessToken;
    var userId = user.userId;
    var imageUrl = url;
    var productUrl = post_name;

    var messageData = {
        recipient: {
            id: userId
        },
        message: {
            attachment: {
                type: "template",
                payload: {
                    template_type: "generic",
                    elements: [{
                        title: name,
                        image_url: imageUrl,
                        subtitle: description,
                        buttons: [{
                            type: "web_url",
                            url: productUrl,
                            title: "Mua Ngay"
                        },
                        {
                            type: "web_url",
                            url: productUrl,
                            title: "Giỏ hàng"
                        }
                        ]
                    }]
                }
            }
        }
    };

    $.ajax({
        url: "https://graph.facebook.com/v20.0/" + pageId + "/messages?access_token=" + pageAccessToken,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(messageData),
        success: function (response) {
            console.log("Tin nhắn đã được gửi!", response);
        },
        error: function (xhr, status, error) {
            console.error("Lỗi khi gửi tin nhắn:", error);
        }
    });
}

function formatDateToMySQL(date) {
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    var hours = ('0' + date.getHours()).slice(-2);
    var minutes = ('0' + date.getMinutes()).slice(-2);
    var seconds = ('0' + date.getSeconds()).slice(-2);

    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
}
 
function escapeSpecialChars(jsonString) {
    return jsonString.replace(/\\n/g, "\\n")
                     .replace(/\\'/g, "\\'")
                     .replace(/\\"/g, '\\"')
                     .replace(/\\&/g, "\\&")
                     .replace(/\\r/g, "\\r")
                     .replace(/\\t/g, "\\t")
                     .replace(/\\b/g, "\\b")
                     .replace(/\\f/g, "\\f");
} 