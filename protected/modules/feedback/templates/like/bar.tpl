<div class="sns-bar-fav" data-sid="{$subject_id}" data-stype="{$subject_type}">
    <span class="fav-num">
        <a class="people-like" href="#" data-url="/feedback/like/peopleList"><span class="like_count">{$like_count}</span>人</a> 收藏
    </span>
    {$has_like=$has_like|default:false}
    <a class="btn-fav {if $has_like===true}fav-cancel{else}fav-add{/if}" title="收藏？" href="#" data-sid="{$subject_id}" data-stype="{$subject_type}">收藏</a>

</div>