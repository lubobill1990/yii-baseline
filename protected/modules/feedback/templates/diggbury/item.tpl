<div class="digg-bury-item" subject_id='{$subject_id}' subject_type='{$subject_type}'>
    <span class="digg-btn btn-story-action-off  {if $hasDiggedOrBuried|default:''=='digg'}active{/if}">
        <span class="story-score">
            <span class="digg-bury-count"">{$digg_count}</span>
        </span>
        <span class="story-digg-label">顶</span>
    </span>
    <span class="bury-btn btn-story-action-on  {if $hasDiggedOrBuried|default:''=='bury'}active{/if}">
        <span class="story-score">
            <span class="digg-bury-count">{$bury_count}</span>
        </span>
        <span class="story-bury-label">踩</span>
    </span>
</div>