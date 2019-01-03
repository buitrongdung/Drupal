(function($) {
    "use strict"
    $('#testimonial-slider').owlCarousel({
        loop:true,
        margin:15,
        dots : true,
        nav: false,
        autoplay : true,
        responsive:{
            0: {
                items:1
            },
            992:{
                items:2
            }
        }
    });

})(jQuery);