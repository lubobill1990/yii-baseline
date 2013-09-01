{*
参数：
subject_type
subject_id
count
items_per_page
*}
<div id="comment-area" class="comment" data-subject-type="{$subject_type}" data-subject-id="{$subject_id}">
    <div id="comment-list">

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
    require(['feedback/comment'], function () {
        $.get('/feedback/comment/list/?subject_type=' + $('#comment-area').attr('data-subject-type') + '&subject_id=' + $('#comment-area').attr('data-subject-id'), function (data) {
            $('#comment-list').html(data);
        })
    })
</script>