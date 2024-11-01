<?php
/*
Plugin Name:  Wit-Buzz, Your Website Buzz Generator!
Plugin URI:   http://www.wit-buzz.com/plugin/wordpress
Description:  Let the users spread the good word. Collect user Opinion, generate the Buzz and grow your Traffic.
Version:      1.0d
Author:       Wit-Me
Author URI:   http://www.wit-me.com/
*/


	function witbuzz_simple_widget($args, $widget_args = 1) {
		
		extract( $args, EXTR_SKIP );
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
	
		$options = get_option('witbuzz_widget');
		if ( !isset($options[$number]) ) 
		return;

		$title = $options[$number]['title']; 		// single value
		$text = $options[$number]['text']; 		// single value
		$radio = $options[$number]['radio']; 		// single value
		$sizepx = $options[$number]['sizepx']; 		// single value
		$image = $options[$number]['image']; 		// single value
		if ($sizepx == '') $sizepx='30';	// set default size if not given to 30 pixels
			
		echo $before_widget; // start widget display code 
                		// echo  plugins_url( 'witmebutton.png' , __FILE__ );
                // 1.0.c allow user to select the picture to display, use our only by default
                if ($image == '') $image = plugins_url( 'witmebutton.png' , __FILE__ );
                ?>
		<a id="witbuzz-call" href="javascript:window.open('https://api.uwit.me/wit-buzz/web?cmd=display&web=<?php echo $title; ?>&lg=<?php echo ($radio=='0' ? 'DT' : ($radio == '1' ? 'EN' : 'FR' ) ); ?>','Wit-Buzz','toolbar=no, titlebar=no, location=no, menubar=no, status=no, scrollbars=no, menubar=no, width=610, height=515, top='+(screen.height - 500) / 2+', left='+(screen.width - 700) / 2);" title='<?=$text?>'><img src='<?=$image?>' alt='<?=$title?>' style='width:<?=$sizepx?>px;height:<?=$sizepx?>px'/></a>
			
	<?php echo $after_widget; // end widget display code
	
	}
	
	
	function witbuzz_simple_widget_control($widget_args) {
	
		global $wp_registered_widgets;
		static $updated = false;
	
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );			
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
	
		$options = get_option('witbuzz_widget');
		
		if ( !is_array($options) )	
			$options = array();
	
		if ( !$updated && !empty($_POST['sidebar']) ) {
		
			$sidebar = (string) $_POST['sidebar'];	
			$sidebars_widgets = wp_get_sidebars_widgets();
			
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar =& $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();
	
			foreach ( (array) $this_sidebar as $_widget_id ) {
				if ( 'witbuzz_simple_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
					if ( !in_array( "witbuzz-widget-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
						unset($options[$widget_number]);
				}
			}
	
			foreach ( (array) $_POST['witbuzz-widget'] as $widget_number => $simple_widget ) {
				if ( !isset($simple_widget['title']) && isset($options[$widget_number]) ) // user clicked cancel
					continue;
				
				$title = strip_tags(stripslashes($simple_widget['title']));
				$text = strip_tags(stripslashes($simple_widget['text_value']));				
				$radio = $simple_widget['radio_value'];
				$sizepx = $simple_widget['sizepx_value'];
                                // v1.0.c add image
                                $image =  $simple_widget['image_value'];
				
				// Pact the values into an array
				$options[$widget_number] = compact( 'title', 'text', 'radio', 'sizepx', 'image' );
			}
	
			update_option('witbuzz_widget', $options);
			$updated = true;
		}
	
		if ( -1 == $number ) { // if it's the first time and there are no existing values
	
			$title = '';
			$text = 'Go ahead, share your opinion !';
			$radio = '0';
			$number = '%i%';
			$sizepx = '30';
                        // v 1.0.c set image to nothing by default, if nothing the default app logo will be used
                        $image='';
			
		} else { // otherwise get the existing values
		
			$title = attribute_escape($options[$number]['title']);
			$text = attribute_escape($options[$number]['text']); // attribute_escape used for security
			$radio = $options[$number]['radio'];
			$sizepx = $options[$number]['sizepx'];
                        // v1.0.c use image stored in settings
                        $image= $options[$number]['image'];
		}
		
		//print_r($options[$number]);
	?>
    <p><?php _e( 'Click', 'witbuzz' ); ?> <a href='www.wit-buzz.com/register.php' title='register with wit-buzz' target='_blank'><?php _e( 'here', 'witbuzz' ); ?></a><?php _e( ' and register in no time to Wit-Buzz. Then, you will be able to start using it !', 'witbuzz' ); ?></p>
    <p><label><?php _e( 'Enter your web site (ex: www.wit-buzz.com)', 'witbuzz' ); ?><br /><input id="title_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][title]" type="text" value="<?=$title?>" size="50"/></p>
    <p><label><?php _e( 'Text to display for your users', 'witbuzz' ); ?></label><br /><input id="text_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][text_value]" type="text" value="<?=$text?>" size="50"/></p>
    <p>
        <label><?php _e( 'Which language the Wit-Buzz widget will display on users?', 'witbuzz' ); ?></label><br />
        <input id="radio_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][radio_value]" type="radio" <?php if($radio == '0') echo 'checked="checked"'; ?> value="0" /> <?php _e( 'Automatic', 'witbuzz' ); ?>&nbsp;&nbsp;
        <input id="radio_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][radio_value]" type="radio" <?php if($radio == '1') echo 'checked="checked"'; ?> value="1" /> English&nbsp;&nbsp;
        <input id="radio_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][radio_value]" type="radio" <?php if($radio == '1') echo 'checked="checked"'; ?> value="2" />French
    </p>
	<p><label><?php _e( 'Enter the size for the Wit-Buzz icon', 'witbuzz' ); ?> <img src='<?php echo  plugins_url( 'witmebutton.png' , __FILE__ ); ?>' alt='<?=$title?>' style='width:16px;padding-top:5px'/> (default=30px)<br /><input id="sizepx_value_<?php echo $number; ?>" name="witbuzz-widget[<?php echo $number; ?>][sizepx_value]" type="text" size="20" value="<?=$sizepx?>" /></p>
	<p><label><?php _e( 'Enter the URL of the image to use (if nothing the defaul pic plugin is used)', 'witbuzz' ); ?></label><br/><input id="image_value_<?php echo $image; ?>" name="witbuzz-widget[<?php echo $number; ?>][image_value]" type="text" size="70" value="<?=$image?>" /></p>
    <input type="hidden" name="simple-widget[<?php echo $number; ?>][submit]" value="1" />
    
	<?php
	}
	
	
	function witbuzz_simple_widget_register() {
  		if ( !$options = get_option('witbuzz_widget') )
			$options = array();
		$widget_ops = array('classname' => 'witbuzz_widget', 'description' => __('Wit-Buzz widget'));
		$control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'witbuzz-widget');
		$name = __('Wit-Buzz, Your Website Buzz Generator!','witbuzz');
	
		$id = false;
		
		foreach ( (array) array_keys($options) as $o ) {
	
			if ( !isset( $options[$o]['title'] ) )
				continue;
						
			$id = "witbuzz-widget-$o";
			wp_register_sidebar_widget($id, $name, 'witbuzz_simple_widget', $widget_ops, array( 'number' => $o ));
			wp_register_widget_control($id, $name, 'witbuzz_simple_widget_control', $control_ops, array( 'number' => $o ));
		}
		
		if ( !$id ) {
			wp_register_sidebar_widget( 'witbuzz-widget-1', $name, 'witbuzz_simple_widget', $widget_ops, array( 'number' => -1 ) );
			wp_register_widget_control( 'witbuzz-widget-1', $name, 'witbuzz_simple_widget_control', $control_ops, array( 'number' => -1 ) );
		}
	}
              // 1.0.d multilingual version
                load_plugin_textdomain('witbuzz', false, basename( dirname( __FILE__ ) ) . '/languages'); 

add_action('init', witbuzz_simple_widget_register, 1);

?>