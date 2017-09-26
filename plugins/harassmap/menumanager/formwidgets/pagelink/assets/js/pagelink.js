'use strict';

$(document).render(function() {
    var selector = $('.page-link-select'),
        selected = function () {
            var value = selector.val();

            $('.control').hide();
            $('.control-' + value).show();
        };

    selected();
    selector.on('change', selected);
});