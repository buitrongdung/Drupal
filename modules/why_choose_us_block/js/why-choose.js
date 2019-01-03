(function($) {
    "use strict"
    // Owl Carousel
    $('#about-slider').owlCarousel({
        items:1,
        loop:true,
        margin:15,
        nav: true,
        navText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        dots : true,
        autoplay : true,
        animateOut: 'fadeOut'
    });
})(jQuery);