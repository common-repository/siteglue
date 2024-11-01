<?php
/*
Plugin Name: Text Us Now Button by SiteGlue
Plugin URI: http://getsiteglue.com
Description: Adds a text us button to your mobile website
Version: 1.0
Author: SiteGlue
Author URI: http://getsiteclue.com
*/
 
/**
 * Main Class
 */
class SitGlue_Options {
  
    /*--------------------------------------------*
     * Attributes
     *--------------------------------------------*/
  
    /** Refers to a single instance of this class. */
    private static $instance = null;
     
    /* Saved options */
    public $options;
  
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
  
    /**
     * Creates or returns an instance of this class.
     *
     * @return  SitGlue_Options A single instance of this class.
     */
    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new self;
        }
  
        return self::$instance;
  
    } // end get_instance;
  
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() { 
    	// Add the page to the admin menu
    	add_action( 'admin_menu', array( &$this, 'add_page' ) );
     
    	// Register page options
    	add_action( 'admin_init', array( &$this, 'register_page_options') );

     
    	// Register javascript
    	add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ) );

        // Register script on public pages
        add_action( 'wp_enqueue_scripts',  array( $this, 'sg_enqueue_script') );
        add_filter('clean_url',array( $this, 'unclean_url'),10,3);






     
    	// Get registered option
    	$this->options = get_option( 'sg_settings_options' );


    }


    

    /**
    * Functions to embed text button script on public page
    */
    public function sg_enqueue_script() {
        wp_enqueue_script( 'text-button-script',  'http://load.lokalmotion.com/cs_widget/v2/cw_lokalmotion.js', '', true );
    }


    public function unclean_url( $good_protocol_url, $original_url, $_context){
        if (false !== strpos($original_url, 'cw_lokalmotion.js')){
            remove_filter('clean_url','unclean_url',10,3);
            $url_parts = parse_url($good_protocol_url);
            if($this->options['enabled'] == "true")
                return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' id='cs_script'  data-lokalmotion-phone='".$this->options['phone']."' data-lokalmotion-bg='".$this->options['background']."' data-lokalmotion-color='".$this->options['color']."' data-lokalmotion-text='".$this->options['text']."'";
            else 
                return $good_protocol_url;
        }
        return $good_protocol_url;
    }


  
    /*--------------------------------------------*
     * Functions
     *--------------------------------------------*/
      
    /**
     * Function that will add the options page under Setting Menu.
     */
    public function add_page() { 
    	// $page_title, $menu_title, $capability, $menu_slug, $callback_function
    	add_options_page( 'SiteGlue Settings', 'SiteGlue Settings', 'manage_options', __FILE__, array( $this, 'display_page' ) );
    }
      
    /**
 	* Function that will display the options page.
 	*/
	public function display_page() { 
    ?>
    <div class="wrap">
     
        <h2><img src="<?php echo plugins_url(); ?>/siteglue/logo.png" /></h2>
        <style>
            .siteglue-top-section{background:#fff;padding: 20px 20px 0px 20px;border: 1px solid #ddd; width: 80%; color: #666;}
            .row-glue{clear:both;}
            .top-title{font-size:20px; color:#666; width:70%; float:left;}
            .btn-get-it-now{ width:10%; float:right; }
        </style>
        <div class="siteglue-top-section">
            <div class="row-glue">
                <div class="top-title">Want to text with customers and keep your personal number private?</div>
                <div class="btn-get-it-now"> 
                    <input type="button" onclick="javascript:location.href='http://getsiteglue.com/pricing'" class="button button-primary"  value="Get it Now">
                </div>

            </div>

            <div class="row-glue" style="padding-top: 2px;">
            <p style="font-style: italic; font-size:15px;">Get a dedicated SiteGlue mobile number to text with customers and keep your personal cell number private.</p>
        </div>
    </div>
        <form method="post" action="options.php">     
        <?php 
            settings_fields(__FILE__);      
            do_settings_sections(__FILE__);
            submit_button();
        ?>
        </form>
    </div> <!-- /wrap -->
    <?php    
	}
       
    /**
     * Function that will register admin page options.
     */
    public function register_page_options() { // Add Section for option fields



    	add_settings_section( 'sg_section', 'SiteGlue Settings', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page
     
        //Add enable/disable radio boxes
        add_settings_field( 'sg_enable_field', 'Text Us Button', array( $this, 'enable_settings_field' ), __FILE__, 'sg_section' ); // id, title, display cb, page, section


    	// Add Phone Field
    	add_settings_field( 'sg_phone_field', 'Mobile Number', array( $this, 'phone_settings_field' ), __FILE__, 'sg_section' ); // id, title, display cb, page, section
     
    	// Add Background Color Field
    	add_settings_field( 'sg_bg_field', 'Background Color', array( $this, 'bg_settings_field' ), __FILE__, 'sg_section' ); // id, title, display cb, page, section
     	

     	// Add Text Field
    	add_settings_field( 'sg_text_field', 'Button Text', array( $this, 'text_settings_field' ), __FILE__, 'sg_section' ); // id, title, display cb, page, section

     	// Add Color Field
    	add_settings_field( 'sg_color_field', 'Button Text Color', array( $this, 'color_settings_field' ), __FILE__, 'sg_section' ); // id, title, display cb, page, section


    	// Register Settings
    	register_setting( __FILE__, 'sg_settings_options', array( $this, 'validate_options' ) ); // option group, option name, sanitize cb 
	}
     
    /**
     * Function that will add javascript file for Color Piker.
     */
    public function enqueue_admin_js() { 
    	// Css rules for Color Picker
    	wp_enqueue_style( 'wp-color-picker' );

    	// Make sure to add the wp-color-picker dependecy to js file
    	wp_enqueue_script( 'cpa_custom_js', plugins_url( 'jquery.siteglue.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
    }
     
    /**
     * Function that will validate all fields.
     */
    public function validate_options( $fields ) { 
    	$valid_fields = array();
     
    	// Validate Phone Field
    	$phone = trim( $fields['phone'] );
    	$valid_fields['phone'] = strip_tags( stripslashes( $phone ) );
     
    	// Validate Background Color
    	$background = trim( $fields['background'] );
    	$background = strip_tags( stripslashes( $background ) );
     
    	// Check if is a valid hex color
    	if( FALSE === $this->check_color( $background ) ) {
     
        	// Set the error message
        	add_settings_error( 'sg_settings_options', 'sg_bg_error', 'Insert a valid color for Background Color', 'error' ); // $setting, $code, $message, $type
         
        	// Get the previous valid value
        	$valid_fields['background'] = $this->options['background'];
     
    	} else {
     
        	$valid_fields['background'] = $background;  
     
    	}


    	// Validate Text Field
    	$text = trim( $fields['text'] );
    	$valid_fields['text'] = strip_tags( stripslashes( $text ) );



    	// Validate Color
    	$background = trim( $fields['color'] );
    	$background = strip_tags( stripslashes( $background ) );
     
    	// Check if is a valid hex color
    	if( FALSE === $this->check_color( $background ) ) {
     
        	// Set the error message
        	add_settings_error( 'sg_settings_options', 'sg_bg_error', 'Insert a valid color for Text Color', 'error' ); // $setting, $code, $message, $type
         
        	// Get the previous valid value
        	$valid_fields['color'] = $this->options['color'];
     
    	} else {
     
        	$valid_fields['color'] = $background;  
     
    	}


        // Validate Enabled Field
        $enabled = trim( $fields['enabled'] );
        $valid_fields['enabled'] = strip_tags( stripslashes( $enabled ) );

     
    	return apply_filters( 'validate_options', $valid_fields, $fields);
	}
 
     
    /**
     * Function that will check if value is a valid HEX color.
     */
    public function check_color( $value ) { 
    	if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
        	return true;
    	}
     
    	return false;
    }
     
    /**
     * Callback function for settings section
     */
    public function display_section() { /* Leave blank */ } 
     
    /**
     * Functions that display the fields.
     */
    public function enable_settings_field() {
        $val = ( isset( $this->options['enabled'] ) ) ? $this->options['enabled'] : '';

        if($val == "true") {
            echo '<input type="radio" name="sg_settings_options[enabled]" value="true" checked/>Enabled<br/>';
            echo '<input type="radio" name="sg_settings_options[enabled]" value="false" />Disabled';
        } else {
            echo '<input type="radio" name="sg_settings_options[enabled]" value="true" />Enabled<br/>';
            echo '<input type="radio" name="sg_settings_options[enabled]" value="false" checked/>Disabled';
        }
    }

    public function phone_settings_field() {  
    	$val = ( isset( $this->options['phone'] ) ) ? $this->options['phone'] : '';
    	echo '<input type="text" name="sg_settings_options[phone]" maxlength="10" style="width: 300px;"   value="' . $val . '" />';
    	echo '<p id="tagline-description" class="description">Send text message from my website to this number.</p>';
    }   
     
    public function bg_settings_field( ) { 
    	$val = ( isset( $this->options['background'] ) ) ? $this->options['background'] : '';
    	echo '<input type="text" name="sg_settings_options[background]" value="' . $val . '" class="sg-color-picker" >';
    }

    public function text_settings_field() {  
    	$val = ( isset( $this->options['text'] ) ) ? $this->options['text'] : '';
    	echo '<input type="text" name="sg_settings_options[text]" maxlength="30" style="width: 300px;" value="' . $val . '" />';
    	    	echo '<p id="tagline-description" class="description">30 Characters Max.</p>';

    }  


    public function color_settings_field( ) { 
    	$val = ( isset( $this->options['color'] ) ) ? $this->options['color'] : '';
    	echo '<input type="text" name="sg_settings_options[color]" value="' . $val . '" class="sg-color-picker" >';
    }

         
} // end class
SitGlue_Options::get_instance();


