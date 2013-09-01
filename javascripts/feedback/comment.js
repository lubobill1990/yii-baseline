/**
 * Created with JetBrains PhpStorm.
 * User: bolu
 * Date: 13-1-28
 * Time: AM2:52
 * To change this template use File | Settings | File Templates.
 */
define(['form', 'atwho'],
    function () {
        $(function () {
            $(document).on('click', '.toggle-reply',function () {
                $(this).prev('.short').slideToggle();
                $(this).prev('.all').slideToggle();
            }).on('click', '.lnk-reply',function () {
                    var ref_id = $(this).attr('data-cid');
                    $('.lnk-close').show();
                    var content = $('#content_' + $(this).attr('data-cid')).html();
                    $('#ref_cmt_ctnt').val(content);
                    $('.reply-ref').html(content);
                    $('.reply-comment').show()
                    $('#ref_cmt_id').val(ref_id);
                }).on('click', '.lnk-close',function () {
                    $('.reply-ref').html('');
                    $('.reply-comment').hide()
                    $(this).hide();
                    $('#ref_cmt_id').val('');
                }).on('mouseenter', '.comment-item',function () {
                    $(this).addClass('over')
                }).on('mouseleave', '.comment-item',function () {
                    $(this).removeClass('over')
                }).on('click', '.toggle-reply', function () {
                    if (!this.has_been_clicked) {
                        $(this).siblings('.all').show().siblings('.short').hide()
                        this.has_been_clicked = true
                    } else {
                        $(this).siblings('.short').show().siblings('.all').hide()
                        this.has_been_clicked = false
                    }
                })


            $('#comment_textarea').atwho({
                at:"@",
                'callback':function (query, callback) {
                    $.get("/follow/candidates", {'key':query, 'limit':10}, function (e) {
                        callback(e);
                    }, 'json');
                },
                'limit':10
            });

            $('[name=comment_form]').ajaxForm({
                success:function (e) {
                    if (e.code == 200) {
                        $('#comment-list-content .comments').append(e.data);
                        $('#comment_textarea').val("");
                    }
                },
                dataType:'json'
            });
        });
    }
)
