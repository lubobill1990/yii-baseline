<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="/stylesheets/screen.css">
    <script type="text/javascript" src='/javascripts/require.2.1.5.js'></script>
    <script type="text/javascript" src='/javascripts/main.js'></script>
</head>
<script type="text/javascript">
    var frameHeight = 0;
    var commentListHeight = 0;
    var otherHeight = 0;
    require(['jquery', 'xdm'], function (jQuery) {
        xdmSocket = new easyXDM.Socket({
            onReady:function () {
                frameHeight = document.body.scrollHeight;
                commentListHeight = $('#comment-list-content').height();
                otherHeight = frameHeight - commentListHeight;
                xdmSocket.postMessage(document.body.scrollHeight + 100)
            }
        });
    })
</script>

<body>
<div id="comment-area" class="comment" data-subject-type="{$subject_type}" data-subject-id="{$subject_id}">
    <div id="comment-list">
        <div id="comment-list-content">
        {include file='file:[feedback]comment/_list.tpl' comments=$comments}
        </div>
        <div id="comment-list-pagination">

        </div>
    </div>
{if $login_user->id|default:false}
    <div class="comment-form txd" id="add_comment">

        <h4>
            你的回应
            &nbsp;·&nbsp;·&nbsp;·&nbsp;·&nbsp;·&nbsp;·
        </h4>

        <div class="reply-comment">
            <a href="#close_reply" class="lnk-close">×</a>

            <p class="reply-ref"></p>
        </div>

        <div class="comment-form">
            <form name="comment_form" method="post" action="/feedback/comment/submit">
                <div style="display:none;">
                    <input type="hidden" name="Comment[subject_id]" value="{$subject_id}">
                    <input type="hidden" name="Comment[subject_type]" value="{$subject_type}">
                    <input type="hidden" name="Comment[refer_comment_id]" value="" id="ref_cmt_id">
                    <input type="hidden" name="Comment[refer_comment_content]" value="" id="ref_cmt_ctnt">
                </div>

                <div class="form-item">
                    <textarea id="comment_textarea" name="Comment[content]" rows="4" cols="64"></textarea><br>
                </div>
                <input type="hidden" name="start" value="100">

                <div class="form-item">
                    <span class="bn-flat-hot rr"><input type="submit" class=" btn" value="加上去"></span>
                </div>
            </form>
        </div>
    </div>
{/if}

</div>

<script type="text/javascript">
    require(['feedback/comment','resize'], function () {
        $(function () {
            $('#comment-list-pagination').WJ('pagination',{$comment_count},{$comment_items_per_page}, function (page_index, jq) {
                $.post('/feedback/comment/listIFrame?subject_type={$subject_type}&subject_id={$subject_id}&page_no=' + page_index, {
                }, function (data) {
                    $('#comment-list-content').html(data);
                    var comment_area_offset = $("#comment-list-content").offset()
                    window.scrollTo(comment_area_offset.left, comment_area_offset.top - 50)
                    xdmSocket.postMessage($('#comment-list-content').height() + otherHeight + 100);
                });
                return false;
            })
        })
        $('#comment-list-content').resize(function () {
            xdmSocket.postMessage($('#comment-list-content').height() + otherHeight + 100)
        })
    })

</script>
</body>
</html>