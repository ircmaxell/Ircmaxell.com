
var ircmaxell = ircmaxell || {};

ircmaxell.Resources = ircmaxell.Resources || {};

(function($, ircmaxell) {

    ircmaxell.Resources.Posts = new function() {
        var root = '/posts/';
        $.extend(this, {
            load: function(type, callback) {
                var uri = root;
                if (typeof type !== 'function') {
                    uri += type;
                } else {
                    callback = type;
                }
                $.get(uri, function(data) {
                    var results = [];
                    for (var i = 0; i < data.length; i++) {
                        results.push(new ircmaxell.Models.Post(data[i]));
                    }
                    callback(results);
                }, 'json');
            }
        });
    }

})(jQuery, ircmaxell);