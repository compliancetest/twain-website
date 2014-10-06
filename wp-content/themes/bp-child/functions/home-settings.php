<?php

$getting_started_steps = array('Step 1','Step 2','Step 3','Step 4','Step 5','Step 6');

add_action( 'admin_menu', 'add_home_settings_menu' );


function add_home_settings_menu() {
    add_submenu_page('themes.php','Home Settings','Home Settings','manage_options','home-settings','create_home_settings_page');
}


function create_home_settings_page() {
    global $getting_started_steps;
    $home_settings = get_option('home-settings');
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
?>
    <script type="text/javascript" src="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/js/jquery-ui-1.10.3.custom.js"></script>
    <link href="<?php echo dirname(get_bloginfo('stylesheet_url'))?>/css/jquery-ui-1.10.3.custom.css"  type="text/css" rel="stylesheet" />
    <style type="text/css">
        #home-settings .ui-tabs-nav{
            padding: 0;
            border-radius: 0;
            background: transparent;

        }
        #home-settings input[type="text"]{
            width: 50%;
        }
        .mceIframeContainer{
            height: 300px;
        }
        .mceIframeContainer iframe{
            height: 100% !important;

        }
        .ui-tabs .ui-tabs-nav li{
            display: block;
            float: none;
            border-radius: 0;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        .ui-tabs .ui-tabs-nav li a{
            float: none;
            display: block;
            border-radius: 0;
            background: #f7f7f7;
            padding: 8px 14px;
            margin: 0;
            border-right: solid 1px #DFDFDF;
            border-left: solid 1px #DFDFDF;
            border-bottom: 1px solid #DFDFDF;
            border-top: 1px solid #F9F9F9;
            outline: none;
        }

        .ui-tabs .ui-tabs-nav li.ui-tabs-active a{
            background: #fff;
            color: #333;
            border-right: solid 1px #fff;
        }
        .ui-tabs .ui-tabs-nav li.tab-separator{
            background: none repeat scroll 0 0 #F0F0F0;
            border: 1px solid #DDD !important;
            color: #333;
            font-size: 13px;
            font-weight: bold;
            padding: 14px !important;
            margin-top: 10px !important;
        }
        .ui-tabs .ui-tabs-nav li.tab-separator:first-child{
            margin-top: 0 !important;
        }
        #home-settings{
            padding: 0;
            border-radius: 0;
        }
        #home-settings-nav{
            width: 210px;
            float: left;
        }
        #home-settings-wrapper{
            margin-left: 210px;
            border-top: solid 1px #ddd;
        }
        #home-settings-wrapper .widefat{
            clear: none;
            padding-top: 5px;
        }
        #home-settings-wrapper h3{
            border-bottom: 1px solid #A0A0A0;
            font-size: 18px;
            margin: 0;
            padding-bottom: 10px;
        }
        #home-settings .widefat th {
            border-bottom: 1px solid #e1e1e1;
        }
    </style>
    <script>
        jQuery(document).ready(function($){
            var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;

            $('#customer-types-tabs .button').click(function(e) {
                var send_attachment_bkp = wp.media.editor.send.attachment;
                var button = $(this);
                var id = button.attr('id').replace('_button', '');
                _custom_media = true;
                wp.media.editor.send.attachment = function(props, attachment){
                    if ( _custom_media ) {
                        $("#"+id).val(attachment.url);
                    } else {
                        return _orig_send_attachment.apply( this, [props, attachment] );
                    };
                }

                wp.media.editor.open(button);
                return false;
            });

            $('.add_media').on('click', function(){
                _custom_media = false;
            });
        });
    </script>
    <form name="adminform" method="post" action="admin.php" onsubmit="return saveHomeSettings()">
        <input type="hidden" name="tab" id="home-settings-tab-idx" value="0" />
        <div class="wrap">
            <h2></h2>
            <br />
            <div>
                <div style="float: right">
                    <input type="submit" value="Save Changes" name="home-settings" class="button-primary" />
                    <?php wp_nonce_field('home-settings'); ?>
                </div>
                <br clear="all" />
            </div>
            <div id="home-settings">
                <div id="home-settings-nav">
                    <ul>
                        <li class="tab-separator">Home sections</li>
                        <li><a href="#top-banner">Top banner</a></li>
                        <li><a href="#customer-types-tabs">Customer Types Tabs</a></li>
                        <li><a href="#getting-started-steps">Getting Started Steps</a></li>
                    </ul>
                </div>
                <div id="home-settings-wrapper">
                    <!-- Top Banner-->
                    <div id="top-banner">
                        <h3>Top banner</h3>
                        <table class="widefat">
                            <tbody>
                                <tr>
                                    <td class="tdlabel"><b>Title</b></td>
                                    <td>
                                        <input type="text" size="50" name="top_banner_title" id="top_banner_title" value="<?php echo $home_settings['top_banner_title']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>SubTitle</b></td>
                                    <td>
                                        <input type="text" size="50" name="top_banner_subtitle" id="top_banner_subtitle" value="<?php echo $home_settings['top_banner_subtitle']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Description</b></td>
                                    <td>
                                        <?php wp_editor($home_settings['top_banner_description'], 'top_banner_description', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Video</b></td>
                                    <td>
                                        <input type="text" size="50" name="top_banner_video" id="top_banner_video" value="<?php echo htmlspecialchars($home_settings['top_banner_video']); ?>" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Top Banner-->

                    <!-- Customer Types Tabs-->
                    <div id="customer-types-tabs">
                        <h3>Customer Types Tabs</h3>
                        <table class="widefat">
                            <tbody>
                                <tr>
                                    <th colspan="2">Tab 1</th>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Title</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_1_title" id="tab_1_title" value="<?php echo $home_settings['tab_1_title']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>SubTitle</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_1_subtitle" id="tab_1_subtitle" value="<?php echo $home_settings['tab_1_subtitle']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Content</b></td>
                                    <td>
                                        <?php wp_editor($home_settings['tab_1_content'], 'tab_1_content', array('media_buttons' => true,  'editor_height' => 150)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Button link</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_1_button_link" id="tab_1_button_link" value="<?php echo $home_settings['tab_1_button_link']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Image</b></td>
                                    <td>
                                        <div class="uploader wp-media-buttons">
                                            <input type="text" size="50" name="tab_1_image" id="tab_1_image" value="<?php echo $home_settings['tab_3_image']; ?>" />
                                            <span class="add_media">
                                                <a title="Add Media" class="button insert-media" id="tab_1_image_button" href="#"><span class="wp-media-buttons-icon"></span> Upload Image</a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2">Tab 2</th>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Title</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_2_title" id="tab_2_title" value="<?php echo $home_settings['tab_2_title']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>SubTitle</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_2_subtitle" id="tab_2_subtitle" value="<?php echo $home_settings['tab_2_subtitle']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Content</b></td>
                                    <td>
                                        <?php wp_editor($home_settings['tab_2_content'], 'tab_2_content', array('media_buttons' => true,  'editor_height' => 150)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Button link</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_2_button_link" id="tab_2_button_link" value="<?php echo $home_settings['tab_2_button_link']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Image</b></td>
                                    <td>
                                        <div class="uploader wp-media-buttons">
                                            <input type="text" size="50" name="tab_2_image" id="tab_2_image" value="<?php echo $home_settings['tab_2_image']; ?>" />
                                            <span class="add_media">
                                                <a title="Add Media" class="button insert-media" id="tab_2_image_button" href="#"><span class="wp-media-buttons-icon"></span> Upload Image</a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2">Tab 3</th>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Title</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_3_title" id="tab_3_title" value="<?php echo $home_settings['tab_3_title']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>SubTitle</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_3_subtitle" id="tab_3_subtitle" value="<?php echo $home_settings['tab_3_subtitle']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Content</b></td>
                                    <td>
                                        <?php wp_editor($home_settings['tab_3_content'], 'tab_3_content', array('media_buttons' => true,  'editor_height' => 150)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Button link</b></td>
                                    <td>
                                        <input type="text" size="50" name="tab_3_button_link" id="tab_3_button_link" value="<?php echo $home_settings['tab_3_button_link']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>Image</b></td>
                                    <td>
                                        <div class="uploader wp-media-buttons">
                                            <input type="text" size="50" name="tab_3_image" id="tab_3_image" value="<?php echo $home_settings['tab_3_image']; ?>" />
                                            <span class="add_media">
                                                <a title="Add Media" class="button insert-media" id="tab_3_image_button" href="#"><span class="wp-media-buttons-icon"></span> Upload Image</a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Customer Types Tabs-->

                    <!-- Getting Started Steps-->
                    <div id="getting-started-steps">
                        <h3>Getting Started Steps</h3>
                        <table class="widefat">
                            <tbody>
                                <tr>
                                    <td class="tdlabel"><b>Description</b></td>
                                    <td>
                                        <?php wp_editor($home_settings['getting_started_steps_description'], 'getting_started_steps_description', array('media_buttons' => false,  'editor_height' => 150)) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdlabel"><b>User Guide Link</b></td>
                                    <td>
                                        <input type="text" size="50" name="user_guide_link" id="user_guide_link" value="<?php echo $home_settings['user_guide_link'] ?>" />
                                    </td>
                                </tr>
                                <?php $i = 0 ?>
                                <?php foreach ($getting_started_steps as $step): ?>
                                    <?php $i++; ?>
                                    <tr>
                                        <th colspan="2"><?php echo $step; ?></th>
                                    </tr>
                                    <tr>
                                        <td class="tdlabel"><b>Title</b></td>
                                        <td>
                                            <input type="text" size="50" name="step_<?php echo $i; ?>_title" id="step_<?php echo $i; ?>_title" value="<?php echo $home_settings['step_'. $i .'_title'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tdlabel"><b>Description</b></td>
                                        <td>
                                            <input type="text" size="50" name="step_<?php echo $i; ?>_description" id="step_<?php echo $i; ?>_description" value="<?php echo $home_settings['step_'. $i .'_description'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tdlabel"><b>Hyperlink</b></td>
                                        <td>
                                            <input type="text" size="50" name="step_<?php echo $i; ?>_hyperlink" id="step_<?php echo $i; ?>_hyperlink" value="<?php echo $home_settings['step_'. $i .'_hyperlink'] ?>" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Getting Started Steps-->
                </div>
            </div>
        </div>

    </form>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#home-settings').tabs({"active": "<?php echo isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 0?>"});
        });
        function saveHomeSettings()
        {
            //Getting Actived Tabs
            var idx = jQuery('#home-settings-nav li.ui-state-default').index(jQuery('#home-settings-nav li.ui-tabs-active').get(0));
            jQuery('#home-settings-tab-idx').val(idx);
            return true;
        }

    </script>



<?php }

add_action('admin_init', 'save_home_settings');
function save_home_settings()
{

    if(isset($_POST['home-settings']) && $_POST['home-settings'] == 'Save Changes')
    {
        check_admin_referer('home-settings');


        /*Top banner data*/
        save_home_settings_option('home-settings','top_banner_title', $_POST['top_banner_title']);
        save_home_settings_option('home-settings','top_banner_subtitle', $_POST['top_banner_subtitle']);
        save_home_settings_option('home-settings','top_banner_description', stripslashes($_POST['top_banner_description']));
        save_home_settings_option('home-settings','top_banner_video', stripslashes($_POST['top_banner_video']));

        /*Getting Started data*/
        save_home_settings_option('home-settings','getting_started_steps_description', stripslashes($_POST['getting_started_steps_description']));
        save_home_settings_option('home-settings','user_guide_link', stripslashes($_POST['user_guide_link']));
        for ($i = 1; $i <= 6; $i++){
            save_home_settings_option('home-settings','step_'. $i . '_title', stripslashes($_POST['step_'. $i . '_title']));
            save_home_settings_option('home-settings','step_'. $i . '_description', stripslashes($_POST['step_'. $i . '_description']));
            save_home_settings_option('home-settings','step_'. $i . '_hyperlink', stripslashes($_POST['step_'. $i . '_hyperlink']));
        }

        for ($i = 1; $i <= 3; $i++){
            save_home_settings_option('home-settings','tab_'. $i . '_title', stripslashes($_POST['tab_'. $i . '_title']));
            save_home_settings_option('home-settings','tab_'. $i . '_subtitle', stripslashes($_POST['tab_'. $i . '_subtitle']));
            save_home_settings_option('home-settings','tab_'. $i . '_content', stripslashes($_POST['tab_'. $i . '_content']));
            save_home_settings_option('home-settings','tab_'. $i . '_image', stripslashes($_POST['tab_'. $i . '_image']));
            save_home_settings_option('home-settings','tab_'. $i . '_button_link', stripslashes($_POST['tab_'. $i . '_button_link']));
        }
        wp_redirect("/wp-admin/admin.php?page=home-settings&tab=" . (!$_REQUEST['tab'] ? 0 : $_REQUEST['tab']));
    }
}


function save_home_settings_option($option_name, $key, $value) {
    $options = get_option( $option_name );

    if ( !$options ) {
        add_option( $option_name, array($key => $value) );
    } else {
        $options[$key] = $value;
        update_option( $option_name, $options );
    }
}


function get_home_settings_option($option_name, $key, $default = false) {
    $options = get_option($option_name);

    if ( $options ) {
        return (array_key_exists( $key, $options )) ? $options[$key] : $default;
    }

    return $default;
}