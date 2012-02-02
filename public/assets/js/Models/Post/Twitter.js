
var ircmaxell = ircmaxell || {};

ircmaxell.Models = ircmaxell.Models || {};

ircmaxell.Models.Post = ircmaxell.Models.Post || {};

(function($, ircmaxell) {
    ircmaxell.Models.Post.Twitter = function(data) {
        $.extend(this, {
            getBody: function() {
                return data.text;
            },
            getChildren: function() {
                return [];
            },
            getData: function() {
                return ircmaxell.Models.Post.getData(this);
            },
            getIcon: function() {
                return 'twitter.png';
            },
            getSummary: function() {
                return data.text;
            },
            getThumbnail: function() {
                return '';
            },
            getTitle: function() {
                return data.text.substr(0, 30);
            },
            hasChildren: function() {
                return false;
            }
        });
    };
})(jQuery, ircmaxell);