{block name=left}
<div class="row">
    <article class="span8">
        <div class="comment-list">
        {if $current_comment_with_parent_comments->parent_more_comments_count}
            <div class="more-parent-count">
                <a href="#">{$current_comment_with_parent_comments->parent_more_comments_count}</a>
            </div>
        {/if}
        {$parent_comments=$current_comment_with_parent_comments->parent_comments}
        {if $parent_comments}
            <div class="parent-comment-list">
                {foreach $parent_comments as $parent_comment}
            {include file='file:[feedback]comment/_item.tpl' comment=$parent_comment}
                    {/foreach}
            </div>
        {/if}
        {*<div class="current-comment">*}
        {*{include file='file:[feedback]comment/item.tpl' comment=$current_comment_with_parent_comments}*}
        {*</div>*}
            <div class="children-comments">
            {include file="file:[feedback]comment/_tree_node.tpl" comment=$children_comment_tree}
            </div>
        </div>
    {if $login_user and $login_user->id|default:false}
        <div class="comment-form txd" id="add_comment">
            <div class="reply-comment">
                <a href="#close_reply" class="lnk-close">×</a>

                <p class="reply-ref"></p>
            </div>
            <h4>
                你的回应
                &nbsp;·&nbsp;·&nbsp;·&nbsp;·&nbsp;·&nbsp;·
            </h4>

            <div class="comment-form">
                <form name="comment_form" method="post" action="/feedback/comment/submit">
                    <div style="display:none;">
                        <input type="hidden" name="Comment[subject_id]" value="{$current_comment->subject_id}">
                        <input type="hidden" name="Comment[subject_type]" value="{$current_comment->subject_type}">
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
    </article>
    <aside class="span3">

    </aside>
</div>
{/block}
{block name=js}

<script type="text/javascript">
    require(['feedback/comment'],function(){

    })
</script>
{/block}
