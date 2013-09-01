/**
 * Created with JetBrains PhpStorm.
 * User: bolu
 * Date: 12-12-19
 * Time: PM2:39
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    function getEventPoint(e) {
        var x = 0, y = 0;
        if (typeof e.offsetX !== 'undefined') {
            x = e.offsetX;
            y = e.offsetY;
        } else if (typeof e.x !== 'undefined') {
            x = e.x, y = e.y
        } else if (typeof e.layerX !== 'undefined') {
            x = e.layerX;
            y = e.layerY;
        } else {
            throw "no x,y in event(_getEventPoint)";
        }
        return {
            x:x,
            y:y
        };
    }

    var tmpwidth;
    $(document).on('mousemove', '.subject_score_div .subject_score',function (e) {
        if ($(this).attr('has_set_score') != 'yes') {
            $(this).children('.score').width(getEventPoint(e.originalEvent).x / e.target.clientWidth * 100 + '%')
        }
    }).on('mouseenter', '.subject_score_div .subject_score',function (e) {
            tmpwidth = $(this).children('.score').width();
        }).on('mouseleave', '.subject_score_div .subject_score',function (e) {
            $(this).children('.score').width(tmpwidth);
        }).on('click', '.subject_score_div .subject_score', function (e) {
            var this_div = $(this);
            var url = "/feedback/score/";
            var tmp = this_div.parents('.subject_score_div');
            var score = getEventPoint(e.originalEvent).x / e.target.clientWidth * 5;
            var subject_type = tmp.attr('subject_type');
            var subject_id = tmp.attr('subject_id');
            $.get(url + subject_type + '/' + subject_id + '?score=' + score, function (data) {
                this_div.children('.score').width(data['new_score'])
                this_div.siblings('.score_vote_count').text(data['new_score_vote_count']);
            });
            this_div.attr('has_set_score', 'yes');
        });
//    $('.subject_score_div .subject_score').live('mousemove', function (e) {
//        if($(this).attr('has_set_score')!='yes'){
//            $(this).children('.score').width(getEventPoint(e.originalEvent).x/e.target.clientWidth*100+'%')
//        }
//    });
//    $('.subject_score_div .subject_score').live('mouseenter', function (e) {
//        tmpwidth=$(this).children('.score').width();
//    });
//    $('.subject_score_div .subject_score').live('mouseleave', function (e) {
//        $(this).children('.score').width(tmpwidth);
//    });
//    $('.subject_score_div .subject_score').live('click',function(e){
//        var this_div=$(this);
//        var url="/feedback/score/";
//        var tmp=this_div.parents('.subject_score_div');
//        var score=getEventPoint(e.originalEvent).x/e.target.clientWidth*5;
//        var subject_type=tmp.attr('subject_type');
//        var subject_id=tmp.attr('subject_id');
//        $.get(url+subject_type+'/'+subject_id+'?score='+score,function(data){
//            this_div.children('.score').width(data['new_score'])
//            this_div.siblings('.score_vote_count').text(data['new_score_vote_count']);
//        });
//        this_div.attr('has_set_score','yes');
//    })
});