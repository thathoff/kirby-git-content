(function(window, document, $) {
    var $widget = $('#git-commit-and-push-content-widget');

    var gcapcUpdate = function (pushOrPull) {
        var method = (pushOrPull && pushOrPull === 'push') ? 'push' : 'pull';

        $widget.find(' .gcapc-status').html(method + 'ing…');

        $.ajax({
            url: window.gcapcSettings + method,
            dataType: 'json',
            method: 'get',
            success: function(response) {
                $widget.find(' .gcapc-status').html(response.message);
            },
            error: function(response) {
                $widget.find(' .gcapc-status').html('Error, see Console or try again.');
                console.error(response);
            }
        });
    };

    $widget.find('a').on('click', function (e) {
        var $this = $(this);
        var target = $this.attr('href');
        var method = false;

        if (target == window.gcapcSettings + 'push') {
            method = 'push';
        } else if (target == window.gcapcSettings + 'pull') {
            method = 'pull';
        }

        if (method) {
            e.preventDefault();
            e.stopImmediatePropagation();

            gcapcUpdate(method);
        }
    });
    
    
})(window, document, jQuery);