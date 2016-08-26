<?php
if ( is_admin() ) {
    add_action('admin_menu', 'logos_slider_settings');
    add_action('admin_init', 'register_wps_logos_settings');
}
function logos_slider_settings() {
    add_menu_page('wps-awesome-logos','wps-awesome-logos', 'administrator','awesome-logos-admin', 'logos_setting_admin_page');   
    add_submenu_page( 'edit.php?post_type=wps_logos', 'Logos Slider Builder', 'Logos Dashboard', 'manage_options', 'awesome-logos-manage', 'wps_awesome_logos_manage_page');
    remove_menu_page('awesome-logos-admin');
}
function logos_setting_admin_page() {
    $logos_id =  $_GET['s_id'];
    $wps_logos_settings = 'wps_logos_settings'.$logos_id;
    $wps_logos_settings_prev = get_option('wps_logos_settings'.$logos_id);
    $wps_default_settings_arr = wps_default_awesome_logos_settings();
    foreach($wps_default_settings_arr as $key=>$value) {
        if(!isset($wps_logos_settings_prev[$key])) $wps_logos_settings_prev[$key]='';
    }
    ?>
    <!-- accordion html code -->
	<div class="wrapper wps-awesome-logos">
    <h2 class="settings-page-title"> Awesome Logos Customization </h2>
    <h2 class="logos-preview-heading"><?php _e('Preview','logos-slider'); ?></h2>
    <?php
            #Reorder Slides
            if (isset ($_POST['update_slides'])) {
                $i=1;
                global $wpdb, $table_prefix;
                $table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
                $slider_id=$_GET['s_id'];
                foreach ($_POST['order'] as $slide_order) {
                    $slide_order = intval($slide_order);
                    $sql = 'UPDATE '.$table_name.' SET slides_order='.$i.' WHERE post_id='.$slide_order.' and slider_id='.$slider_id;
                    $wpdb->query($sql);
                    $i++;
                }
            }
            #Add Multiple slides into logos
            if(isset($_POST['add_posts']) && isset($_POST['post_id']) && $_POST['add_posts'] == "Insert" && $_POST['wps_logos_cat'] == "unset") {
                global $wpdb, $table_prefix, $post;
                $table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
                $slider_id = $_POST['logosid'];
                $date = date('Y-m-d H:i:s');
                $count = count($_POST['post_id']);
                $values = '';
                for($i = 0; $i < $count; $i++ ) {
                    $id = $_POST['post_id'][$i];
                        if($i == $count-1) $values .= "('$id', '$date', '$slider_id')";
                        else $values .= "('$id', '$date', '$slider_id'),";
                }
                $sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES $values";
                $wpdb->query($sql);
                echo '<div class="wrap">
                            <div id="message" class="updated notice">
                                <p style="color:#111">Slides <strong>added</strong>.</p>
                            </div>
                </div>';
            } elseif (isset($_POST['add_posts']) && $_POST['add_posts'] == "Insert" && $_POST['wps_logos_cat'] != "unset") {
                #Add category slides into logos
                global $wpdb, $table_prefix;
                $table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
                $slider_id = $_POST['logosid'];
                $date = date('Y-m-d H:i:s');
                $cat_slug = $_POST['wps_logos_cat'];
                $args = array(
                    'post_type' => 'wps_logos',
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'awesome-logos-cat',
                            'field'    => 'slug',
                            'terms'    => $cat_slug,
                        ),
                    ),
                    'posts_per_page' => '1000'
                );
                $the_query = new WP_Query( $args ); 
                $i=0;
                if ( $the_query->have_posts() ) {
                    $cat_post_id=array();
                    while( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $pos_id = get_the_ID();
                        array_push($cat_post_id,$pos_id);
                    }       
                    $count = count($cat_post_id);
                    $values = '';
                    for($i = 0; $i < $count; $i++ ) {
                        $id = $cat_post_id[$i];
                        if($i == $count-1)
                            $values .= "('$id', '$date', '$slider_id')";
                        else
                            $values .= "('$id', '$date', '$slider_id'),";
                    }
                    $sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES $values";
                    $wpdb->query($sql);
                    echo '<div class="wrap">
                                <div id="message" class="updated notice">
                                    <p style="color:#111">Slides <strong>added</strong>.</p>
                                </div>
                    </div>'; 
                }
            }
            #deletes logo slides
            if ( isset( $_GET['del_slide'] ) ) {
            global $wpdb, $table_prefix;
            $wps_logos_table = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
                    $where = 'post_id ='.$_GET['del_slide'].' AND slider_id ='.$logos_id;
                    $sql = "DELETE FROM $wps_logos_table WHERE $where";
                    $wpdb->query($sql);
            }
            #returns logos html
            $slider_htm = get_wps_awesome_logos($logos_id);
            echo $slider_htm;
    ?>
