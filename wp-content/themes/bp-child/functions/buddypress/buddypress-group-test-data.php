<?php
if ( class_exists( 'BP_Group_Extension' ) )
{
    class CP_Test_Data_Group_Extension extends BP_Group_Extension {
        var $enable_create_step = false;
        function __construct() {
            $this->name = 'Buddypress Group TestData';
            $this->slug = 'testdata';
 
            $this->create_step_position = 21;
            $this->nav_item_position = 31;            
        }
        
        function edit_screen() {
            if(!bp_is_group_creation_step($this->slug))
            {
                return false;
            }
            ?><?php
            wp_nonce_field('groups_create_save_' . $this->slug);
        }
        
        function display()
        {
            //Check if the current user is member of the group or not
            if(bp_group_is_member())
            {
                locate_template( array( 'groups/single/test-data.php'        ), true );
            }else{
                ?><p>You need to join the community to view Test Data.</p><?php
            }
            ?>
            
            <?php
        }
    }
    
    bp_register_group_extension('CP_Test_Data_Group_Extension');
}