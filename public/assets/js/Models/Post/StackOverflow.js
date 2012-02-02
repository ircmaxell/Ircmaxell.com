
var ircmaxell = ircmaxell || {};

ircmaxell.Models = ircmaxell.Models || {};

ircmaxell.Models.Post = ircmaxell.Models.Post || {};

(function($, ircmaxell) {
    ircmaxell.Models.Post.StackOverflow = function(data) {
        $.extend(this, {
            getBody: function() {
                return data.body;
            },
            getChildren: function() {
                return [];
            },
            getData: function() {
                return ircmaxell.Models.Post.getData(this);
            },
            getIcon: function() {
                return 'stackoverflow.png';
            },
            getSummary: function() {
                return data.body;
            },
            getThumbnail: function() {
                return '';
            },
            getTitle: function() {
                return data.title;
            },
            hasChildren: function() {
                return false;
            }
        });
    };
})(jQuery, ircmaxell);