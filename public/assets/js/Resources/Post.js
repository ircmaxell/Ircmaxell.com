
var ircmaxell = ircmaxell || {};

ircmaxell.Resources = ircmaxell.Resources || {};

(function($, ircmaxell) {

    ircmaxell.Resources.Post = new function() {
        var root = '/post/';
        $.extend(this, {
            loadPost: function(type, id, callback) {
                var uri = root + type + '/' + id;
                $.get(uri, function(data) {
                    callback(ircmaxell.Models.Post.loadPost(data));
                }, 'json');
            },
            loadPosts: function(type, callback) {
                var uri = root;
                if (typeof type !== 'function') {
                    uri += type;
                } else {
                    callback = type;
                }
                $.get(uri, function(data) {
                    callback(ircmaxell.Models.Post.loadPosts(data));
                }, 'json');
            }
        });
    }

})(jQuery, ircmaxell);