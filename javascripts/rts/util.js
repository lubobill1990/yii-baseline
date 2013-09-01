function getRandomNumberString() {
    return '1' + (Math.random() + '').substr(2, 10);
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
var dateFormat = function () {
    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
        timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
        timezoneClip = /[^-+\dA-Z]/g,
        pad = function (val, len) {
            val = String(val);
            len = len || 2;
            while (val.length < len) val = "0" + val;
            return val;
        };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date;
        if (isNaN(date)) throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
            d = date[_ + "Date"](),
            D = date[_ + "Day"](),
            m = date[_ + "Month"](),
            y = date[_ + "FullYear"](),
            H = date[_ + "Hours"](),
            M = date[_ + "Minutes"](),
            s = date[_ + "Seconds"](),
            L = date[_ + "Milliseconds"](),
            o = utc ? 0 : date.getTimezoneOffset(),
            flags = {
                d:d,
                dd:pad(d),
                ddd:dF.i18n.dayNames[D],
                dddd:dF.i18n.dayNames[D + 7],
                m:m + 1,
                mm:pad(m + 1),
                mmm:dF.i18n.monthNames[m],
                mmmm:dF.i18n.monthNames[m + 12],
                yy:String(y).slice(2),
                yyyy:y,
                h:H % 12 || 12,
                hh:pad(H % 12 || 12),
                H:H,
                HH:pad(H),
                M:M,
                MM:pad(M),
                s:s,
                ss:pad(s),
                l:pad(L, 3),
                L:pad(L > 99 ? Math.round(L / 10) : L),
                t:H < 12 ? "a" : "p",
                tt:H < 12 ? "am" : "pm",
                T:H < 12 ? "A" : "P",
                TT:H < 12 ? "AM" : "PM",
                Z:utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                o:(o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                S:["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
            };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();

// Some common format strings
dateFormat.masks = {
    "default":"ddd mmm dd yyyy HH:MM:ss",
    shortDate:"m/d/yy",
    mediumDate:"mmm d, yyyy",
    longDate:"mmmm d, yyyy",
    fullDate:"dddd, mmmm d, yyyy",
    shortTime:"h:MM TT",
    mediumTime:"h:MM:ss TT",
    longTime:"h:MM:ss TT Z",
    isoDate:"yyyy-mm-dd",
    isoTime:"HH:MM:ss",
    isoDateTime:"yyyy-mm-dd'T'HH:MM:ss",
    isoUtcDateTime:"UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
    dayNames:[
        "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
        "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames:[
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
        "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
    return dateFormat(this, mask, utc);
};


define(['jquery'], function () {
    (function ($) {
        var jSmartTemplateCache = [];
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
                        prev_text:'&lt;上页',
                        next_text:'下页&gt;',
                        load_first_page:false
                    }, opts));
                })
            },
            openInNewTab:function (url) {
                window.open(url, '_blank');
                window.focus();
            },
            npeasy:function (callback) {
                function s() {
                    if ($.npeasy == undefined) {
                        setTimeout(function () {
                            s()
                        }, 100)
                    } else {
                        callback($.npeasy);
                    }
                }

                s();
            },
            webim:function (callback) {
                function s() {
                    if ($.webim == undefined) {
                        setTimeout(function () {
                            s();
                        }, 100);
                    } else {
                        callback($.webim);
                    }
                }

                s();
            },
            notty:function (opt) {
                require(['jquery.classynotty'], function () {
                    $.ClassyNotty($.extend({
                        timeout:5000,
                        showTime:false
                    }, opt))
                })
            },
            jumpToUrl:function (url) {
                window.location.href = url;
            },
            replaceWithUrl:function (url) {
                window.location.replace(url)
            },
            jumpToAnchor:function (name) {
                window.location.hash = name;
            }
        }
        var methods = {
            pagination:function (total_count, items_per_page, pageSelectCallback, opts) {
                return $.WJ('pagination', $(this), total_count, items_per_page, pageSelectCallback, opts);
            },

            decrInt:function () {
                $(this).each(function (index, ele) {
                    $(ele).text(parseInt($(ele).text()) - 1)
                    if (parseInt($(ele).text()) == 0) {
                        $(ele).hide();
                    }
                })
            },
            incrInt:function () {
                $(this).each(function (index, ele) {
                    $(ele).text(parseInt($(ele).text()) + 1)
                })
            },

            decrIntBy:function (num) {
                $(this).text(parseInt($(this).text()) - parseInt(num));
            },
            incrIntBy:function (num) {
                $(this).text(parseInt($(this).text()) + parseInt(num));
            },
            jSmartFetch:function jSmartFetch(data, callback) {
                var selectorString = $(this).selector;
                if (jSmartTemplateCache[selectorString] == undefined) {
                    jSmartTemplateCache[selectorString] = new jSmart($(this).html().replace(/\n/g,'').replace(/^\s*/,''))
                }
                return  jSmartTemplateCache[selectorString].fetch(data);
            },
            jSmartFetchAsync:function (data, callback) {
                var this_ele = $(this);
                require(['jsmart'], function () {
                    jSmart.prototype.getTemplate = function (name) {
                        return $("#" + name).html();
                    }
                    var selectorString = this_ele.selector;
                    if (jSmartTemplateCache[selectorString] == undefined) {
                        jSmartTemplateCache[selectorString] = new jSmart(this_ele.html().replace(/\n/g,'').replace(/^\s*/,''))
                    }
                    callback(jSmartTemplateCache[selectorString].fetch(data));
                })
            },
            actionType:function () {
                return $(this).attr('action-type');
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
                $.error('Method ' + method + ' does not exist on jQuery.WJ');
            }
        }

    })(jQuery);
    return $.WJ;
})
