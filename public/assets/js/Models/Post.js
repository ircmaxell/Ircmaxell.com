
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
    ircmaxell.Models.Post = new function() {
        $.extend(this, {
            getData: function(post) {
                return {
                    body: post.getBody(),
                    children: map(post.getChildren(), this.getData),
                    icon: post.getIcon(),
                    summary: post.getSummary(),
                    thumbnail: post.getThumbnail(),
                    title: post.getTitle()
                };
            },
            loadPost: function(data) {
                if (data.type && data.data && this[data.type]) {
                    return new this[data.type](data.data);
                }
                return null;
            },
            loadPosts: function(data) {
                var self = this;

                return map(data, function(post) { return self.loadPost(post); });
            }
        });
    };
})(jQuery, ircmaxell);