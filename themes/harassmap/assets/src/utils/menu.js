'use strict';

export const initMenu = () => {
    $('.navbar-toggler').on('click', () => {
        let $navbar = $('.navbar'),
            $nav = $('#main_nav');

        if ($navbar.hasClass('open')) {
            $navbar.removeClass('open');
            $nav.slideUp();
        } else {
            $navbar.addClass('open');
            $nav.slideDown();
        }
    });

    $('.nav .nav-item.dropdown a').on('click', function (event) {
        let $this = $(this);

        // very crude check to see if we are in mobile
        if ($this.parents('.open').length > 0) {
            event.preventDefault();

            $this.siblings('.dropdown-menu').slideToggle();
        }
    });
};