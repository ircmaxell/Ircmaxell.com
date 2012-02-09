
var ircmaxell = ircmaxell || {};

ircmaxell.Models = ircmaxell.Models || {};

(function($, ircmaxell) {
    ircmaxell.Models.Tag = function(data) {
        data.has_children = !!data.has_children;
        $.extend(this, data, {
            getData: function() {
                return data;
            }
        });
    };
})(jQuery, ircmaxell);