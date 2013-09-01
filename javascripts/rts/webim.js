/**
 * Created with JetBrains PhpStorm.
 * User: 勃
 * Date: 13-1-14
 * Time: 下午8:51
 * To change this template use File | Settings | File Templates.
 */
/**
 * user对象：
 * {
 *     id:int,
 *     username:string,
 *     avatar:string
 * }
 * message对象：
 * {
 *      from_user_id:int,
 *      to_user_id:int,
 *      content:int,
 *      timestamp:datetime
 * }
 * webim类
 * 1. 设置用户下线和上线的状态
 * 2.
 * chatbox类
 * open
 * close
 * minimize
 * setActiveUser
 *  修改左栏的focus
 *  修改对话框
 * appendReceiveMessage(message)
 *  如果chatbox中的节点不存在，则创建用户列表栏节点和dialog节点
 *  添加收到的信息到对应的节点
 *  如果左栏非active，则高亮之
 *  如果chatbox未打开，则高亮min_chat_bar
 * appendSendMessage(message)
 */
define(['rts','rts.util','charcounter'],function(rts){
    jSmart.prototype.getTemplate = function (name) {
        return $("#" + name).html();
    }
    ;
    (function ($) {
        /**
         * 存储用户信息的缓存池
         * @type {UserInfoList}
         */
        var userInfo = function UserInfoList() {
            var userInfoArray = [];
            return {
                getUser:function (user_id, callback) {
                    if (userInfoArray[user_id] == undefined) {
                        $.get('/message/userInfo', {
                            user_id:user_id
                        }, function (data) {
                            if (data.success) {
                                callback(data.data[0]);
                            }
                        }, 'json')
                    } else {
                        callback(userInfoArray[user_id])
                    }
                    return userInfoArray[user_id];
                },
                getFriendList:function (callback) {
                    $.get('/message/friendList', function (data) {
                        if (data.success) {
                            for (var i in data.data) {
                                userInfoArray[data.data[i]['id']] = data.data[i];
                            }
                            callback(data.data);
                        }
                    }, 'json')
                }
            };
        }();

        function FriendList() {
            this.lastWindowHeight = 0;
        }

        /**
         * 初始化好友列表
         * 1. 取好友json数组
         * 2. 构建html
         */
        FriendList.prototype.init = function () {
            var thisList = this;
            userInfo.getFriendList(function (data) {
                var result = '';
                var tpl = new jSmart($('#webim_friend_list_item').html());
                for (var i=0;i<data.length;++i) {
                    result += tpl.fetch({user:data[i]});
                }
                $('#friend_list_item_container').html(result);
                $('#friend-list-scroll-container').tinyscrollbar();
                thisList.resize();
                window.onresize = function (event) {
                    thisList.resize()
                }
            });
            $(document).on('click', '.friend-list-user', function () {
                chatBoxObj.chatTo($(this).attr('data-user-id'))
            }).on('click', '.setting_btn', function () {
                    switch ($(this).WJ('actionType')) {
                        case 'downFoldButton':
                            friendListObj.minimize();
                            break;
                        default :
                            break;
                    }
                }).on('click', '.webim_win_minD.wbim_min_friend', function () {
                    friendListObj.restore();
                });

        };
        FriendList.prototype.resize = function () {
            var window_height = $(window).height();
            if (Math.abs(window_height - this.lastWindowHeight) > 42) {
                this.lastWindowHeight = window_height;
                $('#friend-list-scroll-container .viewport').css({height:window_height - 120});
                $('#friend-list-scroll-container').tinyscrollbar_update();
            }
        }

        FriendList.prototype.refreshScrollBar = function () {
            $('#friend-list-scroll-container').tinyscrollbar_update();
        }
        FriendList.prototype.login = function (user_id) {
            var thisList = this;
            userInfo.getUser(user_id, function (data) {
                var item = $('#friend_list_user_' + user_id);
                //如果html中已经存在该用户
                if (item.length) {
                    item.find('.W_chat_stat').removeClass('W_chat_stat_offline').addClass('W_chat_stat_online');
                } else {
                    //否则，先通过该用户的数据构造html，然后再加到列表中去
                    var ele = $($('#webim_friend_list_item').WJ('jSmartFetch', {user:data}))
                    ele.find('.W_chat_stat').removeClass('W_chat_stat_offline').addClass('W_chat_stat_online');
                    $('#friend_list_item_container').append(ele);
                    thisList.refresh();
                }
            })
            $('.online_num').each(function (index, ele) {
                $(ele).WJ('incrInt')
            })
        }
        FriendList.prototype.logout = function (user_id) {
            $('#friend_list_user_' + user_id).find('.W_chat_stat').addClass('W_chat_stat_offline').removeClass('W_chat_stat_online');
            $('.online_num').each(function (index, ele) {
                $(ele).WJ('decrInt')
            })
        }

        FriendList.prototype.minimize = function () {
            $('.webim_list.WBIM_unfold').hide();
            $('.webim_win_minD.wbim_min_friend').show();
        }

        FriendList.prototype.restore = function () {
            $('.webim_list.WBIM_unfold').show();
            $('.webim_win_minD.wbim_min_friend').hide();
            this.refreshScrollBar();
        }
        function ChatBox() {
            this.conversation_count=0;
        }

        ChatBox.prototype.init = function () {
            var chatbox = this;
            $(document).on('click', '.WBIM_icon_wbclose', function () {
                var this_item = $(this).parents('li');
                chatbox.closeConversation($(this).parents('li').attr('data-user-id'));
                return false;
            }).on('click', '#chatbox_send_message', function () {
                    if($(".sendbox_box textarea").val()==''){
                        $(".sendbox_box textarea").addClass('alert')
                        setTimeout(function(){
                            $(".sendbox_box textarea").removeClass('alert')
                            setTimeout(function(){
                                $(".sendbox_box textarea").addClass('alert')
                                setTimeout(function(){
                                    $(".sendbox_box textarea").removeClass('alert')
                                },100)
                            },100)
                        },100)


                        return;
                    }
                    var message = {
                        to_user_id:chatbox.currentUserId(),
                        content:$(".sendbox_box textarea").val()
                    }
                    var item = chatbox.appendRightMessage(message);
                    item.find('.message_send_status').html('<i class="icon-spinner icon-spin"></i>');

                    $("#webim-chatbox-scrollbar").tinyscrollbar_update('bottom');
                    var message_send_timeout=setTimeout(function(){
                        item.find('.message_send_status').html('<i class="icon-exclamation-sign WJ-color-error" title="发送失败，服务器超时"></i>');
                        item.find('.info_date').text("消息发送超时");
                        $("#webim-chatbox-scrollbar").tinyscrollbar_update('bottom');
                    },3000)
                    $.post("/message/send", message, function (data) {
                        clearTimeout(message_send_timeout);
                        if (data.success) {
                            item.find('.info_date').text(data.data);
                            item.find('.message_send_status').html('');
                        }else{
                            item.find('.info_date').text("消息数据错误");
                            item.find('.message_send_status').html('<i class="icon-exclamation-sign WJ-color-error" title="发送失败，信息格式错误"></i>');
                        }
                        $("#webim-chatbox-scrollbar").tinyscrollbar_update('bottom');
                    }, 'json')
                    $(".sendbox_box textarea").val("")
                }).on('click', '.chatbox-user', function () {
                    chatbox.activateConversation($(this).attr('data-user-id'))
                }).on('click', '.WBIM_icon_closeY', function () {
                    chatbox.close();
                }).on('click', '.WBIM_icon_closeY', function () {
                    chatbox.close();
                }).on('click', '.WBIM_icon_minY', function () {
                    chatbox.minimize();
                }).on('click', '#webim-chatbox-mini-bar', function () {
                    chatbox.show();
                    $("#webim-chatbox-scrollbar").tinyscrollbar_update('bottom');
                    this.removeClass('webim_newMsg');
                }).on('keydown',".sendbox_box textarea",function(e){
                    if (e.ctrlKey && e.keyCode == 13) {
                        $('#chatbox_send_message').click();
                    }
                });
            $(".sendbox_box textarea").charCounter(255,{
                container:"#char_limit_counter",
                format: "%1"
            })
        };
        ChatBox.prototype.currentUserId = function () {
            return $('#webim_chat_friend_list').children('.webim_active').attr('data-user-id');
        }
        ChatBox.prototype.getCurrentUser = function (callback) {
            userInfo.getUser(this.currentUserId(), callback)
        }
        ChatBox.prototype.show = function () {
            $('#webim_chat_box').show();
            $('#webim-chatbox-mini-bar').hide();
        }
        ChatBox.prototype.close = function () {
            $('#webim_chat_box').hide();
            $('#webim-chatbox-mini-bar').hide();
            this.flush();
        }
        ChatBox.prototype.minimize = function () {
            $('#webim_chat_box').hide();
            this.getCurrentUser(function (data) {
                $('#webim-chatbox-mini-bar').text(data.username);
                $('#webim-chatbox-mini-bar').show();
            })

        }
        /*刷新聊天box的内容*/
        ChatBox.prototype.flush = function () {
            $('#webim_chat_friend_list').html("");
            $('#webim_chat_dialogue').html("");
            $('#webim-chatbox-head').html('');
        }

        ChatBox.prototype.newConversation = function (user_id, activate) {
            var thisObj = this;

            //显示tab
            userInfo.getUser(user_id, function (data) {
                var html = $('#webim_chating_friend_item').WJ('jSmartFetch', {user:data})
                html = $(html);
                html.addClass('webim_active');
                $('#webim_chat_friend_list').append(html);
                if (activate) {
                    thisObj.activateTab(user_id)
                }
            })
            //显示conversation panel
            userInfo.getUser(user_id, function (data) {
                $.get('/message/conversation', {
                    from_user_id:user_id
                }, function (data) {
                    if (data.success) {
                        var html = $('#webim_dia_list').WJ('jSmartFetch', {messages:data.data, user_id:user_id})
                        html = $(html);
                        html.show();
                        $('#webim_chat_dialogue').append(html);
                        $('#webim-chatbox-scrollbar').tinyscrollbar();
                        if (activate) {
                            thisObj.activateDialog(user_id)
                        }
                    }
                }, 'json')
            })
            this.conversation_count++;
            if(this.conversation_count>1){
                this.extend();
            }
        }
        ChatBox.prototype.chatTo = function (user_id) {
            var thisObj = this;
            //如果已经存在聊天窗口，则激活到该用户
            if (this.conversationExists(user_id)) {
                this.activateConversation(user_id);
                return;
            }
            thisObj.show();
            this.newConversation(user_id, true);
        }
        /**
         * 在左侧添加信息，即收到对方的信息后，在左侧显示
         * @param message
         */
        ChatBox.prototype.appendLeftMessage = function (message) {
            $('#webim_dia_list_' + message.from_user_id).append(
                $('#webim_dialog_box_left').WJ('jSmartFetch', {message:message})
            )
            if ($('#chatbox_user_' + message.from_user_id).hasClass('webim_active')) {
            } else {
                $('#chatbox_user_' + message.from_user_id).addClass('webim_newMsg')
            }
            $('#webim-chatbox-scrollbar').tinyscrollbar_update('bottom');
        }
        /**
         * 在右侧添加信息，即发送信息后，在右侧显示
         * @param message
         */
        ChatBox.prototype.appendRightMessage = function (message) {
            var ele = $($('#webim_dialog_box_right').WJ('jSmartFetch', {message:message}))
            $('#webim_dia_list_' + message.to_user_id).append(
                ele
            )
            this.activateDialog(message.to_user_id);
            return ele;
        }
        ChatBox.prototype.conversationExists = function (user_id) {
            if ($('#chatbox_user_' + user_id).length) {
                return true;
            }
            return false
        }
        ChatBox.prototype.activateTab = function (user_id) {
            $('#webim_chat_friend_list').children('li').removeClass('webim_active');
            $("#chatbox_user_" + user_id).addClass('webim_active');
            userInfo.getUser(user_id, function (data) {
                var html = $('#webim_chatbox_head_area').WJ('jSmartFetch', {user:data})
                $('#webim-chatbox-head').html(html);
            })
            $('#chatbox_user_' + user_id).removeClass('webim_newMsg')
        }
        ChatBox.prototype.activateDialog = function (user_id) {
            $('#webim-chatbox-scrollbar .webim_dia_list').hide();
            $('#webim_dia_list_' + user_id).show();
            $("#webim-chatbox-scrollbar").tinyscrollbar_update('bottom');
        }
        ChatBox.prototype.activateConversation = function (user_id) {
            this.activateTab(user_id);
            this.activateDialog(user_id);
        }
        ChatBox.prototype.closeConversation = function (user_id) {
            var chatbox = this;
            var this_item = $('#chatbox_user_' + user_id);
            if (this_item.hasClass('webim_active')) {
                if (this_item.prev().length != 0) {
                    chatbox.activateConversation(this_item.prev().attr('data-user-id'))
                } else if (this_item.next().length != 0) {
                    chatbox.activateConversation(this_item.next().attr('data-user-id'))
                }
            }

            $('#chatbox_user_' + user_id).remove();
            $('#webim_dia_list_' + user_id).remove();
            if ($('#webim_chat_friend_list>li').length == 0) {
                chatbox.close();
            }

            this.conversation_count--;
            if(this.conversation_count<2){
                this.shrink();
            }
        }
        ChatBox.prototype.shrink=function(){
            $('#webim_chat_box').css({width:316})
        }
        ChatBox.prototype.extend=function(){
            $('#webim_chat_box').css({width:422})
        }
        ChatBox.prototype.highlight = function (user_id) {
            //如果未激活该对话，则在边栏显示高亮
            if (!$('#chatbox_user_' + user_id).hasClass('webim_active')) {
                $('#chatbox_user_' + message.from_user_id).addClass('webim_newMsg')
            }
            //如果聊天窗口最小化，则高亮底栏
            if (this.isMinimized()) {
                $('#webim-chatbox-mini-bar').addClass('webim_newMsg');
            }
        }
        ChatBox.prototype.isMinimized = function () {
            return $('#webim-chatbox-mini-bar').css('display') == 'block';
        }
        ChatBox.prototype.isShow = function () {
            return $('#webim_chat_box').css('display') == 'block';
        }
        ChatBox.prototype.isClosed = function () {
            return !this.isShow() && !this.isMinimized();
        }


        var chat_box = $('.webim_chat_box');
        var min_chat_bar = $('.webim_min_chat');
        var current_user;
        var friendListObj;
        var chatBoxObj;

        function getCurrentUser() {

        }

        var class_methods = {
            init:function () {
                friendListObj = new FriendList();
                friendListObj.init();
                chatBoxObj = new ChatBox();
                chatBoxObj.init();
//            $.webim('closeChatBox')
            },
            chatTo:function (user_id) {
                chatBoxObj.chatTo(user_id)
                min_chat_bar.hide();
            },
            closeChatBox:function () {
                chat_box.hide();
                min_chat_bar.hide();
            },
            minimizeChatBox:function () {
                chat_box.hide();
                min_chat_bar.show();
                min_chat_bar.text(current_user.username);
            },
            openFriendList:function () {

            },
            minimizeFriendList:function () {

            },
            userLogout:function () {

            },
            userLogin:function () {

            },
            receiveMessage:function (data) {
                //如果没有正在和该用户聊天，则创建chatbox dialog
                if ($('#chatbox_user_' + data.from_user_id).length == 0) {
                    chatBoxObj.newConversation(data.from_user_id);
                }
                if (chatBoxObj.isClosed()) {
                    userInfo.getUser(data.from_user_id, function (user) {
                        $('#webim-chatbox-mini-bar').text(user.username);
                        $('#webim-chatbox-mini-bar').show();
                        chatBoxObj.highlight(data.from_user_id)
                    })
                }
                //接到信息后，如果聊天窗口没有开，则跳出
                chatBoxObj.appendLeftMessage(data);
            }


        }
        var methods = {
            pagination:function (total_count, items_per_page, pageSelectCallback, opts) {
                return $.WJ('pagination', $(this), total_count, items_per_page, pageSelectCallback, opts);
            },

            decrInt:function () {
                $(this).text(parseInt($(this).text()) - 1);
            },
            incrInt:function () {
                $(this).text(parseInt($(this).text()) + 1);
            },

            decrIntBy:function (num) {
                $(this).text(parseInt($(this).text()) - parseInt(num));
            },
            incrIntBy:function (num) {
                $(this).text(parseInt($(this).text()) + parseInt(num));
            }
        }
        $.webim = function (method) {
            if (class_methods[method]) {
                return class_methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return class_methods.init.apply(this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.WJ');
            }
        };
        $.fn.webim = function (method) {
            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.WJ');
            }
        }

    })(jQuery);
    $(function () {
        $.webim('init')
        rts.on('chat',function(data){
            $.webim('receiveMessage',data)
        })
    })
})