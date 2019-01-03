(function($) {
   var intv = setInterval(function () {
       if ($('#portfolio-wrapper')) {
           $('#portfolio-wrapper').attr('multiple', 'multiple');
           $('#portfolio-wrapper').attr('name', 'settings[portfolio][terms][]');
       }
    }, 100);

})(jQuery);