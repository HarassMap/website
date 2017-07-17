(function ($) {
    $.fn.FloatLabel = function (options) {
        var defaults = {
                populatedClass: 'populated',
                focusedClass: 'focused'
            },
            settings = $.extend({}, defaults, options);

        return this.each(function () {
            var element = $(this),
                label = element.find('label'),
                input = element.find('textarea, input');

            input.attr("placeholder", label.text());

            if (input.val() !== '') {
                element.addClass(settings.populatedClass);
            }

            input.on('focus', function () {
                element.addClass(settings.focusedClass);
            });

            input.on('blur', function () {
                element.removeClass(settings.focusedClass);

                if (!input.val()) {
                    element.removeClass(settings.populatedClass);
                }
            });

            input.on('keyup', function () {
                element.addClass(settings.populatedClass);
            });

            input.on('change', function () {
                if ($(this).val()) {
                    element.addClass(settings.populatedClass);
                } else {
                    element.removeClass(settings.populatedClass);
                }
            });
        });
    };
})(jQuery);