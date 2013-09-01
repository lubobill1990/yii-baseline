/**
 * Created with JetBrains PhpStorm.
 * User: bolu
 * Date: 13-5-4
 * Time: PM4:20
 * To change this template use File | Settings | File Templates.
 */
requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl:'/javascripts',
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths:{
        app:'app',
        jquery:'lib/jquery.1.9.1',
        underscore:'lib/underscore.1.4.4',
        backbone:'lib/backbone.1.0.0',
        webim:'rts/webim',
        rts:'rts/main',
        "engine.io":'lib/engine.io',
        tinyscrollbar:'lib/jquery.tinyscrollbar.1.81',
        jSmart:'lib/smart-2.9.min',
        jsmart:'lib/smart-2.9.min',
        smart:'lib/smart-2.9.min',
        pagination:'lib/jquery.pagination',
        "rts.util":'rts/util',
        cssrefresh:'lib/cssrefresh',
        charcounter:'lib/jquery.charcounter',
        atwho:'lib/jquery.atwho',
        caret:'lib/jquery.caret',
        json2:'lib/json2',
        form:'lib/jquery.form',
        xdm:'lib/easyXDM',
        resize:'lib/jquery.resize',
        fileupload:'fileupload/main',
        'fileupload.tmpl':'fileupload/tmpl',
        'jquery.fileupload':'fileupload/jquery.fileupload',
        'jquery.fileupload-ui':'fileupload/jquery.fileupload-ui',
        'jquery.iframe-transport':'fileupload/jquery.iframe-transport',
        'jquery.fileupload-process':'fileupload/jquery.fileupload-process',
        'jquery.fileupload-resize':'fileupload/jquery.fileupload-resize',
        'jquery.fileupload-validate':'fileupload/jquery.fileupload-validate',
        'jquery.ui.widget':'lib/jquery.ui.widget',
        'load-image':'fileupload/load-image',
        'canvas-to-blob':'fileupload/canvas-to-blob',
        'components':'common/components',
        notty:'lib/jquery.classynotty'
    },
    shim:{
        components:{
            deps:['jquery']
        },
        backbone:{
            deps:['underscore', 'jquery'],
            exports:'Backbone'
        },
        underscore:{
            exports:'_'
        },
        webim:{
            deps:['jquery', 'rts', 'jSmart', 'rts.util', 'tinyscrollbar']
        },
        rts:{
            deps:['jquery', "engine.io"]
        },
        tinyscrollbar:{
            deps:['jquery']
        },
        pagination:{
            deps:['jquery']
        },
        caret:{
            deps:['jquery']
        },
        atwho:{
            deps:['jquery', 'caret']
        },
        "feedback/comment":{
            deps:['jquery']
        },
        form:{
            deps:['jquery']
        },
        resize:{
            deps:['jquery']
        },
        'jquery.ui.widget':{
            deps:['jquery']
        },
        'fileupload':{
            deps:['jquery',
                'load-image',
                'canvas-to-blob',
                'jquery.iframe-transport',
                'jquery.fileupload',
                'jquery.fileupload-ui',
                'jquery.fileupload-process',
                'jquery.fileupload-resize',
                'jquery.fileupload-validate',
                'fileupload.tmpl'
            ]
        },
        'bootstrap/modal':{
            deps:['jquery'
            ]
        },
        'foundation/foundation':['jquery','lib/zepto'],
        'foundation/foundation.forms':['foundation/foundation'],
        'foundation/foundation.dropdown':['foundation/foundation']
    }
});
require(['jquery'],
    function () {
        var jSmartTemplateCache = [];
        (function ($) {
            function init_upload_form(options, callback) {
                require(['fileupload', 'bootstrap/modal'], function () {
                    var this_div = $('#' + options['modal_id'])
                    var upload_form = this_div.find('form.fileupload');
                    upload_form.fileupload(options);

                    this_div.modal({
                        backdrop:false,
                        keyboard:false
                    })
                    function check_uploading_finished() {
                        if (uploading_count <= 0) {
                            uploading_count = 0;
                            finish_button.attr('disabled', false);
                        }
                    }

                    var uploaded_files = [];
                    var finish_button = this_div.find('.modal-footer .finish');
                    var uploading_count = 0;
                    upload_form.bind('fileuploadsend',function () {
                        uploading_count++;
                        finish_button.attr("disabled", true);
                    }).bind('fileuploadprogressall',function (e, data) {
                            var progress = parseInt(data.loaded / data.total, 10);
                            if (progress === 1) {
                                finish_button.attr("disabled", false);
                            }
                        }).bind('fileuploadalways',function (e, data) {
                            uploading_count--;
                            check_uploading_finished();
                        }).bind('fileuploadstop',function (e, data) {
                            uploading_count--;
                            check_uploading_finished();
                        }).bind('fileuploaddone', function (e, data) {
                            uploaded_files = uploaded_files.concat(data.result.files);
                        })
                    finish_button.click(function () {
                        callback(uploaded_files);
                        uploaded_files = [];
                        this_div.modal('hide');
                        $(this).parents('.modal-footer').siblings('.modal-body').find('tbody.files').html('')
                    })
                    finish_button.attr("disabled", true);
                    $(document).on('click', 'form.fileupload .delete',function () {
                        var this_delete_button = $(this);
                        var file_id = this_delete_button.parents('tr').attr('file_id')
                        for (var i = 0; i < uploaded_files.length; ++i) {
                            if (uploaded_files[i].id == file_id) {
                                delete uploaded_files[i];
                            }
                        }
                        this_delete_button.parents('tr').remove();
                    }).on('click', ".cancel-all-uploads", function () {
                            uploaded_files = [];
                            $(this).parents('.modal-footer').siblings('.modal-body').find('tbody.files').html('')
                        })
                })

            }

            var class_methods = {
                pagination:function (element, total_count, items_per_page, pageSelectCallback, opts) {
                    require(['pagination'], function () {
                        var jump = true;
                        if (opts != undefined && opts['load_first_page'] == undefined) {
                            opts['load_first_page'] = false;
                        }
                        if (opts != undefined && opts['link_to'] == undefined) {
                            jump = false;
                        }
                        $(element).pagination(total_count, $.extend({
                            num_edge_entries:2,
                            num_display_entries:6,
                            callback:function (index, js) {
                                var ret = pageSelectCallback(index, js);
                                if (ret && jump) {
                                    return true;
                                } else {
                                    return false;
                                }
                            },
                            items_per_page:items_per_page,
                            prev_text:'&lt;前页',
                            next_text:'后页&gt;',
                            load_first_page:false
                        }, opts));
                    })
                },
                imageupload:function (callback, options) {
                    var default_options = {
                        maxChunkSize:1500000, // 10 MB
                        url:'/image/create',
                        modal_id:'image_upload_modal',
                        get_template_url:'/image/html'
                    }
                    if (typeof(options) == "function") {
                        callback = options;
                    }
                    if (typeof(options) == 'object') {
                        options = $.extend({}, default_options, options)
                    } else {
                        options = default_options;
                    }
                    require(['jquery'], function () {
                        if ($('#' + options['modal_id']).length > 0) {
                            init_upload_form(options, callback)
                        } else {

                            $.get(options['get_template_url'], function (data) {
                                $('body').append(data);
                                init_upload_form(options, callback);
                            })
                        }
                    })
                },
                fileupload:function (callback, options) {
                    var default_options = {
                        url:'/fileUpload/create',
                        modal_id:'file_upload_modal',
                        get_template_url:'/fileUpload/html'
                    }
                    var opt;
                    if (typeof (options) == 'object') {
                        opt = $.extend({}, default_options, options);
                    } else {
                        opt = default_options;
                    }

                    $.WJ('imageupload', callback, opt);
                },
                notify:function (opt) {
                    opt = $.extend({
                        showTime:false,
                        timeout:5000,
                        title:'提醒'
                    }, opt)
                    require(['notty'], function () {
                        $.ClassyNotty(opt);
                    })
                }
            }
            var methods = {
                pagination:function (total_count, items_per_page, pageSelectCallback, opts) {
                    return $.WJ('pagination', $(this), total_count, items_per_page, pageSelectCallback, opts);
                },
                jSmartFetch:function (data, callback) {
                    var this_ele = $(this);
                    require(['jsmart'], function () {
                        jSmart.prototype.getTemplate = function (name) {
                            return $("#" + name).html();
                        }
                        var selectorString = this_ele.selector;
                        if (jSmartTemplateCache[selectorString] == undefined) {
                            jSmartTemplateCache[selectorString] = new jSmart(this_ele.html())
                        }
                        callback(jSmartTemplateCache[selectorString].fetch(data));
                    })
                }

            }
            $.WJ = function (method) {
                if (class_methods[method]) {
                    return class_methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else if (typeof method === 'object' || !method) {
                    return class_methods.init.apply(this, arguments);
                } else {
                    $.error('Method ' + method + ' does not exist on jQuery.WJ');
                }
            };
            $.fn.WJ = function (method) {
                if (methods[method]) {
                    return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else if (typeof method === 'object' || !method) {
                    return methods.init.apply(this, arguments);
                } else {
                    $.error('Method ' + method + ' does not exist on jQuery.fn.WJ');
                }
            }

        })(jQuery);

    }
)
require(['jquery'],function(){
    $(document).ready(function(){
        $('.captcha-img').click(function(){
            $(this).attr('src',"/captcha?timestamp=" + new Date().getTime())
        })
    })
})