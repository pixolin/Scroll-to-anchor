<?php
/*
 * Plugin Settings
 * Edit settings in menu Settings > Reading in your WordPress Back End.
 *
 * Contents:
 * 1. Option Defaults
 * 2. Settings Section and Fields in Settings > Reading
 * 3. Form Fields
 * 4. Validation
 * 5. Show link to settings
 */

/* ------------------------------------------------------------------------- *
 * 1. Settings Section and Fields in Settings > Reading
 * ------------------------------------------------------------------------- */

add_action( 'admin_init', 'sta_settings_api_init' );

if ( ! function_exists( 'sta_settings_api_init' ) ) {
	function sta_settings_api_init() {
		//New settings section in page "reading"
		add_settings_section(
			$id = 'sta_section',
			$title = '',
			$callback = 'sta_settings_section',
			$page = 'reading'
		);

		//Settings Field Distance
		add_settings_field(
			$id = 'sta_distance',
			$title = __( 'Offset', 'scroll-to-anchor' ),
			$callback = 'sta_settings_distance_function',
			$page = 'reading',
			$section = 'sta_section',
			$args = array()
		);

		//Settings Field Scroll Speed
		add_settings_field(
			$id = 'sta_speed',
			$title = __( 'Animation-Speed', 'scroll-to-anchor' ),
			$callback = 'sta_settings_speed_function',
			$page = 'reading',
			$section = 'sta_section',
			$args = array()
		);

		//Settings Label
		add_settings_field(
			$id = 'sta_label',
			$title = __( 'Label of the anchor', 'scroll-to-anchor' ),
			$callback = 'sta_settings_label_function',
			$page = 'reading',
			$section = 'sta_section',
			$args = array()
		);

		//Settings Show Anchor
		add_settings_field(
			$id = 'sta_show',
			$title = __( 'Show anchor(s) in front end', 'scroll-to-anchor' ),
			$callback = 'sta_settings_showanchor_function',
			$page = 'reading',
			$section = 'sta_section',
			$args = array()
		);

		register_setting( 'reading', 'scroll_to_anchor', 'sta_sanitize' );
	}
}

/* ------------------------------------------------------------------------- *
 * 2. Form Fields
 * ------------------------------------------------------------------------- */

//Form Field DISTANCE
if ( ! function_exists( 'sta_settings_distance_function' ) ) {
	function sta_settings_distance_function() {
		$current = (array) get_option( 'scroll_to_anchor' );

		$html = __( 'Show anchor with an offset of…', 'scroll-to-anchor' ).'<br />';
		$html .= '<input name="scroll_to_anchor[distance]" id="sta-distance" type="text" value="'.esc_attr( $current['distance'] ).'" size="5"/> ';
		$html .= '<label for="sta-distance">'.__( 'Pixel', 'scroll-to-anchor' ).' (0&ndash;600 Pixel)</label>';

		echo  $html;
	}
}

//Form Field SPEED
if ( ! function_exists( 'sta_settings_speed_function' ) ) {
	function sta_settings_speed_function() {
		$current = (array) get_option( 'scroll_to_anchor' );

		$options = array(
			5000 => __( 'slow', 'scroll-to-anchor' ),
			1000 => __( 'medium', 'scroll-to-anchor' ),
			500  => __( 'fast', 'scroll-to-anchor' ),
			0    => __( 'disabled', 'scroll-to-anchor' ),
		);

		$html = __( 'Animation speed when scrolling to anchors', 'scroll-to-anchor' ).'<br />';
		$html .= '<select id="speed" name="scroll_to_anchor[speed]}">';

		foreach ( $options as $value => $text ) {
			$html .= '<option value="'.$value.'"';
			$html .= selected( esc_attr( $current['speed'] ), $value, false ).'>'.$text.'</option>';
		}

		$html .= '</select>';

		echo  $html;
	}
}

//form field for anchor label
if ( ! function_exists( 'sta_settings_label_function' ) ) {
	function sta_settings_label_function() {
		$current = (array) get_option( 'scroll_to_anchor' );

		$html = __( 'By default anchors are labeled as <em>Anchor: foo</em>, <br />
    but you can rename the label here.', 'scroll-to-anchor' ).'<br />';
		$html .= '<label for="sta-label">'.__( 'Name:', 'scroll-to-anchor' ).'</label> ';
		$html .= '<input name="scroll_to_anchor[label]" id="sta-label" type="text" value="'.esc_attr( $current['label'] ).'" /> ';

		echo  $html;
	}
}

//checkbox show anchor
if ( ! function_exists( 'sta_settings_showanchor_function' ) ) {
	function sta_settings_showanchor_function() {
		$current = (array) get_option( 'scroll_to_anchor' );

		$html = '<input name="scroll_to_anchor[show]" id="sta-show" type="checkbox" value="1" '.checked( isset( $current['show'] ), 1, false ).'/>';
		$html .= '<label for="sta-show">'.__( 'display anchor inline as <em>Anchor: foo</em>', 'scroll-to-anchor' ).'</label>';

		echo  $html;
	}
}

/* ------------------------------------------------------------------------- *
 * 4. Validation and Sanitization
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'sta_sanitize' ) ) {
	function sta_sanitize( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		// Loop through the input and sanitize each of the values
		foreach ( $input as $key => $val ) {
			if ( 'label' === $key ) {
				$new_input[ $key ] = sanitize_text_field( $val );
			} else {
				$new_input[ $key ] = absint( $val );

				if ( ( 'distance' === $key ) && ( $val > 600 ) ) {
					$new_input[ $key ] = 0;
					add_settings_error(
						'sta_setting_distance',
						'invalid-value',
						'Invalid value anchor offset'
					);
				}
			}
		}

		return $new_input;
	}
}

/* ------------------------------------------------------------------------- *
 * 5. Link to settings
 * ------------------------------------------------------------------------- */

add_filter( 'plugin_action_links', 'sta_plugin_action_links' );
function sta_plugin_action_links( $links ) {
	$links[] = '<a href="options-reading.php#sta_section">'. __( 'Settings', 'scroll-to-anchor' ) . '</a>';
	return $links;
}

// Settings Section Callback to add an anchor for link from the plugins menu
function sta_settings_section( $arg ) {
	echo '<h2 id="' . $arg['id'] . '">' . __( 'Scroll to Anchor Settings', 'scroll-to-anchor' ) . '</h2>';
}
