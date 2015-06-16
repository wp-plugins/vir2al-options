<?php
/*
Plugin Name:    vir2al Options
Plugin URI:     http://vir2al.ch
Description:    A easy way to manage your options Page.
Version:        1.0.1
Author:         Nico Martin
Author URI:     http://vir2al.ch

Text Domain:   vtlo
Domain Path:   /languages/
*/

// Add Admin Scripts
$keep_html=array(
    'a' => array(
        'href' => array(),
        'title' => array()
    ),
    'br' => array(),
    'em' => array(),
    'strong' => array()
);

function vtlo_add_admin_script() {
    wp_register_style( 'vtlo_admin_styles',plugins_url( 'vtl_options.css', __FILE__ ), false, '1.0.0' );
    wp_enqueue_style( 'vtlo_admin_styles' );

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker');
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'vtlo_add_admin_script' );


// Add ajax
add_action( 'wp_ajax_vtlo_save_settings', 'vtlo_save_settings_func' );
function vtlo_save_settings_func(){

  global $keep_html;

  check_ajax_referer('vtl_options_6z2' );

  foreach($_POST as $k=>$v){
    if($k!='action' && $k!='_ajax_nonce'){
      $value=wp_kses($v,$keep_html);
      update_option( $k, wp_check_invalid_utf8($value,true));
    }
  }
  echo 'ok';

  exit();
}

function create_vtl_options_page($html) {
ob_start();

?>

<div class="wrap">
    <h2><?php _e('Options','vtlo'); ?></h2>
    <div id="vtl_optionslegend"></div>
    <form id="vtl_optionsform" method="post" action="">
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'vtl_options_6z2' ); ?>">
        <?php
        echo $html;

        /*
        $html muss ein Formular sein, wie dieses:

        <fieldset data-name="<?php _e('Allgemein','vtls'); ?>">
            <table>
                <tr>
                    <td><?php _e('Slider','vtls'); ?>:</td>
                    <td><?php echo get_vtlo_input('vtls_slider'); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Slider-2','vtls'); ?>:</td>
                    <td><?php echo get_vtlo_input('vtls_slider3'); ?></td>
                </tr>
            </table>
        </fieldset> */
        ?>
        <p class="submit" style="margin:0px 20px 20px 20px">
          <a class="button button-primary" id="submit_vtls_btn" style="cursor:pointer" onclick="submit_vtls();"><?php _e('Save options','vtlo'); ?></a>
        </p>
    </form>
    <p><?php printf(__('"vir2al Options" by %1$s','vtlo'), '<a href="http://vir2al.ch" id="gtvtl" target="_blank">vir<b>2</b>al websolutions</a>'); ?></p>
</div>
<script type="text/javascript">
jQuery('#wpcontent').addClass('vtl_settingspage');
jQuery('html').css('background-color','#DEEBF2');

jQuery(document).ready(function($){
  var i=1;
  $('#vtl_optionsform fieldset').each(function(){
    $(this).attr('data-id',i);
    $(this).addClass('fs_'+i);
    $(this).hide();
    var titel=$(this).attr('data-name');
    $('#vtl_optionslegend').append('<a class="trigger_'+i+'" onclick="showtab(this,'+i+');">'+titel+'</a>');
    i++;
  });

  if(window.location.hash) {
    var hash = window.location.hash.replace('#','');
    $('#vtl_optionsform fieldset.fs_'+hash).show();
    $('#vtl_optionslegend a.trigger_'+hash).addClass('active');
  }else{
    $('#vtl_optionslegend a.trigger_1').addClass('active');
    $('#vtl_optionsform fieldset.fs_1').show();
  }
});


