
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
            render: function(target, data, partials) {
                this.renderToString(data, partials, function(html) {
                    $(target).html(html);
                });
            },
            renderToString: function(data, partials, callback) {
                var value = '';
                if (templateCache[file]) {
                    value = $.mustache(templateCache[file], data, partials);
                    if (callback) {
                        callback(value);
                    }
                } else {
                    templateCallbacks[file].add(function(template) {
                        callback.html($.mustache(template, data, partials));
                    })
                }
                return value;
            }
        });
    }
})(jQuery, ircmaxell);