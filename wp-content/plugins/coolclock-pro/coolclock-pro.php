<?php
/*
Plugin Name: CoolClock - Advanced extension
Plugin URI: http://status301.net/wordpress-plugins/coolclock-pro/
Description: Extra settings for the CoolClock widget.
Text Domain: coolclock
Domain Path: ../coolclock/languages
Version: 6.0
Author: RavanH
Author URI: http://status301.net/
*/

class CoolClockAdvanced {

	static $plugin_version = '6.0';
	
	static $compat = array (
				'min' => '2.9.7',
				'max' => '3.0'
			);

	private static $showdigital_options = array (
				'digital24' => 'showDigital24',
				'date' => 'showDate'
			);

    private static $advanced_defaults = array (
				'background_image' => '',			// Custom background image url
				'background_stretch' => 'contain',	// empty equals no stretching
				'background_position' => '',	// empty equals "left top" or "0% 0%"
				'background_repeat' => false,	
				'background_height' => '',			// Set height for wrapping div that carries the background image
				'background_width' => '',			// Set width for wrapping div that carries the background image
				'background_color' => '',			// Set background color
				'background_border_radius' => '',	// Set background border radius
				'custom_skin' => '',				// Allow custom skin parameters
				'vertical_position_dist' => 0,		// Clock position relative to background: distance from top
				'horizontal_position_dist' => 0,	// Clock position relative to background: distance from left
			);

	private static $advanced_skins = array (
	    		'minimal'
	    	);
    	
	private static $advanced_skins_config = array (
	    		'minimal' => 'hourHand:{lineWidth:5,startAt:-15,endAt:50,color:"black",alpha:1},minuteHand:{lineWidth:3,startAt:-15,endAt:65,color:"black",alpha:1},secondHand:{lineWidth:1,startAt:-20,endAt:75,color:"red",alpha:1},secondDecoration:{lineWidth:1,startAt:0,radius:4,fillColor:"red",color:"red",alpha:1}'
	    	);
	
	/** 
	 * SHORTCODE 
	 */
	
	static function shortcode_filter( $val, $atts, $content = null )
	{
		extract( $atts );
		
		$clock_height = ( $subtext ) ? 'auto' : 2 * $radius . 'px'; 

		$replace = ( $background_height ) ? 'height:' . $background_height . 'px': 'height:' . $clock_height;
		$replace .= ( $background_width ) ? ';width:' . $background_width . 'px': '';
		$replace .= ( $background_color ) ? ';background-color:' . $background_color : '';
		$replace .= ( $background_image ) ? ';background-image:url(' . $background_image . ')' : '';
		$replace .= ( $background_position ) ? ';background-position:' . $background_position : ';background-position:center';
		$replace .= ( $background_stretch ) ? ';background-size:' . $background_stretch : '';
		$replace .= ( $background_repeat ) ? ';background-repeat:' . $background_repeat : ';background-repeat:no-repeat';
		$replace .= ( $background_border_radius ) ? ';border-radius:' . $background_border_radius . 'px': '';
		$replace .= '">';
		
		// add custom skin parameters to the plugin skins array
		if ( !empty( $skin ) && !in_array( $skin, array_merge( CoolClock::$default_skins, CoolClock::$more_skins, self::$advanced_skins ) ) && !empty( $content ) )
			CoolClock::$advanced_skins_config[$skin] = wp_strip_all_tags( $content, true );

		return str_replace( 'height:auto">', $replace, $val );
	}

	/** 
	 * WIDGET 
	 */
	
