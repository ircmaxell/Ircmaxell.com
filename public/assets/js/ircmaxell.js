
var ircmaxell = (function($) {
    var ircmaxell = new function() {

        $.extend(this, {

            });
    };


    $(function() {
        var deferred = $.Deferred();
        ircmaxell.Resources.Posts.load(function(posts) {
            var temp, post, li, div;
            for (var i = 0; i < posts.length; i++) {
                li = $(document.createElement('li'));
                $('ul.posts').append(li);
                div = $(document.createElement('div'));
                post = posts[i];
                temp = new ircmaxell.Template('/assets/templates/posts/' + post.type + '.html');
                temp.render(div, post, {}, (function(div, li, post) {
                    return function() {
                        if (!post.getData().tags || ! post.getData().tags[0]) {
                            div.height(0);
                            li.append(div);
                            div.animate({height: auto}, 2);
                            return;
                        }
                        var newDiv = document.createElement('div');
                        var tag = post.getData().tags[0];
                        var source = $('.tag-' + tag.replace(' ', '-')).offset();
                        var destination = li.offset()
                        newDiv.css('left', source.left);
                        newDiv.css('top', source.top);
                        newDiv.css('position', 'absolute');
                        newDiv.css('opacity', 0);
                        newDiv.appendChild(div);
                        $('body').append(newDiv);
                        li.height(div.height());
                        newDiv.animate({
                            left: destination.left,
                            top: destination.top,
                            opacity: 1
                        }, 2, function() {
                            $(li).append(div);
                            $('body').remove(newDiv);
                        });
                    };
                })(div, li, post));
            }
        });
        ircmaxell.Resources.Tags.load(function(tags) {
            var li;
            for (var i = 0; i < tags.length; i++) {
                li = $(document.createElement('li'));
                li.addClass('tag-' + tags[i].getData().name.replace(' ', '-'));
                li.html(tags[i].getData().name);
                $('ul.menu-tags').append(li);
            }
        });
        
    });

    return ircmaxell;
    
})(jQuery);
