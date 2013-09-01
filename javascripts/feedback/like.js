define(['wenji'], function () {

    $(function () {
        $(document).on('click', '.btn-fav.fav-cancel',function () {
            var fav_bar = $(this).parents('.sns-bar-fav');
            var subject_type = fav_bar.attr('data-stype');
            var subject_id = fav_bar.attr('data-sid');
            $.post('/feedback/like/undoLike/' + subject_type + '/' + subject_id, {

            }, function (data) {
                fav_bar.find('.like_count').text(data['like_count']);
                fav_bar.find('.btn-fav').removeClass('fav-cancel').addClass('fav-add')
            }, 'json')
            return false;
        }).on('click', '.btn-fav.fav-add',function () {
                var fav_bar = $(this).parents('.sns-bar-fav');
                var subject_type = fav_bar.attr('data-stype');
                var subject_id = fav_bar.attr('data-sid');
                $.post('/feedback/like/like/' + subject_type + '/' + subject_id, {

                }, function (data) {
                    fav_bar.find('.like_count').text(data['like_count']);
                    fav_bar.find('.btn-fav').removeClass('fav-add').addClass('fav-cancel')
                }, 'json');
                return false;
            }).on('click', '.fav-num .people-like', function () {
                var fav_bar = $(this).parents('.sns-bar-fav');
                var subject_type = fav_bar.attr('data-stype');
                var subject_id = fav_bar.attr('data-sid');
                $.get('/feedback/like/peopleList/' + subject_type + '/' + subject_id, function (data) {
                    console.log(data)
                })
                return false;
            });
    });
})