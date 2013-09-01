$('document').ready(function(){
    console.log("喜欢ClassIC，还是发现我们的代码写的太匆忙好多bug？不妨一起来为ClassIC添砖加瓦吧！");
    console.log("http://wiki.lilystudio.org/");
});


//面包屑
$('.crumb li').mouseover(function(){
    $(this).find('.crumb-li-hover').removeClass('none');
    $(this).find('.crumb-li-subnav').removeClass('none');
}).mouseout(function(){
    $(this).find('.crumb-li-hover').addClass('none');
    $(this).find('.crumb-li-subnav').addClass('none');
});

//slide
$('.slide-main ul li').mouseover(function(){
    $(this).find('.slide-popup').removeClass('none');
}).mouseout(function(){
    $(this).find('.slide-popup').addClass('none');
});

//防止弹出窗口冒泡
$('.slide-popup').mouseover(function(){
    return false;
}).mouseout(function(){
    return false;
});
var sliding = 0;
$('.slide-prev').click(function(){
    if(sliding){
        return true;
    }
    var current_prompt = $('.slide-prompt').find('.current');
    var current_num = current_prompt.data('number');
    if(current_num>0){
        //更新右上角提示符
        current_prompt.removeClass('current');
        current_prompt.prev().addClass('current');

        //移动ul
        var offset = parseInt($('.slide-ul-wrapper').css('left').replace('px',''))+509;
        sliding = 1;
        $('.slide-ul-wrapper').animate({'left':offset+"px"},function(){
            sliding = 0;
        });
    }

    //重置按钮
    $('.slide-next').removeClass('off');
    $('.slide-prev').removeClass('off');
    if(current_num<=1){
        $(this).addClass('off');
    }
});
$('.slide-next').click(function(){
    if(sliding){
        return true;
    }
    var current_prompt = $('.slide-prompt').find('.current');
    var current_num = current_prompt.data('number');
//    console.log($('.slide-prompt ').length);
    if(current_num<$('.slide-prompt li').length-1){
        //更新右上角提示符
        current_prompt.removeClass('current');
        current_prompt.next().addClass('current');

        //移动ul
        var offset = parseInt($('.slide-ul-wrapper').css('left').replace('px',''))-509;
        sliding =1;
        $('.slide-ul-wrapper').animate({'left':offset+"px"},function(){
            sliding = 0;
        });
    }

    //重置按钮
    $('.slide-next').removeClass('off');
    $('.slide-prev').removeClass('off');
    if(current_num>=$('.slide-prompt li').length-2){
        $(this).addClass('off');
    }
});