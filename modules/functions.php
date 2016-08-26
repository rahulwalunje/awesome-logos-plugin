<?php
function get_wps_awesome_logos( $uid ) {
    global $wpdb, $table_prefix;
    $table_name = $table_prefix.WPS_AWESOME_LOGOS_META;
    $logos_type = $wpdb->get_var("SELECT slider_type FROM $table_name WHERE slider_id = '$uid'");
    $slider_id = $uid;
    $logos_posts = array();
    global $wpdb, $table_prefix;
    $table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
    $logos_posts = $wpdb->get_results("SELECT * FROM $table_name WHERE slider_id = '$slider_id' ORDER BY slides_order ASC", OBJECT);
    $count = 0;
    wp_enqueue_script( 'wps-logos-js', AWESOME_LOGOS_PLUGIN_URL.'js/awesome.logos.js', array('jquery'),1.0,false);
if($logos_type == 'Slider') {
    #code for slider
    wp_enqueue_script( 'logos-slider-script', AWESOME_LOGOS_PLUGIN_URL.'js/jssor.slider.min.js', array('jquery'),1.0, false);
    wp_enqueue_style( 'sliderstyle-css', AWESOME_LOGOS_PLUGIN_URL.'css/sliderstyle.css',false, 1.0, 'all');
    $wps_logos_settings = 'wps_logos_settings'.$slider_id;   
    $wps_logos_settings_prev = get_option($wps_logos_settings);
    $wps_default_settings_arr = wps_default_awesome_logos_settings();

    foreach($wps_default_settings_arr as $key=>$value){
        if(!isset($wps_logos_settings_prev[$key])) $wps_logos_settings_prev[$key]='';
    }
    if($wps_logos_settings_prev['logo_fancybox']==1){
        wp_enqueue_script( 'lightcase-pack-js', AWESOME_LOGOS_PLUGIN_URL.'js/lightcase.js', array('jquery'),1.0,false);
        wp_enqueue_style( 'lightcase-pack-css', AWESOME_LOGOS_PLUGIN_URL.'css/lightcase.css',false, 1.0,'all');
    }
    if(count($logos_posts)!='0') {
    $cwidth = $wps_logos_settings_prev['cont_width'];
    $cheight = $wps_logos_settings_prev['cont_height'];
    $background_color = $wps_logos_settings_prev['background_color'];
        $tooltip =''; $hovereffectscale=''; $grayscales=''; $hovershadow=''; $light_box=''; $act_light_box='';
        if( $wps_logos_settings_prev['title_tooltip']=='1' ) $tooltip = 'data-tooltips="act"';
        if( $wps_logos_settings_prev['hover_scale']=='1' ) $hovereffectscale = 'data-imgscale="act"';
        if( $wps_logos_settings_prev['hover_inshadow']=='1' ) $hovershadow = 'data-boxshadow="in"';
        if( $wps_logos_settings_prev['hover_outshadow']=='1' ) $hovershadow = 'data-boxshadow="out"';
        if( $wps_logos_settings_prev['grayscale_effect']=='1' ) $grayscales ='data-grayfilter="act"';
        if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
            $light_box ='data-lightbox="lightcase"';
            $act_light_box ='data-rel="lightcase"';  
        }
        $html ='<div id="slider'.$uid.'_container" class="wps-awesome-logo-slider" '.$tooltip.' '.$hovereffectscale.' '.$hovershadow.' '.$grayscales.' '.$light_box.' data-logostype="slider" style="width:'.$cwidth.'px; height:'.$cheight.'px; background-color:'.$background_color.';display:none;">';
        $html.='<!-- Slides Container -->
        <div u="slides" class="wps-logos-slide-wrapper">';
            foreach($logos_posts as $logo_post) {
                $logo_arr[] = $logo_post->post_id;
                $post = get_post($logo_post->post_id);
                if ( in_array($post->ID, $logo_arr) ) {
                    $count++;
                    $post_id = $post->ID;
                    $logo_title = $post->post_title;
                    $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
                }
                #fetch post meta values
                $wps_logo_alt_text = get_post_meta($post_id, '_wps_logo_alt_text',true); 
                $wps_logo_link_target = get_post_meta($post_id, '_wps_logo_link_target' ,true);
                $wps_logo_img_url = get_post_meta($post_id, '_wps_logo_img_url' ,true);
                
                if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
                    $hyper_link = 'href="'.$img_url.'"';
                    if(!empty($wps_logo_img_url)) 
                    $hyper_link = 'href="'.$wps_logo_img_url.'"';
                }
                else {    
                    $wps_logo_slide_link = get_post_meta($post_id, '_wps_logo_slide_link',true);
                    $slide_link=$wps_logo_slide_link;
                    $hyper_link = 'href="'.$slide_link.'"';
                    if(empty($slide_link)) $hyper_link ='';
                }
                if(empty($wps_logo_alt_text)) $alt_attr = '';
                else {
                    $alt_attr = 'alt="'.$wps_logo_alt_text.'"';
                }
                if(!empty($wps_logo_img_url)) {
                    $img_url = $wps_logo_img_url;
                }
                if( $wps_logo_link_target == "1") $attr = 'target="_blank"';
                else $attr = '';
                if($wps_logos_settings_prev['title_style'] == "bold" or $wps_logos_settings_prev['title_style'] == "bold italic" ){$title_weight = "bold";} else {$title_weight = "normal";}
                if($wps_logos_settings_prev['title_style'] == "italic" or $wps_logos_settings_prev['title_style'] == "bold italic"){$title_style = "italic";} else {$title_style = "normal";}
                $logos_title_style='font-family:'.$wps_logos_settings_prev['title_font'].';font-size:'.$wps_logos_settings_prev['title_size'].'px;font-weight:'.$title_weight.';font-style:'.$title_style.';color:'.$wps_logos_settings_prev['title_color'].';text-align: center;margin-top: 30%;';
                $html.='<div class="wps_logo_slide_item">
                            <a u="image" class="wps_logos_img" '.$act_light_box.' '.$hyper_link.' '.$attr.' title="'.$logo_title.'">
                                <img u="image" '.$alt_attr.' src="'.$img_url.'" class="wps_slide_img" />';
                                if($wps_logos_settings_prev['show_title']=='1' ) {
                                    $html.='<div class="meta">
                                                <span class="logos_overlay"> </span>
                                                <h3 style="'.$logos_title_style.'"> '.$logo_title.' </h3>
                                            </div>';
                                }
                                if( $wps_logos_settings_prev['title_tooltip']=='1' ) {
                                    $html.='<span>'.$logo_title.'</span>';
                                }
                    $html.='</a>
                        </div>';
            }         
        $html.='</div>';
        if($wps_logos_settings_prev['arrow_navigation']=='1') {
            $html.= '<span u="arrowleft" class="jssora'.$wps_logos_settings_prev['pick_arrow'].'l jssoral" style="width: 40px; height: 50px; top: 40px; left: 0px;"></span>
            <span u="arrowright" class="jssora'.$wps_logos_settings_prev['pick_arrow'].'r jssorar" style="width: 40px; height: 50px; top: 40px; right: 0px"></span>';
        }
        #for bullets
        if($wps_logos_settings_prev['bullet_navigation']=='1') {
        $html.= '<div u="navigator" class="logosb'.$wps_logos_settings_prev['pick_bullet'].' logobl" style="position: absolute; bottom: 16px; left: 6px;">
                <!-- bullet navigator item prototype -->
                <div u="prototype" style="position: absolute; width: 18px; height: 18px;"></div>
            </div>';
        }
   $html.='</div>
    <!-- Slider End -->';
$easingeffect = '$JssorEasing$.$'.$wps_logos_settings_prev['easing_effects'];
if( $wps_logos_settings_prev['play_orient'] == '1' || $wps_logos_settings_prev['play_orient'] == '5' ) {
    $dragorient = 1;
}
elseif( $wps_logos_settings_prev['play_orient'] == '2' || $wps_logos_settings_prev['play_orient'] == '6' ) {
    $dragorient = 2;
}
if($wps_logos_settings_prev['enable_drag']=='0') {
    $dragorient = 0;   
}
$ArrowHover = 2; $BulletHover = 2; $BulletOrient = 1; $BulletActive = 1; 
if($wps_logos_settings_prev['arrow_hover']=='1') $ArrowHover = 1;
if($wps_logos_settings_prev['bullet_hover']=='1') $BulletHover = 1;
if($wps_logos_settings_prev['bullet_verti']=='1') $BulletOrient = 2;
if($wps_logos_settings_prev['bullet_acthover']=='1') $BulletActive = 2;
$html.= '<script>
jQuery(document).ready(function ($) {
        var options = {
            $AutoPlay: '.$wps_logos_settings_prev['autoplay'].',                         
            $AutoPlaySteps: '.$wps_logos_settings_prev['autoplaysteps'].',                      
            $AutoPlayInterval:'.$wps_logos_settings_prev['autoplaytime'].',
            $PauseOnHover: '.$wps_logos_settings_prev['pause_hover'].',
            $ArrowKeyNavigation: '.$wps_logos_settings_prev['arrow_keynav'].',
            $SlideEasing: '.$easingeffect.',
            $SlideDuration: '.$wps_logos_settings_prev['duration'].',
            $SlideWidth: '.$wps_logos_settings_prev['slide_width'].',
            $SlideHeight: '.$wps_logos_settings_prev['slide_height'].',
            $SlideSpacing: '.$wps_logos_settings_prev['spacing'].',
            $DisplayPieces: '.$wps_logos_settings_prev['display_pieces'].',
            $PlayOrientation: '.$wps_logos_settings_prev['play_orient'].',
            $DragOrientation: '.$dragorient.',';
            if($wps_logos_settings_prev['arrow_navigation']=='1') {
            $html.='$ArrowNavigatorOptions: {
                 $Class: $JssorArrowNavigator$,
                 $ChanceToShow: '.$ArrowHover.',
                 $AutoCenter: 2,
                 $Steps : '.$wps_logos_settings_prev['autoplaysteps'].',
                },';
            }
            #bullets
            if($wps_logos_settings_prev['bullet_navigation']=='1') {
            $html.='$BulletNavigatorOptions: {
                    $Class: $JssorBulletNavigator$,
                    $ChanceToShow: '.$BulletHover.',
                    $AutoCenter: '.$wps_logos_settings_prev['bullet_centr'].',
                    $SpacingX: '.$wps_logos_settings_prev['bullet_spacing'].',
                    $Orientation: '.$BulletOrient.',
                    $ActionMode: '.$BulletActive.',
                    $Steps: '.$wps_logos_settings_prev['bulletsteps'].',
                },';
            }
            $html.='};
            var wps_logos_slider = new $JssorSlider$("slider'.$uid.'_container", options);';
        #responsive code
        #remove responsive code if you don\'t want the slider scales while window resizes
        $html.='function ScaleSlider() {
            var bodyWidth = document.body.clientWidth;
            if (bodyWidth)
                wps_logos_slider.$ScaleWidth(Math.min(bodyWidth,'. $cwidth.'));
            else
                window.setTimeout(ScaleSlider, 30);
        }
        ScaleSlider();
        $(window).bind("load", ScaleSlider);
        $(window).bind("resize", ScaleSlider);
        $(window).bind("orientationchange", ScaleSlider);
        $("#slider'.$uid.'_container").show();
    });