	static function widget_filter( $val, $args, $instance )
	{
		$clock_width = 2 * $instance['radius'] . 'px'; 

		$background_height = ( isset( $instance['background_height'] ) && $instance['background_height'] != '' ) 
			? $instance['background_height'].'px' : 'auto';
		$background_width = ( isset( $instance['background_width'] ) && $instance['background_width'] != '' ) 
			? $instance['background_width'].'px' : 'auto';
		$background_color = ( isset( $instance['background_color'] ) ) 
			? $instance['background_color'] : 'transparent';
		$background_border_radius = ( isset( $instance['background_border_radius'] ) )
			? $instance['background_border_radius'] : '';

		$wrapper_style = ' style="height:' . $background_height . ';width:' . $background_width . ';max-width:100%;position:relative;';
		// TODO allow fixed position !!
		if ( !empty($instance['background_image']) ) {
			$wrapper_style .= 'background-image:url(\''.$instance['background_image'].'\');';
			$wrapper_style .= ( !empty( $instance['background_position'] ) ) 
				? 'background-position:'.$instance['background_position'].';' : '';
			$wrapper_style .= ( !empty( $instance['background_stretch'] ) ) 
				? 'background-size:'.$instance['background_stretch'].';' : '';
			$wrapper_style .= ( isset ( $instance['background_repeat'] ) && $instance['background_repeat'] == false ) 
				? 'background-repeat:no-repeat;' : '';
		}
		$wrapper_style .= ( !empty( $instance['background_color'] ) ) 
			? 'background-color:'.$instance['background_color'].';' : '';
		$wrapper_style .= ( !empty( $instance['background_border_radius'] ) ) 
			? 'border-radius:'.$instance['background_border_radius'].'px;' : '';
		$wrapper_style .= '"';

		$container_style = ' style="position:relative;';
		$container_style .= ( !empty( $instance['vertical_position_dist'] ) ) 
			? 'top:' . $instance['vertical_position_dist'].'px;' : '';
		$container_style .= ( !empty( $instance['horizontal_position_dist'] ) ) 
			? 'left:' . $instance['horizontal_position_dist'].'px;' : '';
		$container_style .= 'height:auto;width:' . $clock_width . ';max-width:100%';
		$container_style .= '"';
		
		// align is set: give wrapper the align class
		$class = !empty($instance['align']) ? ' class="align' . $instance['align'] . '"' : '';

		// TODO remove the canvas div class from $val ?

		return '<div' . $class . $wrapper_style . '><div' . $class . $container_style . '>' . $val . '</div></div>';
	}

	static function update( $instance, $new_instance )
	{
		$instance['background_image'] = strip_tags( $new_instance['background_image'] ); // TODO some URL/path validation ?
		$instance['background_stretch'] = strip_tags( $new_instance['background_stretch'] );
		$instance['background_position'] = strip_tags( $new_instance['background_position'] );
		$instance['background_repeat'] = (bool) $new_instance['background_repeat'];
		$instance['background_width'] = ( !$new_instance['background_width'] || (int) $new_instance['background_width'] < 1 ) 
			? '' : (int) $new_instance['background_width'];
		$instance['background_height'] = ( !$new_instance['background_height'] || (int) $new_instance['background_height'] < 1 ) 
			? '' : (int) $new_instance['background_height'];
		$instance['background_color'] = strip_tags( $new_instance['background_color'] ); // TODO callback for hex value ?
		$instance['background_border_radius'] = ( !$new_instance['background_border_radius'] || (int) $new_instance['background_border_radius'] < 1 ) 
			? '' : (int) $new_instance['background_border_radius'];
		$instance['vertical_position_dist'] = (int) $new_instance['vertical_position_dist'];
		$instance['horizontal_position_dist'] = (int) $new_instance['horizontal_position_dist'];
    
        return $instance;
	}
        
