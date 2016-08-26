<?php
add_shortcode('awesomelogos', 'wps_awesome_logos_shortcode');
function wps_awesome_logos_shortcode($atts) {
	if( !isset($atts['0']) || empty($atts['0'] )) {
		return '[awesomelogos] Invalid shortcode id required!';
	}
	$id = intVal($atts['0']);
	$slider_html = get_wps_awesome_logos($id);
	return $slider_html;
}
add_action( 'widgets_init', create_function('', 'return register_widget("WPS_Awesome_Logos_Widget");') );
class WPS_Awesome_Logos_Widget extends WP_Widget {
	function WPS_Awesome_Logos_Widget() {
		$widget_options = array('classname' => 'wps_awesome_widget_class', 'description' => 'Add Awesome Logos' );
		parent::__construct('wps_awesome_logos_widget', 'Awesome Logos', $widget_options);
	}
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$title = apply_filters( 'widget_title', $instance['title'] );
		$title = !empty($title) ? $before_title . $title . $after_title : $title;
		#get logos 
		$html = get_wps_awesome_logos( $instance['wps_logos_id'] );
		echo $before_widget, $title, $html, $after_widget;
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['wps_logos_id'] = strip_tags($new_instance['wps_logos_id']);
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
	function form($instance) {	
		$instance = wp_parse_args( (array) $instance, array( 'title'=>'AwesomeLogos', 'wps_logos_id' => '' ) );
		if( isset( $instance[ 'title' ] ) ) $title = $instance[ 'title' ];
			$title = 'AwesomeLogos';		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'AwesomeLogos'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>">
		</p>
		<?php 		
		$wps_logos_id = strip_tags($instance['wps_logos_id']);
		#get list of all names
		$logos = wps_get_logos_info();
		$sname_html ="";
		foreach ($logos as $slider) { 
			if($slider->slider_id ==$wps_logos_id){ $selected = 'selected'; } else{ $selected=''; }
			$sname_html = $sname_html.'<option value="'.$slider->slider_id.'" '.$selected.'> '.$slider->slider_name.' | '.$slider->slider_type.' | #'.$slider->slider_id.'</option>';
		} ?>
		<p><label for="<?php echo $this->get_field_id('wps_logos_id'); ?>">Select Logos: <select class="widefat" id="<?php echo $this->get_field_id('wps_logos_id'); ?>" name="<?php echo $this->get_field_name('wps_logos_id'); ?>"><?php echo $sname_html;?></select></label></p>
<?php }
}
?>