</script>'; 
return $html;
}
else{
    echo '<div class="wps_logos_no_preview" > No preview yet. Add Slides Now! </div>';
} #ends check slide count >0
    #code for slider ends
}
elseif($logos_type == 'Simple-Grid') {
#code for simple Grids
if(count($logos_posts)!='0') {
    $wps_logos_settings = 'wps_logos_settings'.$slider_id;   
    $wps_logos_settings_prev = get_option($wps_logos_settings);
    $wps_default_settings_arr = wps_default_awesome_logos_settings();
    foreach($wps_default_settings_arr as $key=>$value){
        if(!isset($wps_logos_settings_prev[$key])) $wps_logos_settings_prev[$key]='';
    }
    $ghtml ='';
    wp_enqueue_style( 'user-style-grid-css', AWESOME_LOGOS_PLUGIN_URL.'css/gridstyle.css',false, 1.0, 'all');
    wp_enqueue_script( 'lightcase-pack-js', AWESOME_LOGOS_PLUGIN_URL.'js/lightcase.js', array('jquery'),1.0,false);
    wp_enqueue_style( 'lightcase-pack-css', AWESOME_LOGOS_PLUGIN_URL.'css/lightcase.css',false, 1.0, 'all');

    $column_items = $wps_logos_settings_prev['grid_column'];
    $rows_items = count($logos_posts)/$column_items;
    $rows_items = ceil($rows_items);
    $containerWidth = $column_items*($wps_logos_settings_prev['slide_width'] + (2*$wps_logos_settings_prev['spacing']));
    $containerHeight = $rows_items*($wps_logos_settings_prev['slide_height'] + (2*$wps_logos_settings_prev['spacing']));
    $tooltip =''; $hovereffectscale=''; $grayscales=''; $hovershadow=''; $light_box=''; $act_light_box='';
    if( $wps_logos_settings_prev['title_tooltip']=='1' ) $tooltip = 'data-tooltips="act"';
    if( $wps_logos_settings_prev['hover_scale']=='1' ) $hovereffectscale = 'data-imgscale="act"';
    if( $wps_logos_settings_prev['hover_inshadow']=='1' ) $hovershadow = 'data-boxshadow="in"';
    if( $wps_logos_settings_prev['hover_outshadow']=='1' ) $hovershadow = 'data-boxshadow="out"';
    if( $wps_logos_settings_prev['grayscale_effect']=='1' ) $grayscales ='data-grayfilter="act"';
    if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
            $light_box ='data-lightbox="lightcase"';
            $act_light_box ='data-rel="lightcase"';  
    }
    $ghtml.='<div id="grids'.$uid.'_container" class="wps_logos_simple_grid wps_logos_layout" '.$tooltip.' '.$hovereffectscale.' '.$hovershadow.' '.$grayscales.' '.$light_box.' data-logostype="simplegrid" style="max-width:'.$containerWidth.'px;height:'.$containerHeight.'px;display:none;" >';
                foreach( $logos_posts as $logo_post ) {
                    $logo_arr[] = $logo_post->post_id;
                    $post = get_post($logo_post->post_id);
                    if ( in_array($post->ID, $logo_arr) ) {
                        $count++;
                        $post_id = $post->ID;
                        $logo_title = $post->post_title;
                        $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
                    }
                    #fetch post meta values
                    $wps_logo_alt_text = get_post_meta($post_id, '_wps_logo_alt_text',true); 
                    $wps_logo_link_target = get_post_meta($post_id, '_wps_logo_link_target' ,true);
                    $wps_logo_img_url = get_post_meta($post_id, '_wps_logo_img_url' ,true);
                    if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
                       $hyper_link = 'href="'.$img_url.'"';
                       if(!empty($wps_logo_img_url)) 
                       $hyper_link = 'href="'.$wps_logo_img_url.'"';
                    }
                    else {    
                       $wps_logo_slide_link = get_post_meta($post_id, '_wps_logo_slide_link',true);
                       $slide_link=$wps_logo_slide_link;
                       $hyper_link = 'href="'.$slide_link.'"';
                       if(empty($slide_link)) $hyper_link ='';
                    }
                    if(empty($wps_logo_alt_text)) $alt_attr = '';
                    else {
                        $alt_attr = 'alt="'.$wps_logo_alt_text.'"';
                    }
                    if(!empty($wps_logo_img_url)) {
                        $img_url = $wps_logo_img_url;
                    }
                    if( $wps_logo_link_target == "1") $attr = 'target="_blank"';
                    else $attr = '';
                    if($wps_logos_settings_prev['title_style'] == "bold" or $wps_logos_settings_prev['title_style'] == "bold italic" ){$title_weight = "bold";} else {$title_weight = "normal";}
                    if($wps_logos_settings_prev['title_style'] == "italic" or $wps_logos_settings_prev['title_style'] == "bold italic"){$title_style = "italic";} else {$title_style = "normal";}

                    $logos_title_style='font-family:'.$wps_logos_settings_prev['title_font'].';font-size:'.$wps_logos_settings_prev['title_size'].'px;font-weight:'.$title_weight.';font-style:'.$title_style.';color:'.$wps_logos_settings_prev['title_color'].';';
                    $ghtml.='<div class="wps_simple_grid_item" style="height:'.$wps_logos_settings_prev['slide_height'].'px; margin:'.$wps_logos_settings_prev['spacing'].'px; width:'.$wps_logos_settings_prev['slide_width'].'px;">
                        <div class="wps_logos_simple_grid_image">
                            <a class="wps_logos_img" '.$hyper_link.' '.$act_light_box.' '.$attr.' title="'.$logo_title.'">
                            <img style="border: '.$wps_logos_settings_prev['border_thickness'].'px solid '.$wps_logos_settings_prev['border_color'].';" '.$alt_attr.' src="'.$img_url.'" />';
                            if($wps_logos_settings_prev['show_title']=='1' ) {
                                $ghtml.= '<div class="meta">
                                        <span class="logos_overlay"></span>
                                        <h3 style="'.$logos_title_style.'">'.$logo_title.'</h3>
                                </div>';
                            }
                            if( $wps_logos_settings_prev['title_tooltip']=='1' ) {
                                $ghtml.= '<span>'.$logo_title.'</span>';
                            }
                           $ghtml.= '</a>
                        </div>
                    </div>';
            }
            $ghtml.= '</div><div style="clear:both"></div>';
        return $ghtml; 
    }
    else{
    echo '<div class="wps_logos_no_preview"> No preview yet. Add Slides Now! </div>';
    } #end check slide count >0
      #end code for simple Grids
}
elseif($logos_type == 'Responsive-Grid') {
#code for responsive Grids
if(count($logos_posts)!='0') {
    $wps_logos_settings = 'wps_logos_settings'.$slider_id;   
    $wps_logos_settings_prev = get_option($wps_logos_settings);
    $wps_default_settings_arr = wps_default_awesome_logos_settings();
    foreach($wps_default_settings_arr as $key=>$value){
        if(!isset($wps_logos_settings_prev[$key])) $wps_logos_settings_prev[$key]='';
    }
    $ghtml ='';
    wp_enqueue_style( 'user-style-grid-css', AWESOME_LOGOS_PLUGIN_URL.'css/gridstyle.css',false, 1.0, 'all');
    wp_enqueue_script( 'lightcase-pack-js', AWESOME_LOGOS_PLUGIN_URL.'js/lightcase.js', array('jquery'),1.0,false);
    wp_enqueue_style( 'lightcase-pack-css', AWESOME_LOGOS_PLUGIN_URL.'css/lightcase.css',false, 1.0, 'all');
    $column_items = $wps_logos_settings_prev['grid_column'];
    $rows_items = count($logos_posts)/$column_items;
    $rows_items = ceil($rows_items);
    $containerWidth = $wps_logos_settings_prev['cont_width'];
    $slideitemWidth = 100/$column_items;
     $staticWidth = $containerWidth/$column_items;
    $containerHeight = $rows_items*($wps_logos_settings_prev['slide_height'] + (2*$wps_logos_settings_prev['spacing']));
    $tooltip =''; $hovereffectscale=''; $grayscales=''; $hovershadow=''; $light_box=''; $act_light_box='';
    $itemWidth = 'data-staticwidth="'.$staticWidth.'"';
    if( $wps_logos_settings_prev['title_tooltip']=='1' ) $tooltip = 'data-tooltips="act"';
    if( $wps_logos_settings_prev['hover_scale']=='1' ) $hovereffectscale = 'data-imgscale="act"';
    if( $wps_logos_settings_prev['hover_inshadow']=='1' ) $hovershadow = 'data-boxshadow="in"';
    if( $wps_logos_settings_prev['hover_outshadow']=='1' ) $hovershadow = 'data-boxshadow="out"';
    if( $wps_logos_settings_prev['grayscale_effect']=='1' ) $grayscales ='data-grayfilter="act"';
    if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
            $light_box ='data-lightbox="lightcase"';
            $act_light_box ='data-rel="lightcase"';  
    }
    $ghtml.='<div id="gridres'.$uid.'_container" class="wps_logos_simple_grid wps_logos_layout" data-rowitem="'.$rows_items.'" data-logostype="responsive" '.$tooltip.' '.$hovereffectscale.' '.$hovershadow.' '.$grayscales.' '.$light_box.' '.$itemWidth.' style="max-width:'.$containerWidth.'px;height:'.$containerHeight.'px;display:none;" >';
                foreach( $logos_posts as $logo_post ) {
                    $logo_arr[] = $logo_post->post_id;
                    $post = get_post($logo_post->post_id);
                    if ( in_array($post->ID, $logo_arr) ) {
                        $count++;
                        $post_id = $post->ID;
                        $logo_title = $post->post_title;
                        $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
                    }
                    #fetch post meta values
                    $wps_logo_alt_text = get_post_meta($post_id, '_wps_logo_alt_text',true); 
                    $wps_logo_link_target = get_post_meta($post_id, '_wps_logo_link_target' ,true);
                    $wps_logo_img_url = get_post_meta($post_id, '_wps_logo_img_url' ,true);
                    if( $wps_logos_settings_prev['logo_fancybox']=='1' ) {
                       $hyper_link = 'href="'.$img_url.'"';
                       if(!empty($wps_logo_img_url)) 
                       $hyper_link = 'href="'.$wps_logo_img_url.'"';
                    }
                    else {    
                       $wps_logo_slide_link = get_post_meta($post_id, '_wps_logo_slide_link',true);
                       $slide_link=$wps_logo_slide_link;
                       $hyper_link = 'href="'.$slide_link.'"';
                       if(empty($slide_link)) $hyper_link ='';
                    }
                    if(empty($wps_logo_alt_text)) $alt_attr = '';
                    else {
                        $alt_attr = 'alt="'.$wps_logo_alt_text.'"';
                    }
                    if(!empty($wps_logo_img_url)) {
                        $img_url = $wps_logo_img_url;
                    }
                    if( $wps_logo_link_target == "1") $attr = 'target="_blank"';
                    else $attr = '';
                    if($wps_logos_settings_prev['title_style'] == "bold" or $wps_logos_settings_prev['title_style'] == "bold italic" ){$title_weight = "bold";} else {$title_weight = "normal";}
                    if($wps_logos_settings_prev['title_style'] == "italic" or $wps_logos_settings_prev['title_style'] == "bold italic"){$title_style = "italic";} else {$title_style = "normal";}
                    $logos_title_style='font-family:'.$wps_logos_settings_prev['title_font'].';font-size:'.$wps_logos_settings_prev['title_size'].'px;font-weight:'.$title_weight.';font-style:'.$title_style.';color:'.$wps_logos_settings_prev['title_color'].';';
                    $ghtml.='<div class="wps_simple_grid_item" style="height:'.$wps_logos_settings_prev['slide_height'].'px; margin:'.$wps_logos_settings_prev['spacing'].'px; width:'.$slideitemWidth.'%;">
                        <div class="wps_logos_simple_grid_image">
                            <a class="wps_logos_img" '.$hyper_link.' '.$act_light_box.' '.$attr.' title="'.$logo_title.'">
                            <img style="border: '.$wps_logos_settings_prev['border_thickness'].'px solid '.$wps_logos_settings_prev['border_color'].';" '.$alt_attr.' src="'.$img_url.'" />';
                            if($wps_logos_settings_prev['show_title']=='1' ) {
                                $ghtml.= '<div class="meta">
                                        <span class="logos_overlay"></span>
                                        <h3 style="'.$logos_title_style.'">'.$logo_title.'</h3>
                                </div>';
                            }
                            if( $wps_logos_settings_prev['title_tooltip']=='1' ) {
                                $ghtml.= '<span>'.$logo_title.'</span>';
                            }
                           $ghtml.= '</a>
                        </div>
                    </div>';
            }
            $ghtml.= '</div>';
        return $ghtml; 
    }
    else{
        echo '<div class="wps_logos_no_preview"> No preview yet. Add Slides Now! </div>';
    } #end check slide count >0
    #end code for responsive Grids
 }
} #end Function
?>