	static function form( $obj, $instance, $defaults, $echo = true )
	{
		$background_image = esc_attr( $instance['background_image'] );
		$background_color = esc_attr( $instance['background_color'] );
		$output = '';
	
		// relative position fields
		$output .= '<p>' . __('Clock position relative to background:', 'coolclock') . '</p>';

		$output .= '<p><label for="' . $obj->get_field_id('horizontal_position_dist') . '"> ' .  __('Left', 'coolclock') . '</label> ';
		$output .= '<input class="small-text" id="' . $obj->get_field_id('horizontal_position_dist') . '" name="' . $obj->get_field_name('horizontal_position_dist') . '" type="number" value="' . $instance['horizontal_position_dist'] . '" /> ';
		$output .= '&nbsp; <label for="' . $obj->get_field_id('vertical_position_dist') . '"> ' . __('Top', 'coolclock') . '</label> ';
		$output .= '<input class="small-text" id="' . $obj->get_field_id('vertical_position_dist') . '" name="' . $obj->get_field_name('vertical_position_dist') . '" type="number" value="' . $instance['vertical_position_dist'] . '" /></p>';

		$output .= '<p><strong>' . translate('Background') . '</strong></p>';

		// wrapper size fields
		$output .= '<p><label for="' . $obj->get_field_id('background_width') . '">' . translate('Width') . '</label> ';
		$output .= '<input class="small-text" id="' . $obj->get_field_id('background_width') . '" name="' . $obj->get_field_name('background_width') . '" type="number" value="' . $instance['background_width'] . '" /> ';
		$output .= '&nbsp; <label for="' . $obj->get_field_id('background_height') . '">' . translate('Height') . '</label> ';
		$output .= '<input class="small-text" id="' . $obj->get_field_id('background_height') . '" name="' . $obj->get_field_name('background_height') . '" type="number" value="' . $instance['background_height'] . '" /></p>';

		// background color text field
		$output .= '<p><label for="' . $obj->get_field_id('background_color') . '">' . __('Color:', 'coolclock') . '</label> ';
		$output .= '<input class="" id="' . $obj->get_field_id('background_color') . '" name="' . $obj->get_field_name('background_color') . '" type="text" value="' . $background_color . '" /></p>';

		// border radius field
		$output .= '<p><label for="' . $obj->get_field_id('background_border_radius') . '">' . __('Border radius:', 'coolclock') . '</label> ';
		$output .= '<input class="small-text" id="' . $obj->get_field_id('background_border_radius') . '" name="' . $obj->get_field_name('background_border_radius') . '" type="number" value="' . $instance['background_border_radius'] . '" /></p>';

		$output .= '<p><strong>' . translate('Background Image') . '</strong></p>';

		// image url text field
		$output .= '<p><label for="' . $obj->get_field_id('background_image') . '">' . __('Image URL:', 'coolclock') . '</label> ';
		$output .= '<input class="widefat" id="' . $obj->get_field_id('background_image') . '" name="' . $obj->get_field_name('background_image') . '" type="text" value="' . $background_image . '" /></p>';

		// image stretching select
		$output .= '<p><label for="' . $obj->get_field_id('background_stretch') . '">' . __('Stretching:', 'coolclock') . '</label> ';
		$output .= '<select class="select" id="' . $obj->get_field_id('background_stretch') . '" name="' . $obj->get_field_name('background_stretch') . '">';

		$output .= '<option value=""';
		$output .= ($instance['background_stretch'] == '')
			? ' selected="selected"' : '';
		$output .= '>' . translate('none') . '</option>';
		$output .= '<option value="cover"';
		$output .= ($instance['background_stretch'] == 'cover')
			? ' selected="selected"' : '';
		$output .= '>' . __('cover', 'coolclock') . '</option>';
		$output .= '<option value="contain"';
		$output .= ($instance['background_stretch'] == 'contain')
			? ' selected="selected"' : '';
		$output .= '>' . __('contain', 'coolclock') . '</option>';

		$output .= '</select></p>';		
				
		// image position select
		$output .= '<p><label for="' . $obj->get_field_id('background_position') . '">' . __('Position:', 'coolclock') . '</label> ';
		$output .= '<select class="select" id="' . $obj->get_field_id('background_position') . '" name="' . $obj->get_field_name('background_position') . '">';

		$output .= '<option value=""';
		$output .= ($instance['background_position'] == '')
			? ' selected="selected"' : '';
		$output .= '>' . __('top left', 'coolclock') . '</option>';
		$output .= '<option value="left"';
		$output .= ($instance['background_position'] == 'left')
			? ' selected="selected"' : '';
		$output .= '>' . __('left', 'coolclock') . '</option>';
		$output .= '<option value="left bottom"';
		$output .= ($instance['background_position'] == 'left bottom')
			? ' selected="selected"' : '';
		$output .= '>' . __('bottom left', 'coolclock') . '</option>';

		$output .= '<option value="top"';
		$output .= ($instance['background_position'] == 'top')
			? ' selected="selected"' : '';
		$output .= '>' . __('top', 'coolclock') . '</option>';
		$output .= '<option value="center"';
		$output .= ($instance['background_position'] == 'center')
			? ' selected="selected"' : '';
		$output .= '>' . __('center', 'coolclock') . '</option>';
		$output .= '<option value="bottom"';
		$output .= ($instance['background_position'] == 'bottom')
			? ' selected="selected"' : '';
		$output .= '>' . __('bottom', 'coolclock') . '</option>';

		$output .= '<option value="right top"';
		$output .= ($instance['background_position'] == 'right top')
			? ' selected="selected"' : '';
		$output .= '>' . __('top right', 'coolclock') . '</option>';
		$output .= '<option value="right"';
		$output .= ($instance['background_position'] == 'right')
			? ' selected="selected"' : '';
		$output .= '>' . __('right', 'coolclock') . '</option>';
		$output .= '<option value="right bottom"';
		$output .= ($instance['background_position'] == 'right bottom')
			? ' selected="selected"' : '';
		$output .= '>' . __('bottom right', 'coolclock') . '</option>';

		$output .= '</select></p>';		
				
		// image repeat checkbox
		$output .= '<p><input id="' . $obj->get_field_id('background_repeat') . '" name="' . $obj->get_field_name('background_repeat') . '" type="checkbox" value=';
		$output .= ( $instance['background_repeat'] ) ? '"true"  checked="checked"' : '"false"';
		$output .= ' /> ';
		$output .= '<label for="' . $obj->get_field_id('background_repeat') . '">' . __('Repeat image', 'coolclock') . '</label></p>';

		if ($echo) // echo by default for backward compat
			echo $output;
		else 
			return $output;
	}
	
