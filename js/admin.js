jQuery(document).ready(function() {
      //accordian
      jQuery('.accordion').accordion({
            defaultOpen: 'some_id',
            defaultOpen: '',  
            speed: 'medium'  
      });
      //checkbox
      jQuery(".wps-toggle-round").click(function() {
            if(jQuery(this).prop("checked")==true) {
                  jQuery(this).prev('.hidden_check').val(1);
            } else {
                  jQuery(this).prev('.hidden_check').val(0);
            }
      });
      //select box
      jQuery('#arrow-htmlselect').ddslick({
         data:'',
         onSelected: function(data){
             jQuery('#selected-arrow-hidden').val(data.selectedIndex+1);
         }
      });
      jQuery('#bullet-htmlselect').ddslick({
         data:'',
         onSelected: function(data){
             jQuery('#selected-bullet-hidden').val(data.selectedIndex+1);
         }
      });
      //dataTables call
      jQuery("#data-table-slider").DataTable({
            responsive: true
      });
      //sorting
      jQuery(function() {
           jQuery(".wps-logos-slide-wrapper").sortable({ items: ".awesome-logo-reorder" });
      });
      //dashboard:validations
      jQuery(".create_go").click(function() {
            if(jQuery("#wps_logos_slidername").val() == "" ) {
               alert("Please Name your Logos");
               return false;
            }
            var logotype = jQuery("input[name='logostype']");
            if(logotype[0].checked == false && logotype[1].checked == false && logotype[2].checked == false) {
               alert("Select type of Logos");
               return false;
            }
      });
      //settings:validations
      jQuery(".wps-apply-settings").click(function() {
         var autosteps = jQuery("#wps_logos_slider_autopsteps").val();
         if( autosteps != undefined && (autosteps == "" || autosteps < 0 || isNaN(autosteps)) ) {
               alert("Please fill Autoplay Steps!");
               return false;
         }
         var autoptime = jQuery("#wps_logos_slider_autoptime").val();
         if( autoptime != undefined && (autoptime == "" || autoptime < 0 || isNaN(autoptime)) ) {
               alert("Please fill Autoplay Time!");
               return false;
         }
         var duration = jQuery("#wps_logos_slider_duration").val();
         if( duration != undefined && (duration == "" || duration < 0 || isNaN(duration)) ) {
               alert("Please fill Duration!");
               return false;
         }
         var slidewidth = jQuery("#wps_logos_slider_swidth").val();
         if( slidewidth != undefined && (slidewidth == "" || slidewidth < 0 || isNaN(slidewidth)) ) {
               alert("Please fill Slide Width!");
               return false;
         }
         var slideheight = jQuery("#wps_logos_slider_sheight").val();
         if( slideheight != undefined && (slideheight == "" || slideheight < 0 || isNaN(slideheight)) ) {
               alert("Please fill Slide Height!");
               return false;
         }
         var cont_width = jQuery("#wps_logos_slider_cwidth").val();
         if( cont_width != undefined && (cont_width == "" || cont_width < 0 || isNaN(cont_width)) ) {
               alert("Please fill Container Width!");
               return false;
         }
         var cont_height = jQuery("#wps_logos_slider_cheight").val();
         if( cont_height != undefined && (cont_height == "" || cont_height < 0 || isNaN(cont_height)) ) {
               alert("Please fill Container Height!");
               return false;
         }
         var sspacing = jQuery("#wps_logos_slider_sspacing").val();
         if( sspacing != undefined && (sspacing == "" || sspacing < 0 || isNaN(sspacing)) ) {
               alert("Please fill Slide Spacing!");
               return false;
         }
         var disp_pieces = jQuery("#wps_logos_slider_disp_pieces").val();
         if( disp_pieces != undefined && (disp_pieces == "" || disp_pieces < 0 || isNaN(disp_pieces)) ) {
               alert("Please fill Display Pieces!");
               return false;
         }
         var bulletspacing = jQuery("#wps_logos_bullet_spacing").val();
         if( bulletspacing != undefined && (bulletspacing == "" || bulletspacing < 0 || isNaN(bulletspacing)) ) {
               alert("Please fill Inter Spacing!");
               return false;
         }
         var titlesize = jQuery("#awesome_logos_title_size").val();
         if( titlesize != undefined && (titlesize == "" || titlesize < 0 || isNaN(titlesize)) ) {
               alert("Please fill Font Size!");
               return false;
         }
         var gridcols = jQuery("#wps_logos_slider_gridcol").val();
         if( gridcols != undefined && (gridcols == "" || gridcols < 0 || isNaN(gridcols)) ) {
               alert("Please fill Number of Columns!");
               return false;
         }
         var border_thickness = jQuery("#wps_logos_border_thickness").val();
         if( border_thickness != undefined && (border_thickness == "" || border_thickness < 0 || isNaN(border_thickness)) ) {
               alert("Please fill Border Thickness!");
               return false;
         }
      });
});