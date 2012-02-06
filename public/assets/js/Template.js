
var ircmaxell = ircmaxell || {};

(function($, ircmaxell) {
    var templateCache = {};

    var templateCallbacks = {};

    ircmaxell.Template = function(file, onReady) {
        var tmpl = this;
        if (!templateCache[file]) {
            templateCallbacks[file] = $.Callbacks();
            /**
            * Start the template fetch now
            */
            $.get(file, function(data) {
                templateCache[file] = data;
                templateCallbacks[file].fire(data);
                if (onReady) {
                    onReady(tmpl);
                }
            }, 'html');
        } else {
            if (onReady) {
                onReady(tmpl);
            }
        }
        $.extend(this, {
            render: function(target, data, partials, callback) {
                this.renderToString(data, partials, function(html) {
                    $(target).html(html);
                    if (callback) {
                        callback();
                    }
                });
            },
            renderToString: function(data, partials, callback, onDone) {
                var value = '';
                if (templateCache[file]) {
                    value = $.mustache(templateCache[file], data, partials);
                    if (callback) {
                        callback(value);
                    }
                } else {
                    templateCallbacks[file].add(function(template) {
                        callback($.mustache(template, data, partials));
                    })
                }
                if (onDone) {
                    onDone();
                }
                return value;
            }
        });
    }
})(jQuery, ircmaxell);