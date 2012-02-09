
var ircmaxell = ircmaxell || {};

ircmaxell.Resources = ircmaxell.Resources || {};

(function($, ircmaxell) {

    ircmaxell.Resources.Tags = new function() {
        var root = '/tags/';
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
                        results.push(new ircmaxell.Models.Tag(data[i]));
                    }
                    callback(results);
                }, 'json');
            }
        });
    }

})(jQuery, ircmaxell);