function submit_vtls(){
    var data = jQuery('#vtl_optionsform fieldset:visible select, #vtl_optionsform fieldset:visible textarea, #vtl_optionsform fieldset:visible input').serialize();
    var data = data+'&action=vtlo_save_settings&_ajax_nonce='+jQuery('#_wpnonce').val();
    var btnwidth = jQuery('#submit_vtls_btn').width();
    var btnhtml = jQuery('#submit_vtls_btn').html();
    jQuery('#submit_vtls_btn').width(btnwidth);
    jQuery('#submit_vtls_btn').html('<img style="height:13px;width:13px;" src="/wp-admin/images/wpspin_light.gif"/>');
    jQuery.ajax({
        type: "POST",
        url: "<?php echo admin_url('admin-ajax.php'); ?>",
        data: data
      }).done(function() {
        var id = jQuery('#vtl_optionsform fieldset:visible').attr('data-id');
        var content = jQuery('#vtl_optionslegend a.active'+id).attr('data-name');
        jQuery('#vtl_optionslegend a.trigger_'+id+' img').remove();
        jQuery('#vtl_optionsform fieldset.fs_'+id).removeClass('tosave');
        jQuery('#submit_vtls_btn').html(btnhtml);
      });
}
jQuery(function($){
    $('#vtl_optionsform').find('select, textarea, input').each(function(){
        $(this).change(function(){
         var id=$(this).parents('fieldset').attr('data-id');
         if(!$(this).parents('fieldset').hasClass('tosave')){
           //$('#vtl_optionslegend a.trigger_'+id).append('<i class="fa fa-floppy-o"></i>');
           $('#vtl_optionslegend a.trigger_'+id).append('<img style="margin-left:10px;" src="<?php echo plugins_url( 'img/save.png', __FILE__ ) ?>"/>');
           $(this).parents('fieldset').addClass('tosave');
         }

        });
    });
});



function showtab(e,id){
  jQuery('#vtl_optionsform fieldset').hide();
  jQuery('#vtl_optionslegend a').removeClass('active');
  jQuery(e).addClass('active');
  //window.location.hash=id;
  jQuery('#vtl_optionsform fieldset.fs_'+id).show();
  document.location.hash = id;
}

function remove_image(id){
  jQuery('#'+id).val('');
  jQuery('#'+id+'_img').attr('src','<?php echo plugins_url( 'default_img.png', __FILE__ ); ?>');
}



var uploader;
function upload_image(id) {

 //Extend the wp.media object
 uploader = wp.media.frames.file_frame = wp.media({
    title: 'Choose Image',
    button: {
        text: 'Choose Image'
    },
    multiple: false
 });

 //When a file is selected, grab the URL and set it as the text field's value
 uploader.on('select', function() {
    attachment = uploader.state().get('selection').first().toJSON();
    var url = attachment['url'];
    var iid = attachment['id'];
    jQuery('#'+id).val(iid);
    jQuery('#'+id).trigger('change');
    jQuery('#'+id+'_img').attr('src',url);
 });

 uploader.on('open', function(){
    var selection = uploader.state().get('selection');
    var selected = jQuery('#'+id).val(); // the id of the image
    if (selected) {
        selection.add(wp.media.attachment(selected));
    }
 });

 //Open the uploader dialog
 uploader.open();
}


var uploader;
function upload_multiimage(id) {

 //Extend the wp.media object
 uploader = wp.media.frames.file_frame = wp.media({
    title: 'Choose Image',
    button: {
        text: 'Choose Image'
    },
    multiple: false
 });

 //When a file is selected, grab the URL and set it as the text field's value
 uploader.on('select', function() {
    attachment = uploader.state().get('selection').first().toJSON();
    var url = attachment['url'];
    var iid = attachment['id'];
    jQuery('#'+id+'_multiimgs').append('<span id="multiimg_'+iid+'_'+id+'" class="'+id+'_multiimg multiimg"><img src="'+url+'" style="width:60px;height:60px;" alt="" /><br><a style="color:#B8302E;" onclick="remove_multiimg(\''+id+'\',\''+iid+'\');"><?php _e('entfernen','vtls'); ?></a></span>');
    var oldids=jQuery('#'+id).val().split(',');
    oldids.push(iid);
    var newids=oldids.join();
    jQuery('#'+id).val(newids);
 });

 uploader.on('open', function(){
    var selection = uploader.state().get('selection');
    var selected = jQuery('#'+id).val(); // the id of the image
    if (selected) {
        selection.add(wp.media.attachment(selected));
    }
 });

 //Open the uploader dialog
 uploader.open();
}

function remove_multiimg(input,image){
    var oldids=jQuery('#'+input).val().split(',');
    newarray = jQuery.grep(oldids, function(value) {
      return value != image;
    });
    var newids=newarray.join();
    jQuery('#'+input).val(newids);
    jQuery('#multiimg_'+image+'_'+input).hide();
}


function goodbye() {
  var canleave=1;
  jQuery('#vtl_optionsform fieldset.tosave').each(function(){
    canleave=0;
  });
    if (canleave == 0) {
            return "<?php _e('There are some unsaved Fields.','vtlo'); ?>";
        }
    }