<div class="wrap-add-slides wps-awesome-logos">
        <div class="manage-logos-headn"> Add Created Slides </div>
        <div style="margin-right:2%;" class="setting-content">
        <form method="post" class="addImgForm">
            <p align="center"><input type="button" alt="#TB_inline?width=750&height=525&inlineId=popup-add-slides&class=logosmodal" title="Add logo slides" class="thickbox" id="create_save_btn" value="Add Slides" /></p>
        </form>
        <div id="popup-add-slides" style="display:none"> <!-- div for popup -->
            <?php add_thickbox(); ?>
            <h4> Add new slide <a href="<?php echo admin_url('post-new.php?post_type=wps_logos'); ?>"> here</a></h4>
            <?php
            $args = array(
                'post_type' => 'wps_logos',
                'post_status' => 'publish',
                'posts_per_page' => '1000'
            );
            $the_query = new WP_Query( $args );
            $i=0;
            $html ='';
            if ( $the_query->have_posts() ) {
                $html .= '<div style="margin-left: 20px;" >';
                $html .= '<form name="eb-wp-posts" id="eb-wp-posts" action="" method="post" >';
                $html .= '<table class="wp-list-table widefat sliders" >';
                $html .= '<col width="30%">
                          <col width="40%">
                          <col width="30%">
                            <thead>
                            <tr>
                                <th class="sliderid-column">'.__('Select','wps-awesome-logos').'</th>
                                <th class="slidername-column">'.__('Name','wps-awesome-logos').'</th>
                                <th class="slidername-column">'.__('Image','wps-awesome-logos').'</th>
                            </tr>
                            </thead>';
                while ( $the_query->have_posts() ) {
                    $the_query->the_post();
                    $wps_post_id = get_the_ID();
                    $i++; 
                    $html .= '<tr>';
                    $html .= '<td><input type="checkbox" name="post_id[]" value="'.$wps_post_id.'"></td>';  
                    $wpspostlink = get_edit_post_link($wps_post_id);
                    $html .= '<td><span><a href='.$wpspostlink.'>'.get_the_title().'</a></span></td>';
                    $html .= '<td>'.get_the_post_thumbnail( $wps_post_id,array( 80, 80 )).'</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
                $html .= '<div class="overlay_save_button">';
                $wpslogosterms = get_terms('awesome-logos-cat');
                $logoslide_category='<option value="unset"> Select category </option>';
                foreach( $wpslogosterms as $wpslogocats) {
                    $logoslide_category= $logoslide_category.'<option value="'.$wpslogocats->slug.'">'.$wpslogocats->name.'</option>';
                }
                $html .= '<h4 style="display:inline;"> or Add category specific slides </h4>';
                $html .= '<select name="wps_logos_cat">'.$logoslide_category.'</select>';
                $html .= "<input style='width: 25%;' type='submit' id='create_insert_btn' name='add_posts' class='add_posts button-primary' value='Insert' />";
                $html .='</div>';
                $html .= '<input type="hidden" name="logosid" value="'.$_GET['s_id'].'">';
                $html .= '<input type="hidden" name="post_type" class="post_type" value="wps_logos">';
                $html .= '</form>';
                $html .= "</div>";
                echo $html;
                #restore original Post Data
                wp_reset_postdata();
            } else {
                echo "no posts found";
            }
            ?>
        </div> <!-- ends div for popups -->
        <div class="wps-logos-slide-wrapper">
            <?php           
            #reorder
            $slider_id = $_GET['s_id'];
            $logos_posts = array();
            global $wpdb, $table_prefix;
            $table_name = $table_prefix.WPS_AWESOME_LOGOS_TABLE;
            $logos_posts = $wpdb->get_results("SELECT * FROM $table_name WHERE slider_id = '$slider_id' ORDER BY slides_order ASC", OBJECT);
            $count = 0;
            $htmls ='';
                foreach($logos_posts as $logo_post) {
                    $logo_arr[] = $logo_post->post_id;
                    $post = get_post($logo_post->post_id);
                    if(isset($post) and isset($logo_arr)) {
                        if ( in_array($post->ID, $logo_arr) ) {
                            $count++;
                            $post_id = $post->ID;
                            $thumbs_img=get_the_post_thumbnail( $post_id,array( 80, 80 ),array( 'class' => 'added-slides-image'));
                $htmls .= '<form action="" method="post">
                <div id="'.$post->ID.'" class="awesome-logo-reorder"><input type="hidden" name="order[]" value="'.$post->ID.'" /><div>'.$thumbs_img;
                }
                $editplink = get_edit_post_link($post_id);
                $deletelink = $del_sldr_url = admin_url('edit.php?post_type=wps_logos&page=awesome-logos-admin&s_id='.$slider_id.'&del_slide='.$post_id);
                $htmls .= '</div>
                    <span><a href='.$deletelink.' class="dashicons dashicons-no delSlide" id="'.$post_id.'"></a></span>
                    <span><a class="dashicons dashicons-edit" href='.$editplink.'></a></span>
                </div>';
            }
        }
    if ($count > 1) { 
        $htmls .= '<p align="center"><input type="submit" name="update_slides" class="btn_save nt-img" id="create_save_btn" value="Update Order" /></p></form>';
    }
    else { 
        $htmls .= '<p align="center"><input type="submit" name="update_slides" class="btn_save nt-img" id="create_save_btn" value="Update Order" disabled/></p></form>';
    }
        echo $htmls;
    ?>
    </div> <!-- class wrap-add-slides ends here -->
            <?php add_thickbox(); ?>
        </div>
    </div>
    <!-- accordion html code starts -->
<?php
    global $wpdb, $table_prefix;
    $uid = $_GET['s_id'];
    $table_name = $table_prefix.WPS_AWESOME_LOGOS_META;
    $logos_type = $wpdb->get_var("SELECT slider_type FROM $table_name WHERE slider_id = '$uid'");
?>
    <div class="logos-accordion-wrapper wps-awesome-logos">        
    <!-- Basic section -->        
    <?php if($logos_type == 'Slider') { ?>
    <div class="accordion" id="section1"> Basic Customization </div>
    <div class="container">
        <div class="setting-content">
        <form method="post" action="options.php" id="wps_logos_slider_form">
        <?php settings_fields('wps-awesome-logos-group'.$uid); ?>
        <table class="settingstbl">
            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_slider_autoplay"><?php _e('Auto Play','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_slider_autoplay" name="<?php echo $wps_logos_settings;?>[autoplay]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['autoplay'];?>">
                    <input id="wpslogos_autoplay" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked( '1', $wps_logos_settings_prev['autoplay']); ?> >
                    <label for="wpslogos_autoplay"></label>
                </div>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_autopsteps"><?php _e('Autoplay Steps','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[autoplaysteps]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['autoplaysteps']; ?>" id="wps_logos_slider_autopsteps" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_autoptime"><?php _e('Autoplay Time','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[autoplaytime]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['autoplaytime']; ?>" id="wps_logos_slider_autoptime" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_duration"><?php _e('Duration','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[duration]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['duration']; ?>" id="wps_logos_slider_duration" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_swidth"><?php _e('Slide Width','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[slide_width]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['slide_width']; ?>" id="wps_logos_slider_swidth" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_sheight"><?php _e('Slide Height','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[slide_height]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['slide_height']; ?>" id="wps_logos_slider_sheight" /> 
            </td>
            </tr>  

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_cwidth"><?php _e('Container Width','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[cont_width]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['cont_width']; ?>" id="wps_logos_slider_cwidth" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_cheight"><?php _e('Container Height','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[cont_height]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['cont_height']; ?>" id="wps_logos_slider_cheight" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_sspacing"><?php _e('Slide Spacing','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[spacing]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['spacing']; ?>" id="wps_logos_slider_sspacing" /> 
            </td>
            </tr>  

            <tr valign="top">
            <th scope="row"><label for="wps_logos_slider_disp_pieces"><?php _e('Display Pieces','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[display_pieces]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['display_pieces']; ?>" id="wps_logos_slider_disp_pieces" /> 
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_fancybox"><?php _e('Enable Lightbox','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_fancybox" name="<?php echo $wps_logos_settings;?>[logo_fancybox]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['logo_fancybox'];?>">
                    <input id="wpsfancybox" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['logo_fancybox']); ?> >
                    <label for="wpsfancybox"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_slider_drag"><?php _e('Enable Drag','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_slider_drag" name="<?php echo $wps_logos_settings;?>[enable_drag]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['enable_drag'];?>">
                    <input id="wpslogos_drag" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['enable_drag']); ?> >
                    <label for="wpslogos_drag"></label>
                </div>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><?php _e('Hover Pause/Freeze','wps-awesome-logos'); ?></th>
            <td><select name="<?php echo $wps_logos_settings;?>[pause_hover]" style="width: 146px;" >
                <option value="0" <?php if ($wps_logos_settings_prev['pause_hover'] == "0"){ echo "selected";}?> ><?php _e('No Pause','wps-awesome-logos'); ?></option>
                <option value="1" <?php if ($wps_logos_settings_prev['pause_hover'] == "1"){ echo "selected";}?> ><?php _e('Pause : Desktop','wps-awesome-logos'); ?></option>
                <option value="2" <?php if ($wps_logos_settings_prev['pause_hover'] == "2"){ echo "selected";}?> ><?php _e('Pause : Touch device','wps-awesome-logos'); ?></option>
                <option value="3" <?php if ($wps_logos_settings_prev['pause_hover'] == "3"){ echo "selected";}?> ><?php _e('Pause : Desktop,touch device','wps-awesome-logos'); ?></option>
                <option value="4" <?php if ($wps_logos_settings_prev['pause_hover'] == "4"){ echo "selected";}?> ><?php _e('Freeze : Desktop','wps-awesome-logos'); ?></option>
                <option value="8" <?php if ($wps_logos_settings_prev['pause_hover'] == "8"){ echo "selected";}?> ><?php _e('Freeze : Touch device','wps-awesome-logos'); ?></option>
                <option value="12" <?php if ($wps_logos_settings_prev['pause_hover'] == "12"){ echo "selected";}?> ><?php _e('Freeze : Desktop,touch device','wps-awesome-logos'); ?></option>
            </select>

            <tr valign="top">
            <th scope="row"><?php _e('Easing Effect','smooth-slider'); ?></th>
            <td><select name="<?php echo $wps_logos_settings;?>[easing_effects]" >
                <option value="EaseLinear" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseLinear"){ echo "selected";}?> ><?php _e('EaseLinear','wps-awesome-logos'); ?></option>
                <option value="EaseGoBack" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseGoBack"){ echo "selected";}?> ><?php _e('EaseGoBack','wps-awesome-logos'); ?></option>
                <option value="EaseSwing" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseSwing"){ echo "selected";}?> ><?php _e('EaseSwing','wps-awesome-logos'); ?></option>
                <option value="EaseInQuad" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInQuad"){ echo "selected";}?> ><?php _e('EaseInQuad','wps-awesome-logos'); ?></option>
                <option value="EaseOutQuad" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutQuad"){ echo "selected";}?> ><?php _e('EaseOutQuad','wps-awesome-logos'); ?></option>                
                <option value="EaseInOutQuad" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutQuad"){ echo "selected";}?> ><?php _e('EaseInOutQuad','wps-awesome-logos'); ?></option>
                <option value="EaseInCubic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInCubic"){ echo "selected";}?> ><?php _e('EaseInCubic','wps-awesome-logos'); ?></option>
                <option value="EaseOutCubic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutCubic"){ echo "selected";}?> ><?php _e('EaseOutCubic','wps-awesome-logos'); ?></option>
                <option value="EaseInJump" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInJump"){ echo "selected";}?> ><?php _e('EaseInJump','wps-awesome-logos'); ?></option>
                <option value="EaseOutJump" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutJump"){ echo "selected";}?> ><?php _e('EaseOutJump','wps-awesome-logos'); ?></option>
                <option value="EaseOutWave" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutWave"){ echo "selected";}?> ><?php _e('EaseOutWave','wps-awesome-logos'); ?></option>
                <option value="EaseInWave" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInWave"){ echo "selected";}?> ><?php _e('EaseInWave','wps-awesome-logos'); ?></option>
                <option value="EaseInOutBounce" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutBounce"){ echo "selected";}?> ><?php _e('EaseInOutBounce','wps-awesome-logos'); ?></option>
                <option value="EaseOutBounce" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutBounce"){ echo "selected";}?> ><?php _e('EaseOutBounce','wps-awesome-logos'); ?></option>
                <option value="EaseInBounce" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInBounce"){ echo "selected";}?> ><?php _e('EaseInBounce','wps-awesome-logos'); ?></option>
                <option value="EaseInOutBack" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutBack"){ echo "selected";}?> ><?php _e('EaseInOutBack','wps-awesome-logos'); ?></option>
                <option value="EaseInBack" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInBack"){ echo "selected";}?> ><?php _e('EaseInBack','wps-awesome-logos'); ?></option>
                <option value="EaseOutBack" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutBack"){ echo "selected";}?> ><?php _e('EaseOutBack','wps-awesome-logos'); ?></option>
                <option value="EaseInOutElastic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutElastic"){ echo "selected";}?> ><?php _e('EaseInOutElastic','wps-awesome-logos'); ?></option>
                <option value="EaseOutElastic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutElastic"){ echo "selected";}?> ><?php _e('EaseOutElastic','wps-awesome-logos'); ?></option>
                <option value="EaseInElastic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInElastic"){ echo "selected";}?> ><?php _e('EaseInElastic','wps-awesome-logos'); ?></option>
                <option value="EaseInOutCirc" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutCirc"){ echo "selected";}?> ><?php _e('EaseInOutCirc','wps-awesome-logos'); ?></option>
                <option value="EaseOutCirc" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutCirc"){ echo "selected";}?> ><?php _e('EaseOutCirc','wps-awesome-logos'); ?></option>
                <option value="EaseInOutCubic" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutCubic"){ echo "selected";}?> ><?php _e('EaseInOutCubic','wps-awesome-logos'); ?></option>
                <option value="EaseInQuart" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInQuart"){ echo "selected";}?> ><?php _e('EaseInQuart','wps-awesome-logos'); ?></option>
                <option value="EaseOutQuart" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutQuart"){ echo "selected";}?> ><?php _e('EaseOutQuart','wps-awesome-logos'); ?></option>
                <option value="EaseInOutQuart" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutQuart"){ echo "selected";}?> ><?php _e('EaseInOutQuart','wps-awesome-logos'); ?></option>
                <option value="EaseInQuint" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInQuint"){ echo "selected";}?> ><?php _e('EaseInQuint','wps-awesome-logos'); ?></option>
                <option value="EaseOutQuint" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutQuint"){ echo "selected";}?> ><?php _e('EaseOutQuint','wps-awesome-logos'); ?></option>
                <option value="EaseInOutQuint" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutQuint"){ echo "selected";}?> ><?php _e('EaseInOutQuint','wps-awesome-logos'); ?></option>
                <option value="EaseInSine" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInSine"){ echo "selected";}?> ><?php _e('EaseInSine','wps-awesome-logos'); ?></option>
                <option value="EaseOutSine" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutSine"){ echo "selected";}?> ><?php _e('EaseOutSine','wps-awesome-logos'); ?></option>
                <option value="EaseInOutSine" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutSine"){ echo "selected";}?> ><?php _e('EaseInOutSine','wps-awesome-logos'); ?></option>
                <option value="EaseInExpo" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInExpo"){ echo "selected";}?> ><?php _e('EaseInExpo','wps-awesome-logos'); ?></option>
                <option value="EaseOutExpo" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseOutExpo"){ echo "selected";}?> ><?php _e('EaseOutExpo','wps-awesome-logos'); ?></option>
                <option value="EaseInOutExpo" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInOutExpo"){ echo "selected";}?> ><?php _e('EaseInOutExpo','wps-awesome-logos'); ?></option>
                <option value="EaseInCirc" <?php if ($wps_logos_settings_prev['easing_effects'] == "EaseInCirc"){ echo "selected";}?> ><?php _e('EaseInCirc','wps-awesome-logos'); ?></option>            </select>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><?php _e('Play Orientation','smooth-slider'); ?></th>
            <td><select name="<?php echo $wps_logos_settings;?>[play_orient]" style="width: 146px;" >
                <option value="1" <?php if ($wps_logos_settings_prev['play_orient'] == "1"){ echo "selected";}?> ><?php _e('Horizontal','wps-awesome-logos'); ?></option>
                <option value="2" <?php if ($wps_logos_settings_prev['play_orient'] == "2"){ echo "selected";}?> ><?php _e('Vertical','wps-awesome-logos'); ?></option>
                <option value="5" <?php if ($wps_logos_settings_prev['play_orient'] == "5"){ echo "selected";}?> ><?php _e('Horizontally reverse','wps-awesome-logos'); ?></option>
                <option value="6" <?php if ($wps_logos_settings_prev['play_orient'] == "6"){ echo "selected";}?> ><?php _e('Vertical reverse','wps-awesome-logos'); ?></option>
            </select>
            </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="wps_logos_slider_background_color"><?php _e('Background color','wps-awesome-logos'); ?></label></th>
                <td>
                    <input name="<?php echo $wps_logos_settings;?>[background_color]" type="text" class="wps-color-picker-box" data-default-color="" value="<?php echo $wps_logos_settings_prev['background_color']; ?>" id="wps_logos_background_color" /> 
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="wps_logos_slider_title_color"><?php _e('Title color','wps-awesome-logos'); ?></label></th>
                <td>
                    <input name="<?php echo $wps_logos_settings;?>[title_color]" type="text" class="wps-color-picker-box" data-default-color="" value="<?php echo $wps_logos_settings_prev['title_color']; ?>" id="wps_logos_title_color" />
                </td>
            </tr>
        </table>    
        <p class="submit">
            <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
        </p>
        </div> <!-- div content ends -->
    </div> <!-- div container ends -->

    <!-- Advance section -->
    <div class="accordion" id="section3">Arrows</div>
    <div class="container">
        <div class="setting-content">
            
        <table class="settingstbl" >
            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_logo_arrownav"><?php _e('Enable Arrow','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_logo_arrownav" name="<?php echo $wps_logos_settings;?>[arrow_navigation]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['arrow_navigation'];?>">
                    <input id="wpslogos_arrownav" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['arrow_navigation']); ?> >
                    <label for="wpslogos_arrownav"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_logo_arrownav"><?php _e('Show on MouseOver','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_logo_arrowov" name="<?php echo $wps_logos_settings;?>[arrow_hover]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['arrow_hover'];?>">
                    <input id="wpslogos_arrowov" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['arrow_hover']); ?> >
                    <label for="wpslogos_arrowov"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_logo_keynav"><?php _e('Keyboard Navigation','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_logo_keynav" name="<?php echo $wps_logos_settings;?>[arrow_keynav]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['arrow_keynav'];?>">
                    <input id="wpslogos_keynav" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['arrow_keynav']); ?> >
                    <label for="wpslogos_keynav"></label>
                </div>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><?php _e('Select Arrow','wps-awesome-logos'); ?></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[pick_arrow]" id="selected-arrow-hidden" type="hidden" value="">
                <select id="arrow-htmlselect">
                    <?php
                        for($i=1;$i<20;$i++) {
                            $aro_src = AWESOME_LOGOS_PLUGIN_URL.'images/arow/a'.$i.'.png'; ?>
                            <option value="<?php echo $i; ?>" data-imagesrc="<?php echo $aro_src; ?>" <?php if($wps_logos_settings_prev['pick_arrow'] == $i ){ echo 'selected'; } ?> ></option>
                    <?php } ?>
                </select>
            </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
        </p>
        </div>
    </div>
    <div class="accordion" id="section3">Bullets</div>
    <div class="container">
        <div class="setting-content">
        <table class="settingstbl" >
            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_bullet"><?php _e('Enable Bullets','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_bullet" name="<?php echo $wps_logos_settings;?>[bullet_navigation]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['bullet_navigation'];?>">
                    <input id="wpslogos_bullet" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['bullet_navigation']); ?> >
                    <label for="wpslogos_bullet"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_logo_blov"><?php _e('Show on MouseOver','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_logo_blov" name="<?php echo $wps_logos_settings;?>[bullet_hover]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['bullet_hover'];?>">
                    <input id="wpslogos_blov" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['bullet_hover']); ?> >
                    <label for="wpslogos_blov"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_bulletact"><?php _e('Active on MouseOver','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_bulletact" name="<?php echo $wps_logos_settings;?>[bullet_acthover]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['bullet_acthover'];?>">
                    <input id="wpslogos_bact" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['bullet_acthover']); ?> >
                    <label for="wpslogos_bact"></label>
                </div>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><label for="wps_logos_bullet_spacing"><?php _e('Inter Spacing','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[bullet_spacing]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['bullet_spacing']; ?>" id="wps_logos_bullet_spacing" /> 
            </td>
            </tr>  
            
            <tr valign="top">
            <th scope="row"><label for="wps_logos_bullet_steps"><?php _e('Bullet Steps','wps-awesome-logos'); ?></label></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[bulletsteps]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['bulletsteps']; ?>" id="wps_logos_bullet_steps" /> 
            </td>
            </tr>  


            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_blcntr"><?php _e('Align Center','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_blcntr" name="<?php echo $wps_logos_settings;?>[bullet_centr]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['bullet_centr'];?>">
                    <input id="wpslogos_blcntr" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['bullet_centr']); ?> >
                    <label for="wpslogos_blcntr"></label>
                </div>
            </td>
            </tr>

            <tr valign="top"> 
            <th scope="row"><label for="wps_logos_orient"><?php _e('Show Vertical','wps-awesome-logos'); ?></label></th>
            <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_orient" name="<?php echo $wps_logos_settings;?>[bullet_verti]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['bullet_verti'];?>">
                    <input id="wpslogos_orient" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['bullet_verti']); ?> >
                    <label for="wpslogos_orient"></label>
                </div>
            </td>
            </tr>

            <tr valign="top">
            <th scope="row"><?php _e('Select Bullet','wps-awesome-logos'); ?></th>
            <td>
                <input name="<?php echo $wps_logos_settings;?>[pick_bullet]" id="selected-bullet-hidden" type="hidden" value="">
                <select id="bullet-htmlselect" >
                    <?php
                        for($j=1;$j<16;$j++) {
                            $nav_src = AWESOME_LOGOS_PLUGIN_URL.'images/nav/b'.$j.'.png'; ?>
                            <option value="<?php echo $j; ?>" data-imagesrc="<?php echo $nav_src; ?>" <?php if($wps_logos_settings_prev['pick_bullet'] == $j ){ echo 'selected="selected"'; } ?> ></option>
                    <?php } ?>
                </select>
            </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
        </p>
        </div>
    </div>
