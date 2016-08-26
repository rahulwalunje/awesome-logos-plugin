(function($){
$(document).ready(function() {
    var simple_grid_handler = $('.wps_simple_grid_item').parent(); 
    var logosType = simple_grid_handler.data('logostype');
    var slider_handle = $('.wps-logos-slide-wrapper').parent();
    var wps_slide_item = slider_handle.find('.wps_logo_slide_item');
    var slidertype = slider_handle.data('logostype');
    var orignalItemHeight = new Array();
    var orignalItemWidth = new Array();
    if(logosType == 'simplegrid' || logosType == 'responsive') {
    $(simple_grid_handler).show();
    $.each($(simple_grid_handler), function(index, value) {
      var simple_grid_items = $(this).find('.wps_simple_grid_item'); 
      var tooltips = $(this).data('tooltips');
      if (tooltips == 'act') {
         simple_grid_items.addClass('wps_tooltip');
      }
      var boxshadow = $(this).data('boxshadow');
      if (boxshadow == 'in') {
         simple_grid_items.find('.wps_logos_simple_grid_image').addClass('logos-inshadow');
      }
      else if (boxshadow == 'out') {
         simple_grid_items.find('.wps_logos_simple_grid_image').addClass('logos-outshadow');
      }
      var grayscales = $(this).data('grayfilter');
      if (grayscales == 'act') {
          simple_grid_items.find('.wps_logos_img img').addClass('wps_desaturate');
      }
      var scaleimg = $(this).data('imgscale');
      simple_grid_items.mouseenter(function(){
          if (grayscales == 'act') {
             $(this).find('.wps_logos_img img').css({'filter':'none','-webkit-filter': 'grayscale(0%)'});
          }
          if (scaleimg == 'act') {
              $(this).find('.wps_logos_img img').css({'transform': 'scale(1.12,1.12)','-webkit-transform': 'scale(1.12,1.12)','-moz-transform': 'scale(1.12,1.12)','-o-transform': 'scale(1.12,1.12)','-ms-transform': 'scale(1.12,1.12)','transition':'all 0.3s ease-in-out','-webkit-transition':'all 0.3s ease-in-out','-moz-transition':'all 0.3s ease-in-out','-o-transition':'all 0.3s ease-in-out'});
          }
      });
      simple_grid_items.mouseleave(function(){
          if (grayscales == 'act') {
             $(this).find('.wps_logos_img img').css({'filter':'grayscale(100%)','-webkit-filter': 'grayscale(100%)'});
          }
          if (scaleimg == 'act') {
              $(this).find('.wps_logos_img img').css({'transform': 'scale(1,1)','-webkit-transform': 'scale(1,1)','-moz-transform': 'scale(1,1)','-o-transform': 'scale(1,1)','-ms-transform': 'scale(1,1)','transition':'all 0.5s ease-in-out','-webkit-transition':'all 0.5s ease-in-out','-moz-transition':'all 0.5s ease-in-out','-o-transition':'all 0.5s ease-in-out'});
          }   
      });
      var datalightbox =  $(this).data('lightbox');
      if (datalightbox=='lightcase') {
          $(this).find(".wps_simple_grid_item .wps_logos_img").addClass('glightbox');
          $('a[data-rel^=lightcase]').lightcase();
      }
      if (logosType == 'responsive') {
          orignalItemHeight[index] = simple_grid_items.eq(0).height();
          orignalItemWidth[index] = simple_grid_items.eq(0).width();
          var staticitemWidth = $(this).data('staticwidth');
          if(orignalItemWidth[index] < staticitemWidth) {   // for small width - mobile and tab divices
              orignalItemWidth[index] = staticitemWidth; 
          }
      }
    }); 
} // simple and responsive grid end
if (logosType == 'responsive') {
        // code for responsive grid:responsiveness
        function responsiveLogoGrid() {
           $.each($(simple_grid_handler), function(index, value) {
              var simple_grid_itemsrz = $(this).find('.wps_simple_grid_item'); 
              var GridrowItems = $(this).data('rowitem');
              ItemWidth = simple_grid_itemsrz.eq(0).width();
              ItemHeight = (orignalItemHeight[index]*ItemWidth)/orignalItemWidth[index];
              simple_grid_itemsrz.height(ItemHeight);
              $(this).height(GridrowItems*ItemHeight);
          });
        }
        $(window).resize(function() { 
           responsiveLogoGrid();
        });
}
if(slidertype == 'slider'){
  $.each($(slider_handle), function(index, value) {
      var wps_slide_item = $(this).find('.wps_logo_slide_item');
      var tooltips = $(this).data('tooltips');
      if (tooltips == 'act') {
         wps_slide_item.addClass('wps_tooltip');
      }
      var boxshadow = $(this).data('boxshadow');
      if (boxshadow == 'in') {
         wps_slide_item.addClass('logos-inshadow');
      }
      else if (boxshadow == 'out') {
         wps_slide_item.addClass('logos-outshadow');
      }
      var grayscales = $(this).data('grayfilter');
      if (grayscales == 'act') {
         wps_slide_item.find('img.wps_slide_img').addClass('wps_desaturate');
      }
      var scaleimg = $(this).data('imgscale');
      wps_slide_item.mouseenter(function(){
          if (grayscales == 'act') {
            $(this).find('img.wps_slide_img').css({'filter':'none','-webkit-filter': 'grayscale(0%)'});
          }
          if (scaleimg == 'act') {
            $(this).find('img.wps_slide_img').css({'transform': 'scale(1.12,1.12)','-webkit-transform': 'scale(1.12,1.12)','-moz-transform': 'scale(1.12,1.12)','-o-transform': 'scale(1.12,1.12)','-ms-transform': 'scale(1.12,1.12)','transition':'all 0.3s ease-in-out','-webkit-transition':'all 0.3s ease-in-out','-moz-transition':'all 0.3s ease-in-out','-o-transition':'all 0.3s ease-in-out'});
          }
      });
      wps_slide_item.mouseleave(function(){
          if (grayscales == 'act') {
            $(this).find('img.wps_slide_img').css({'filter':'grayscale(100%)','-webkit-filter': 'grayscale(100%)'});
          }
          if (scaleimg == 'act') {
            $(this).find('img.wps_slide_img').css({'transform': 'scale(1,1)','-webkit-transform': 'scale(1,1)','-moz-transform': 'scale(1,1)','-o-transform': 'scale(1,1)','-ms-transform': 'scale(1,1)','transition':'all 0.5s ease-in-out','-webkit-transition':'all 0.5s ease-in-out','-moz-transition':'all 0.5s ease-in-out','-o-transition':'all 0.5s ease-in-out'});
          }   
      });
      var datalightbox =  $(this).data('lightbox');
      if (datalightbox=='lightcase') {
          $(this).find(".wps_logo_slide_item .wps_logos_img").addClass('slightbox');
          $('a[data-rel^=lightcase]').lightcase();
      }
  }); // each end
  } //slider end
});
})(jQuery);