window.onbeforeunload=goodbye;

</script>


<?php
return ob_get_contents();
ob_end_clean();
}



/*vtl_settings_elements*/
function get_vtlo_select($id,$options){
  global $settings_list;
  $settings_list[]=$id;
  $html='<select name="'.$id.'" id="'.$id.'">';
  foreach($options as $o){
    $selected='';
    if(get_option($id,'')==$o){$selected='selected';}
    $html.='<option '.$selected.' value="'.$o.'">'.$o.'</option>';
  }
  $html.='</select>';
  return $html;
}
function get_vtlo_multiimgupload($id){
    $getimgs=array_filter(explode(',',get_option($id,'')));
    echo '<div id="'.$id.'_multiimgs" class="multiimgs">';
    foreach($getimgs as $i){
      echo '<span id="multiimg_'.$i.'_'.$id.'" class="'.$id.'_multiimg multiimg"><img src="'.wp_get_attachment_image_src($i,'thumbnail')[0].'" style="width:60px;height:60px;" alt="" /><br><a style="color:#B8302E;" onclick="remove_multiimg(\''.$id.'\',\''.$i.'\');">'.__('remove','vtlo').'</a></span>';
    }
    echo '</div><div style="clear:both"></div><input type="hidden" id="'.$id.'" name="'.$id.'" value="'.implode(',',$getimgs).'" /><p><a class="button button-primary" onclick="upload_multiimage(\''.$id.'\');">'.__('Add image').'</p>';
}
function get_vtlo_imgupload($id){
    global $settings_list;
    $settings_list[]=$id;
    $getimg=get_option($id,'');
    if($getimg!=''){
        $url=wp_get_attachment_image_src($getimg,'thumbnail');
        $imgurl=$url[0];
        $del = '<br><a style="color:#B8302E;" onclick="remove_image(\''.$id.'\');">'.__('remove image','vtlo').'</a>';
    } else {
        $imgurl=plugins_url( 'default_img.png', __FILE__ );
        $del = '';
    }
    $html='<input type="hidden" id="'.$id.'" name="'.$id.'" value="'.$getimg.'" />';
    $html.='<a onclick="upload_image(\''.$id.'\');">';
    $html.='<img src="'.$imgurl.'" id="'.$id.'_img" style="width:60px;height:60px;" alt="" />';
    $html.='</a>'.$del;
    return $html;
}

function get_vtlo_textarea($id){
  global $settings_list,$keep_html;
  $settings_list[]=$id;
  $the_tags='';
  foreach($keep_html as $key=>$v){$display_key[]=$key;};
  $html='<textarea name="'.$id.'" id="'.$id.'" cols="30" rows="10">'.stripslashes(get_option($id,'')).'</textarea><br><small>Allowed Tags: '.implode(', ',$display_key).'</small>';
  return $html;
}

function get_vtlo_input($id){
    global $settings_list;
  $settings_list[]=$id;
  $html='<input type="text" name="'.$id.'" id="'.$id.'" value="'.htmlentities(stripslashes(get_option($id,''))).'" />';
  return $html;
}

function get_vtlo_colorinput($id,$dc){
    global $settings_list;
  $settings_list[]=$id;
    $html= '<input name="'.$id.'" id="'.$id.'" type="text" value="'.stripslashes(get_option($id,'')).'" class="'.$id.'_class colorpicker" data-default-color="'.$dc.'" />';
    $html.='<script type="text/javascript">jQuery(document).ready(function($){$(\'.'.$id.'_class\').wpColorPicker();});</script>';
  return $html;
}

/*Test Options Page


add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_submenu_page( 'edit.php?post_type=parks', 'My Custom Submenu Page', 'My Custom Submenu Page', 'manage_options', 'my-custom-submenu-page', 'my_custom_submenu_page_callback' );
}

function my_custom_submenu_page_callback() {
    ob_start();
    ?>
    <fieldset data-name="<?php _e('Allgemein'); ?>">
            <table>
                <tr>
                    <td><?php _e('Slider','vtls'); ?>:</td>
                    <td><?php echo get_vtlo_input('vtls_slider'); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Slider-2','vtls'); ?>:</td>
                    <td><?php echo get_vtlo_input('vtls_slider3'); ?></td>
                </tr>
            </table>
        </fieldset>
    <?php
    $html=ob_get_contents();
    ob_end_clean();
	return create_vtl_options_page($html);
}
*/
?>