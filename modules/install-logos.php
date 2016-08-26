<?php
	function logos_slider_enqueue_scripts() {
		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'wps-logos-js',AWESOME_LOGOS_PLUGIN_URL.'js/awesome.logos.js', array('jquery'), 1.0,false);
	}
	function logos_slider_adminpage_scripts() {
		
		if( is_admin() ){
			wp_register_script('jquery', false, false, false, false);
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'logos-ddslick', AWESOME_LOGOS_PLUGIN_URL.'js/jquery.ddslick.min.js', array('jquery'), 1.0,false);
			wp_enqueue_script( 'accordions-script', AWESOME_LOGOS_PLUGIN_URL.'js/jquery.accordion.js',array('jquery'), 1.0, false);
			wp_enqueue_script( 'jquery.dataTables', AWESOME_LOGOS_PLUGIN_URL.'js/jquery.dataTables.min.js', array( 'jquery' ),1.0, false);
			wp_enqueue_script( 'logos_slider_admin_js', AWESOME_LOGOS_PLUGIN_URL.'js/admin.js',array('jquery'), 1.0, false);
			wp_enqueue_style( 'logos_slider_admin_css', AWESOME_LOGOS_PLUGIN_URL.'css/admin.css',false, 1.0, 'all');
			wp_enqueue_style( 'datatable_css', AWESOME_LOGOS_PLUGIN_URL.'css/jquery.dataTables.min.css',false, 1.0, 'all');
			
			if ( isset($_GET['page']) && 'awesome-logos-admin' == $_GET['page'] ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}

		}

	}

	#register CPT
	function create_wps_post_type() {
		
		if( !post_type_exists('wps_logos') ){
			$labels = array('name'               => _x( 'Awesome logos slides', 'post type general name', 'wps-awesome-logos' ),'singular_name'      => _x( 'Awesome logos slide', 'post type singular name', 'wps-awesome-logos' ),'menu_name'          => _x( 'Awesome Logos', 'awesome-logos-admin', 'wps-awesome-logos' ),'name_admin_bar'     => _x( 'Wps logo', 'add new on admin bar', 'wps-awesome-logos' ),'add_new'            => _x( 'Add New Slide', 'wps', 'wps-awesome-logos' ),'add_new_item'       => __( 'Add New Slide', 'wps-awesome-logos' ),'new_item'           => __( 'New Logo', 'wps-awesome-logos' ),'edit_item'          => __( 'Edit Slide', 'wps-awesome-logos' ),'view_item'          => __( 'View Slide', 'wps-awesome-logos' ),'all_items'          => __( 'All Slides', 'wps-awesome-logos' ),'search_items'       => __( 'Search Slides', 'wps-awesome-logos' ),'not_found'          => __( 'No slides found.', 'wps-awesome-logos' ),'not_found_in_trash' => __( 'No slides found in Trash.', 'wps-awesome-logos' ));
			$args = array('labels' => $labels,'public' => true,'publicly_queryable' => true,'show_ui' => true, 'show_in_menu' => true,'query_var' => true,'rewrite' => array('slug' => 'wps_logos' ,'with_front' => false),'capability_type' => 'post','has_archive' => true, 'hierarchical' => false,'menu_position' => null,'taxonomy' => array('awesome-logos-cat'),'supports' => array('title','thumbnail'),'menu_icon' => 'dashicons-images-alt',);
			register_post_type('wps_logos',$args);
			$labels = array('name'                       => _x( 'Categories', 'wps-awesome-logos' ),'singular_name'              => _x( 'Logo Category', 'wps-awesome-logos' ),'search_items'               => __( 'Search Logos Category' ),'popular_items'              => __( 'Popular Logos Categories' ),'all_items'                  => __( 'All Categories' ),'parent_item'                => null,'parent_item_colon'          => null,'edit_item'                  => __( 'Edit Logos' ),'update_item'                => __( 'Update Logos' ),'add_new_item'               => __( 'Add New Logos Category' ),'new_item_name'              => __( 'New Logos Name' ),'separate_items_with_commas' => __( 'Separate cats with commas' ),'add_or_remove_items'        => __( 'Add or remove Logos' ),'choose_from_most_used'      => __( 'Choose from the most used Logos' ),'not_found'                  => __( 'No Logos found.' ),'menu_name'                  => __( 'Categories' ),);
			$args = array('hierarchical'          => true,'labels'                => $labels,'show_ui'               => true,'show_admin_column'     => true,'update_count_callback' => '_update_post_term_count','query_var'             => true,'rewrite'               => array( 'slug' => 'Logo' ),);
			register_taxonomy( 'awesome-logos-cat', 'wps_logos', $args );
			#change default texts on edit posts
			add_action('do_meta_boxes', 'changewps_cpt_image_box');
			function changewps_cpt_image_box() {
				remove_meta_box( 'postimagediv', 'wps_logos', 'side' );
				add_meta_box('postimagediv', __('Slide Image'), 'post_thumbnail_meta_box', 'wps_logos', 'normal', 'high');
			}

		}

		#custom post true end
	}

	
	function wps_logos_update_cfields( $post_id) {
		#fetch saved post meta
		$wps_logo_slide_link_url = get_post_meta($post_id,'_wps_logo_slide_link',true);
		$wps_logo_slide_target = get_post_meta($post_id,'_wps_logo_link_target',true);
		$wps_logo_fetch_url = get_post_meta($post_id,'_wps_logo_img_url',true);
		$wps_logo_alt = get_post_meta($post_id,'_wps_logo_alt_text',true);
		#new meta for updation
		$slide_link_url_temp= isset($_POST['wps_logo_slide_link'])?$_POST['wps_logo_slide_link']:
		'';
		$slide_link_target_temp= isset($_POST['wps_logo_link_target'])?$_POST['wps_logo_link_target']:
		'';
		$slide_logo_fetch_temp= isset($_POST['wps_logo_img_url'])?$_POST['wps_logo_img_url']:
		'';
		$wps_alt_text_temp= isset($_POST['wps_logo_alt_text'])?$_POST['wps_logo_alt_text']:
		'';
		#update meta values
		
		if( $wps_logo_slide_link_url != $slide_link_url_temp) {
			$slide_link_url_temp = esc_url($slide_link_url_temp);
			update_post_meta($post_id,'_wps_logo_slide_link', $slide_link_url_temp);
		}

		
		if( $wps_logo_slide_target != $slide_link_target_temp) {
			update_post_meta($post_id,'_wps_logo_link_target', $slide_link_target_temp);
		}

		
		if( $wps_logo_fetch_url != $slide_logo_fetch_temp) {
			update_post_meta($post_id,'_wps_logo_img_url', $slide_logo_fetch_temp);
		}

		
		if( $wps_logo_alt != $wps_alt_text_temp) {
			$wps_alt_text_temp = sanitize_text_field( $wps_alt_text_temp );
			update_post_meta($post_id,'_wps_logo_alt_text', $wps_alt_text_temp);
		}

	}

	
	function wps_logos_cpt_meta_box() {
		
		if(current_user_can('edit_posts')) {
			add_meta_box( 'wps_cpt_meta_box', 'Slide Attributes','wps_cpt_meta_box_view','wps_logos','advanced');
		}

	}

	
	function wps_cpt_meta_box_view() {
		global $post;
		$wps_pid = $post->ID;
		$wps_logo_slide_link = get_post_meta($wps_pid, '_wps_logo_slide_link',true);
		$wps_logo_link_target = get_post_meta($wps_pid, '_wps_logo_link_target' ,true);
		$wps_logo_img_url = get_post_meta($wps_pid, '_wps_logo_img_url' ,true);
		$wps_logo_alt_text = get_post_meta($wps_pid, '_wps_logo_alt_text',true);
		?>
	<div style="padding: 15px;" >
		<label style="padding:0px 94px 0px 25px;font-weight: bold;" for="wps_logo_slide_link"><?php  _e('Slide Link URL','wps-awesome-logos'); ?></label>
		<input type="text" size="35" name="wps_logo_slide_link" value="<?php  echo $wps_logo_slide_link; ?>" id="" placeholder="e.g. http://www.example.com" />
	</div>
	<hr>	
	<div style="padding: 15px;">
		<label style="padding: 0px 78px 0px 25px; font-weight: bold;"><?php  _e('Slide Link Target','smooth-slider'); ?></label>
		<select name="wps_logo_link_target" >	
			<option value="0" <?php  if($wps_logo_link_target=='0') echo 'selected'; ?> ><?php  _e('Open in same window','wps-awesome-logos'); ?></option>
			<option value="1" <?php  if($wps_logo_link_target=='1') echo 'selected'; ?> > <?php  _e('Open in new window','wps-awesome-logos'); ?></option>
		</select>
    </div>
    <hr>
	<div style="padding: 15px;">
		<label style="padding: 0px 115px 0px 25px; font-weight: bold;" for="wps_logo_img_url"><?php  _e('Image URL','wps-awesome-logos'); ?></label>
		<input size="35" name="wps_logo_img_url" type="text" value="<?php  echo $wps_logo_img_url; ?>" placeholder="This will dominate over featured image" />
	</div>
	<hr>
	<div style="padding: 15px;">
	    <label style="padding: 0px 92px 0px 25px; font-weight: bold;" for="wps_logo_alt_text"><?php  _e('Alternate Text','wps-awesome-logos'); ?></label>
	    <input size="35" name="wps_logo_alt_text" type="text" value="<?php  echo $wps_logo_alt_text; ?>" placeholder="Alt for url and image" />
	</div>
	<hr>
<?php
	}

	#custom columns in all slides
	function wpscpt_edit_columns($columns){
		$columns = array(    "cb" =>"<input type='checkbox' />",    "title" => "Slide Title",    "cat" => "Categories",        "logo" => "Slide Image",    "author"=> "Author");
		return $columns;
	}

	
	function wpscpt_custom_columns($column){
		global $post;
		switch ($column) {
			case "logo";
			the_post_thumbnail(array( 80, 80));
			break;
		case "cat":
			echo get_the_term_list($post->ID, 'awesome-logos-cat', '', ', ','');
			break;
	}

}

