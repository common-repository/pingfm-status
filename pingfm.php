<?php
/*
Plugin Name: Ping.fm status
Plugin URI: http://www.samlesher.com/code/wordpress-plugin-to-display-twitter-status-via-pingfm/
Description: Adds a sidebar widget to display current Ping.fm status
Author: Sam Lesher
Version: 1.0
Author URI: http://www.samlesher.com
*/

// This gets called at the plugins_loaded action
function widget_pingfm_init() {
    // Check for the required API functions
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) {
		return;
	}

    // This saves options and prints the widget's config form.
    function widget_pingfm_control() {
        $options = get_option('widget_pingfm');
        
        if (!is_array( $options )) {
            $options = array('title' => 'Widget Title');
        }      

        if ($_POST['pingfm-submit']) {
            $options['title'] = htmlspecialchars($_POST['pingfm-title']);
            update_option('widget_pingfm', $options);
        }
        ?>
        <p>
            <label for="pingfm-title">Title: </label>
            <input type="text" id="pingfm-title" name="pingfm-title" value="<?php echo $options['title'];?>" />
            <input type="hidden" id="pingfm-submit" name="pingfm-submit" value="1" />
        </p>
    <?php
    }

	// This prints the widget
	function widget_pingfm($args) {
	    extract($args);
		$options = get_option('widget_pingfm');
		
		if (!is_array( $options )) {
		    $options = array('title' => 'Widget Title');
		}      
		
		$title = $options['title'];
        $status_file = WP_PLUGIN_DIR.'/pingfm-status/pingfm-post.txt';
        
        $status_file_size=filesize($status_file);
        
        if ($status_file_size==0) {
            $status_data = 'Status currently unavailable.';
        } else {
            //Convert contents of the text file that Ping.fm writes to into a variable.
            $fh = fopen($status_file, 'r');
            $status_data = fread($fh, filesize($status_file));
            fclose($fh);
        
            //Find hyperlinks in Ping.fm status data and make them clickable.
            // match protocol://address/path/
            $status_data = ereg_replace("[a-zA-Z]+://([-]*[.]?[a-zA-Z0-9_/-?&%])*", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $status_data);
            // match www.something
            $status_data = ereg_replace("(^| )(www([-]*[.]?[a-zA-Z0-9_/-?&%])*)", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $status_data);
            //matches email
            $status_data = ereg_replace('[-a-z0-9!#$%&\'*+/=?^_`{|}~]+@([.]?[a-zA-Z0-9_/-])*',"<a href=\"mailto:\\0\">\\0</a>" ,$status_data);
        }
        
        //Display the Widget
    	echo $before_widget;
            echo $before_title . $title . $after_title;
            echo '<ul><li><span class="statustext">' . $status_data . '</span></li></ul>';
        echo $after_widget;
    }

	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget(array('pingfm', 'widgets'), 'widget_pingfm');
	register_widget_control(array('pingfm', 'widgets'), 'widget_pingfm_control');
	
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('widgets_init', 'widget_pingfm_init');
?>
