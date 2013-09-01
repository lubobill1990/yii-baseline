<div class="tree-node">
{include file="file:[feedback]comment/_item.tpl" comment=$comment}
{if $comment->child_comments}
    <div class="tree-children">
        {foreach $comment->child_comments as $child_comment}
        {include file="file:[feedback]comment/_tree_node.tpl" comment=$child_comment}
        {/foreach}
        {if $comment->child_more_comments_count}
            <div class="more-sibling-count">
                <a href="#">{$comment->child_more_comments_count}</a>
            </div>
        {/if}

    </div>
{/if}

</div>