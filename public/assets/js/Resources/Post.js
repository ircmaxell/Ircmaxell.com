
var ircmaxell = ircmaxell || {};

ircmaxell.Resources = ircmaxell.Resources || {};

(function($, ircmaxell) {

    ircmaxell.Resources.Post = new function() {
        var root = '/post/';
        $.extend(this, {
            load: function(type, id, callback) {
                var uri = root + type + '/' + id;
                $.get(uri, function(data) {
                    callback(ircmaxell.Models.Post.loadPost(data));
                }, 'json');
            }
        });
    }

})(jQuery, ircmaxell);