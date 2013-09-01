/**
 * Created with JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-18
 * Time: AM11:07
 * To change this template use File | Settings | File Templates.
 */
define(['jquery'],function(){
    $('.digg-bury-item>.digg-btn,.digg-bury-item>.bury-btn').live('click', function (e) {
        var digg_bury_item = $(this).parents('.digg-bury-item');
        var this_item = $(this);
        var subject_id = digg_bury_item.attr("subject_id");
        var subject_type = digg_bury_item.attr('subject_type');
        var digg_url = "/feedback/diggbury/digg/" + subject_type + '/' + subject_id;
        var bury_url = "/feedback/diggbury/bury/" + subject_type + '/' + subject_id;
        if ($(e.target).parents('.digg-btn').length != 0) {
            $.get(digg_url, function (data) {
                digg_bury_item.find('.digg-btn .digg-bury-count').html(data['digg_count']);
                digg_bury_item.find('.bury-btn .digg-bury-count').html(data['bury_count']);
                digg_bury_item.find('.digg-btn').addClass('active');
                digg_bury_item.find('.bury-btn').removeClass('active');
            }, 'json')
        } else if ($(e.target).parents('.bury-btn').length != 0) {
            $.get(bury_url, function (data) {
                digg_bury_item.find('.digg-btn .digg-bury-count').html(data['digg_count']);
                digg_bury_item.find('.bury-btn .digg-bury-count').html(data['bury_count']);
                digg_bury_item.find('.bury-btn').addClass('active');
                digg_bury_item.find('.digg-btn').removeClass('active');
            }, 'json')
        }
    });
})