<?php } # slider ends
    else {  ?> 
          <div class="accordion" id="section1"> Basic </div>
            <div class="container">
                <div class="setting-content">
                <form method="post" action="options.php" id="wps_logos_slider_form">
                <?php settings_fields('wps-awesome-logos-group'.$uid); ?>
                <table class="settingstbl" >
                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_fancybox"><?php _e('Enable Lightbox','wps-awesome-logos'); ?></label></th>
                    <td>
                        <div class="wps-switch wps-switchnone">
                            <input type="hidden" id="wps_logos_fancybox" name="<?php echo $wps_logos_settings;?>[logo_fancybox]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['logo_fancybox'];?>">
                            <input id="wpsfancybox" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['logo_fancybox']); ?> >
                            <label for="wpsfancybox"></label>
                        </div>
                    </td>
                    </tr>

                    <?php if($logos_type == 'Responsive-Grid') { ?>
                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_cwidth"><?php _e('Container Width','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[cont_width]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['cont_width']; ?>" id="wps_logos_slider_cwidth" /> 
                    </td>
                    </tr>
                    <?php } else { ?>
                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_swidth"><?php _e('Item Width','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[slide_width]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['slide_width']; ?>" id="wps_logos_slider_swidth" /> 
                    </td>
                    </tr>
                    <?php } ?>

                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_sheight"><?php _e('Item Height','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[slide_height]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['slide_height']; ?>" id="wps_logos_slider_sheight" /> 
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_gridcol"><?php _e('Number of Columns','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[grid_column]" type="number" min="1" value="<?php echo $wps_logos_settings_prev['grid_column']; ?>" id="wps_logos_slider_gridcol" /> 
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_sspacing"><?php _e('Slide Spacing','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[spacing]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['spacing']; ?>" id="wps_logos_slider_sspacing" /> 
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_border_thickness"><?php _e('Border Thickness','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[border_thickness]" type="number" min="0" value="<?php echo $wps_logos_settings_prev['border_thickness']; ?>" id="wps_logos_border_thickness" /> 
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><label for="wps_logos_slider_border_color"><?php _e('Border color','wps-awesome-logos'); ?></label></th>
                    <td>
                        <input name="<?php echo $wps_logos_settings;?>[border_color]" type="text" class="wps-color-picker-box" data-default-color="#222222" value="<?php echo $wps_logos_settings_prev['border_color']; ?>" id="wps_logos_border_color" /> 
                    </td>
                    </tr>  

                    <tr valign="top">
                        <th scope="row"><label for="wps_logos_slider_title_color"><?php _e('Title color','wps-awesome-logos'); ?></label></th>
                        <td>
                            <input name="<?php echo $wps_logos_settings;?>[title_color]" type="text" class="wps-color-picker-box" data-default-color="" value="<?php echo $wps_logos_settings_prev['title_color']; ?>" id="wps_logos_title_color" />
                        </td>
                    </tr>
                </table>    
                    <p class="submit">
                        <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
                    </p>
                    </div> <!-- div content ends -->
                </div> <!-- div container ends -->
      <?php  } ?>

    <!-- title customization -->
    <div class="accordion" id="section9"> Title </div>
        <div class="container">
            <div class="setting-content">
            <table class="settingstbl" >
                <tr valign="top">
                <th scope="row"><label for="wps_logos_oshowtitle"><?php _e('Title as Overlay','wps-awesome-logos'); ?></label></th>
                <td>
                <div class="wps-switch wps-switchnone">
                    <input type="hidden" id="wps_logos_oshowtitle" name="<?php echo $wps_logos_settings;?>[show_title]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['show_title'];?>">
                    <input id="wpsotitle" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['show_title']); ?> >
                    <label for="wpsotitle"></label>
                </div>
                </td>
                </tr>

                <tr valign="top"> 
                <th scope="row"><label for="wps_logos_logo_keynav"><?php _e('Tool Tip','wps-awesome-logos'); ?></label></th>
                <td>
                    <div class="wps-switch wps-switchnone">
                        <input type="hidden" id="wps_logos_logo_tooltip" name="<?php echo $wps_logos_settings;?>[title_tooltip]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['title_tooltip'];?>">
                        <input id="wpslogos_tooltip" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['title_tooltip']); ?> >
                        <label for="wpslogos_tooltip"></label>
                    </div>
                </td>
                </tr>

                <tr valign="top">
                <th scope="row"><?php _e('Font','wps-awesome-logos'); ?></th>
                <td>
                <select name="<?php echo $wps_logos_settings;?>[title_font]" id="awesome_logos_title_font" style="width:200px;" >
                    <option value="Arial,Helvetica,sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
                    <option value="'Arial Narrow',sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
                    <option value="'Arial Black',sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
                    <option value="'Bookman Old Style',Bookman,serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
                    <option value="'Comic Sans MS',cursive" <?php if ($wps_logos_settings_prev['title_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
                    <option value="'Courier New',Courier,monospace" <?php if ($wps_logos_settings_prev['title_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
                    <option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($wps_logos_settings_prev['title_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
                    <option value="Cursive" <?php if ($wps_logos_settings_prev['title_font'] == "Cursive"){ echo "selected";}?> >Cursive</option>
                    <option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
                    <option value="Verdana,Geneva,sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
                    <option value="Tahoma,Geneva,sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
                    <option value="Trebuchet MS,sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
                    <option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
                    <option value="fantasy" <?php if ($wps_logos_settings_prev['title_font'] == "fantasy"){ echo "selected";}?> >fantasy</option>   
                    <option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
                    <option value="'Times New Roman',Times,serif" <?php if ($wps_logos_settings_prev['title_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
                    <option value="Georgia,serif" <?php if ($wps_logos_settings_prev['title_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
                    <option value="Garamond,serif" <?php if ($wps_logos_settings_prev['title_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
                    <option value="Impact,fantasy" <?php if ($wps_logos_settings_prev['title_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
                    <option value="sans-serif" <?php if ($wps_logos_settings_prev['title_font'] == "sans-serif"){ echo "selected";}?> >sans-serif</option>
                    <option value="serif" <?php if ($wps_logos_settings_prev['title_font'] == "serif"){ echo "selected";}?> >serif</option>               
                    <option value="monospace" <?php if ($wps_logos_settings_prev['title_font'] == "monospace"){ echo "selected";}?> >monospace</option>
                </select>
                </td>
                </tr>

                <tr valign="top">
                <th scope="row"><?php _e('Font Size','wps-awesome-logos'); ?></th>
                <td>
                    <input type="number" name="<?php echo $wps_logos_settings;?>[title_size]" id="awesome_logos_title_size" value="<?php echo $wps_logos_settings_prev['title_size']; ?>" min="1" />
                </td>
                </tr>

                <tr valign="top">
                <th scope="row"><?php _e('Font Style','wps-awesome-logos'); ?></th>
                <td>
                <select name="<?php echo $wps_logos_settings;?>[title_style]" id="awesome_logos_title_style" >
                    <option value="bold" <?php if ($wps_logos_settings_prev['title_style'] == "bold"){ echo "selected";}?> ><?php _e('Bold','wps-awesome-logos'); ?></option>
                    <option value="bold italic" <?php if ($wps_logos_settings_prev['title_style'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','wps-awesome-logos'); ?></option>
                    <option value="italic" <?php if ($wps_logos_settings_prev['title_style'] == "italic"){ echo "selected";}?> ><?php _e('Italic','wps-awesome-logos'); ?></option>
                    <option value="normal" <?php if ($wps_logos_settings_prev['title_style'] == "normal"){ echo "selected";}?> ><?php _e('Normal','wps-awesome-logos'); ?></option>
                </select>
                </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
            </p>
            </div>
        </div>
            <!-- Hover Effects -->
            <div class="accordion" id="section3">Hover Effects</div>
            <div class="container">
                <div class="setting-content">         
                <table class="settingstbl" >

                    <tr valign="top"> 
                    <th scope="row"><label for="wps_logos_logo_arrownav"><?php _e('Scale Image','wps-awesome-logos'); ?></label></th>
                    <td>
                        <div class="wps-switch wps-switchnone">
                            <input type="hidden" id="wps_logos_logo_hoverscale" name="<?php echo $wps_logos_settings;?>[hover_scale]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['hover_scale'];?>">
                            <input id="wpslogos_hoverscale" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['hover_scale']); ?> >
                            <label for="wpslogos_hoverscale"></label>
                        </div>
                    </td>
                    </tr>

                    <tr valign="top"> 
                    <th scope="row"><label for="wps_logos_logo_arrownav"><?php _e('Inner Box Shadow','wps-awesome-logos'); ?></label></th>
                    <td>
                         <div class="wps-switch wps-switchnone">
                            <input type="hidden" id="wps_logos_logo_hovershadowin" name="<?php echo $wps_logos_settings;?>[hover_inshadow]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['hover_inshadow'];?>">
                            <input id="wpslogos_hovershadowin" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['hover_inshadow']); ?> >
                            <label for="wpslogos_hovershadowin"></label>
                        </div>
                    </td>
                    </tr>

                    <tr valign="top"> 
                    <th scope="row"><label for="wps_logos_logo_arrownav"><?php _e('Outer Box Shadow','wps-awesome-logos'); ?></label></th>
                    <td>
                         <div class="wps-switch wps-switchnone">
                            <input type="hidden" id="wps_logos_logo_hovershadowout" name="<?php echo $wps_logos_settings;?>[hover_outshadow]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['hover_outshadow'];?>">
                            <input id="wpslogos_hovershadowout" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['hover_outshadow']); ?> >
                            <label for="wpslogos_hovershadowout"></label>
                        </div>
                    </td>
                    </tr>

                    <tr valign="top"> 
                    <th scope="row"><label for="wps_logos_grid_grayscale"><?php _e('Gray Scale Filter','wps-awesome-logos'); ?></label></th>
                    <td>
                        <div class="wps-switch wps-switchnone">
                            <input type="hidden" id="wps_logos_grid_grayscale" name="<?php echo $wps_logos_settings;?>[grayscale_effect]" class="hidden_check" value="<?php echo $wps_logos_settings_prev['grayscale_effect'];?>">
                            <input id="wps_logos_grd_grayscale" class="cmn-toggle wps-toggle-round" type="checkbox" <?php checked('1', $wps_logos_settings_prev['grayscale_effect']); ?> >
                            <label for="wps_logos_grd_grayscale"></label>
                        </div>
                    </td>
                    </tr>

                </table>
                <p class="submit">
                    <input type="submit" class="wps-apply-settings" id="create_save_btn" value="<?php _e('Apply Changes') ?>" />
                </p>
                </form>
                </div>
            </div>  
        <!-- Shortcode section -->
        <div class="accordion" id="section3">Shortcode</div>
        <div class="container">
            <div class="setting-content">
                <p style="text-align: center;"> <code>[awesomelogos <?php echo $uid; ?>]</code></p>
            </div>
        </div>
        <!-- Template-Tag section -->
        <div class="accordion" id="section4">Template Tag</div>
        <div class="container">
            <div class="setting-content">
                <p style="text-align: center;"> <code> &lt;?php if(function_exists('get_awesome_logos')){ get_awesome_logos('<?php echo $uid; ?>'); }?&gt;</code></p>
            </div>
        </div>
    </div>
    </div>
    <script>
        jQuery(document).ready(function(){
          jQuery('.wps-color-picker-box').wpColorPicker();
        }); 
    </script>
    <!-- accordion html code ends-->
<?php
} #logos_settings_admin_page ends
add_action('load-wps-awesome-logos_page_wps-awesome-logos-setting','logos_settings_admin_page');
function register_wps_logos_settings() {
        #get list of all names
        $logos = wps_get_logos_info();
        foreach ($logos as $logo) {
            $setid = $logo->slider_id;
            register_setting( 'wps-awesome-logos-group'.$setid, 'wps_logos_settings'.$setid );
        }
} ?>