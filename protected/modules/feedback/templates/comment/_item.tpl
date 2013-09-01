<div class="comment-item" id="{$comment->id}" data-target_id="{$comment->refer_comment_id}">
    <div class="pic">
        <a href="/{$comment->user_id}"><img
                src="{$comment->user->getAvatarUrl(60)}"
                alt=""></a>
    </div>
    <div class="content">
        <div class="author">
        {$comment->create_time}
            <a href="{$comment->user->url}">{$comment->user->username}</a>
        {*{if $comment->user->signature|default:false}({$comment->user->signature}){/if}*}


        </div>

    {if $with_quote|default:true && $comment->refer_comment_id}
        <div class="reply-quote">
            <span class="short">{$comment->getShortReferCommentContent()}{if $comment->isShortReferCommentCut()}
                ...{/if}</span>
            <span class="all" style="display: none;">{$comment->refer_comment_content}</span>
            <span class="pubdate"><a
                    href="{$comment->referComment->user->url}">{$comment->referComment->user->username}</a></span>
            <a href="javascript:void(0);" class="toggle-reply">{if $comment->isShortReferCommentCut()}切换{/if}</a></div>
    {/if}

        <p id="content_{$comment->id}">{$comment->content}</p>

        <div class="op-lnks">
        {if $login_user}

            <a href="#add_comment" class="lnk-reply" data-cid="{$comment->id}">回应</a>
            {if $login_user->id==$comment->user->id}
                <a rel="nofollow" href="#delete_comment" class="j a_confirm_link"
                   title="删除{$comment->user->username}的留言?">删除</a>
            {/if}
            <div class="comment-report"><a rel="nofollow" href="javascript:void(0)">举报</a>
            </div>
        {/if}
            <a href="/feedback/comment/show/?id={$comment->id}" target="_blank">展开</a>

        </div>
    {if $login_user and $login_user->hasPrivilege('delete-comment')|default:false}
        <div class="group_banned">
            <span class="gact hidden p_u1181212 p_admin p_intern fright">&gt;
                <a rel="nofollow"
                   href="http://www.douban.com/note/248291511/remove_comment?cid=31238349&amp;amp;start=100"
                   class="j a_confirm_link" title="删除{$comment->user->username}的评论?">删除</a>
            </span>
        </div>
    {/if}

    </div>
</div>