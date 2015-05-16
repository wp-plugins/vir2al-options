=== vir2al options ===
Contributors: 13848695
Donate link: http://vir2al.ch/
Tags: options
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A easy way to manage your options Page.

== Description ==

This Plugin let's you create an beautiful options page based on AJAX.  

**Note:** This PlugIn is made for Developers only. If you are not the Developer of the theme or plugin. Ask your developer.

SetUp
-----

 1. Create a File called "options.php".
 2. Add the following to your functions.php or plugin.php 
`<?php //check if plugin exists
if(function_exists ('create_vtl_options_page')){
  include('options.php');
  add_action('admin_menu', 'register_options_pages');
} else {
	function vtco_error_notice() {
	    echo '<div class="error"><p>'.__('You need to Install the Plugin "vir2al options"').'</p></div>';
	}
	add_action( 'admin_notices', 'vtco_error_notice' );
} ?>`

options.php
-----------

    <?php
    function vtco_register_settings_pages(){
	    //Add any Page you want here
        add_options_page( 'Additional Options', 'Additional Options', 'manage_options', 'addoptions', 'options_cbfunc');
    }
    
    function options_cbfunc(){
      ob_start();
      // create your form ?>
      <fieldset data-name="Tab 1">
	      <table>
	          <tr>
	              <td>Test</td>
	              <td><?php echo get_vtlo_input('option_name'); ?></td>
		      </tr>
		  </table>
      </fieldset>
      <fieldset data-name="Tab 2">
	      <table>
	          <tr>
	              <td>Test Image</td>
	              <td><?php echo get_vtlo_imgupload('option_name_img'); ?></td>
		      </tr>
          </table>
       </fieldset>
       <?php
       $html=ob_get_contents();
       ob_end_clean();
       //let the magic happen
       return create_vtl_options_page($html);
    }
	?>

Possible inputs
---------------

**Textarea:**		get_vtlo_textarea(name);
**Input:**			get_vtlo_input(name);
**Select:**			get_vtlo_select(name,options_array);
**IMG:**				get_vtlo_imgupload(name);
**Multi IMG:** 	get_vtlo_multiimgupload(name);
**Colorinput:**  get_vtlo_colorinput(name,default_color);	

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Install the Plugin either via the WordPress.org plugin directory, or by uploading the files to your server.
2. Activate the Plugin
3. You're ready to set up your Options Page.

== Screenshots ==

1. the options Page

== Frequently Asked Questions ==

= No questions yet =

Please feel free to contact me: nico@vir2al.ch

== Changelog ==

= 1.0 =
* First Version

== Upgrade Notice ==
none
