{block name=js}
{*{include file="file:[feedback]comment/show_list_wrapper.tpl" subject_id=1 subject_type='article'}*}
<script type="text/javascript">

    require(['jquery', 'feedback/comment.client'], function (jQuery) {
        getCommentList({
            url:"http://npeasy.com:3001/feedback/comment/listIFrame?subject_type=article&subject_id=1",
            element:$('#comment_frame').get(0)
        })
    })
</script>
{/block}
{block name=left}
<div id="comment_frame" style="width:500px;">

</div>
{/block}
