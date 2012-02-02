
var ircmaxell = (function($) {
    var ircmaxell = new function() {

        $.extend(this, {

        });
    };


    $(function() {
        var template = new ircmaxell.Template('/assets/templates/posts.html');
        ircmaxell.Resources.Post.loadPosts(function(posts) {
            new ircmaxell.Template('/assets/templates/post.html', function(postTemplate) {
                var data = posts.map(function(post) {
                    return postTemplate.renderToString(post.getData());
                });
                template.render('body', {posts: data});
            });

        });
    });

    return ircmaxell;
})(jQuery);