	static function form_filter( $val, $obj, $instance, $defaults )
	{
		// do not filter, just replace...
		$val = self::form($obj, $instance, $defaults, false);
		return $val;
	}	

	/**
	 * INIT
	 */

	static function go()
	{
		add_action( 'init', array( __CLASS__, 'init' ), 11 );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ), 9 );
	}

	static function init()
	{        
		if ( !class_exists('CoolClock') )
			return;
		// TODO check version compatibility
		// TODO set admin notice when failing

		// change class parameters
		CoolClock::$advanced = true;
		CoolClock::$showdigital_options = array_merge( CoolClock::$showdigital_options, self::$showdigital_options );
		CoolClock::$advanced_defaults = array_merge( CoolClock::$advanced_defaults, self::$advanced_defaults );
		CoolClock::$advanced_skins = array_merge( CoolClock::$advanced_skins, self::$advanced_skins );
		CoolClock::$advanced_skins_config = array_merge( CoolClock::$advanced_skins_config, self::$advanced_skins_config );
	}

	static function widgets_init()
	{
		if ( !class_exists('CoolClock') )
			return;

		// add widget filters
		add_filter( 'coolclock_widget_advanced', array( __CLASS__, 'widget_filter' ), 10, 3 );
		add_filter( 'coolclock_shortcode_advanced', array( __CLASS__, 'shortcode_filter' ), 10, 3 );
		add_filter( 'coolclock_widget_form_advanced', array( __CLASS__, 'form_filter' ), 1, 4 );
		add_filter( 'coolclock_widget_update_advanced', array( __CLASS__, 'update' ), 10, 2 );
	}

}
 
CoolClockAdvanced::go();
