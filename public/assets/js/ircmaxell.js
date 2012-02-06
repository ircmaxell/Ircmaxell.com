
var ircmaxell = (function($) {
    var ircmaxell = new function() {

        $.extend(this, {

        });
    };


    $(function() {
        var template = new ircmaxell.Template('/assets/templates/posts.html');
        ircmaxell.Resources.Posts.load(function(posts) {
            new ircmaxell.Template('/assets/templates/post.html', function(postTemplate) {
                template.render('body', {}, {}, function() {
                    for (var i = 0; i < posts.length; i++) {
                         $('ul.posts').append(postTemplate.renderToString(posts[i].getData()));
                    }
                });
            });

        });
    });

    return ircmaxell;
})(jQuery);
