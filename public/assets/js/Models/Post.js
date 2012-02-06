
var ircmaxell = ircmaxell || {};

ircmaxell.Models = ircmaxell.Models || {};

(function($, ircmaxell) {
    var map = function(array, callback) {
        if (Array.prototype.map) {
            return array.map(callback);
        } else {
            var ret = [];
            for (var i = 0; i < array.length; i++) {
                ret.push(callback(array[i]));
            }
            return ret;
        }
    };
    ircmaxell.Models.Post = function(data) {
        data.has_children = !!data.has_children;
        $.extend(this, data, {
            getData: function() {
                return data;
            },
            children: []
        });
        if (data.children) {
            for (var i = 0; i < data.children.length; i++) {
                this.children[i] = new ircmaxell.Models.Post(data.children[i]);
            }
        }
    };
})(jQuery, ircmaxell);