#settings option array
function wps_default_awesome_logos_settings() {
	$default_wps_awesome_settings = array(   'autoplay'=>'1',#auto play
	'autoplaysteps'=>'1', #auto play steps
	'autoplaytime'=>'0', #auto play interval
	'pause_hover'=>'1',   #pause on hover
	'arrow_keynav'=>'1',#keyboard key arrows navigation
	'arrow_navigation'=>'0',  #arrow navigation
	'arrow_hover'=>'0',#on hover visible arrow
	'bullet_navigation'=>'0',#bullet enable navigation
	'bullet_hover'=>'0',#on hover visible bullet
	'bullet_centr'=>'0',#show in centre
	'bullet_spacing'=>'0',#spacings among bullet dots
	'bullet_verti'=>'0',#bullet vertical allign
	'bullet_acthover'=>'0',#activate bullet on hover
	'bulletsteps'=>'1',#bullet navigation steps
	'logo_fancybox'=>'0', #fancybox lightbox effect 
	'show_title'=>'1',#show title overlay
	'title_tooltip'=>'0',#show title tooltip 
	'easing_effects'=>'EaseLinear',  #slider easing effects
	'duration'=>'1500',   #scroll duration
	'slide_width'=>'200', #slides width
	'slide_height'=>'120',  #slides height
	'spacing'=>'0',  #slides spacing 
	'display_pieces'=>'7', #display slide pieces 
	'play_orient'=>'1', #play orientation - selectbox or picture
	'enable_drag'=>'1',  #dragging
	'pick_arrow'=>'3',#select arrow
	'pick_bullet'=>'1',#select bullet
	'cont_width'=>'980',#slider container width
	'cont_height'=>'120',#slider container height
	'hover_scale'=>'0',#scale effect
	'hover_inshadow'=>'0',#inside box shadow
	'hover_outshadow'=>'0',#outside box shadow
	'grayscale_effect'=>'0',#grayscale filter color
	'grid_column'=>'3',#grid number of coloumns
	'border_thickness'=>'0',#slide border thickness
	'border_color'=>'#222222',#slide border color
	'background_color'=>'transparent', #background color
	'title_color'=>'#ffffff',#title color
	'title_font'=>'Georgia',   'title_size'=>'18',   'title_style'=>'normal',  );
	return $default_wps_awesome_settings;
}

#template tags
function get_awesome_logos( $uid ) {
	$html_data = get_wps_awesome_logos( $uid );
	echo $html_data;
}

#fetch all logos name
function wps_get_logos_info(){
	global $wpdb,$table_prefix;
	$slider_meta = $table_prefix.WPS_AWESOME_LOGOS_META;
	$sql = "SELECT * FROM $slider_meta ORDER BY slider_id DESC";
	$logos = $wpdb->get_results($sql);
	return $logos;
}
?>