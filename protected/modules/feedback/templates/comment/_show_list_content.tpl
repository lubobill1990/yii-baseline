<div id="comment-list-content">
{include file='file:[feedback]comment/_list.tpl' comments=$comments}
</div>
<div id="comment-list-pagination">

</div>
<script type="text/javascript">
    $(function () {
        $('#comment-list-pagination').WJ('pagination',{$comment_count},{$comment_items_per_page}, function (page_index, jq) {
            $.post('/feedback/comment/list?subject_type={$subject_type}&subject_id={$subject_id}&page_no=' + page_index, {
            }, function (data) {
                $('#comment-list-content').html(data);
                var comment_area_offset = $("#comment-area").offset()
                window.scrollTo(comment_area_offset.left, comment_area_offset.top - 50)
            });
            return false;
        })
    })
</script>