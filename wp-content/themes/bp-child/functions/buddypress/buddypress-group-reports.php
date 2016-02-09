<?php
if ( class_exists( 'BP_Group_Extension' ) )
{
    class CP_Reports_Group_Extension extends BP_Group_Extension {
        var $enable_create_step = false;
        function __construct() {
            $this->name = 'Reports';
            $this->slug = 'reports';

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
            if (is_user_logged_in()) {
                locate_template( array( 'groups/single/reports.php'        ), true );
            }else{
                echo '<p>' . MESSAGE_WARNING_ANONYMOUS . '</p>';
            }
        }

        public function download(){
            error_reporting(E_ALL);
            include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel.php';
            include_once __DIR__ . '/../generate-json/phpExcel/Classes/PHPExcel/IOFactory.php';

            $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
            $excel2 = $excel2->load(  __DIR__ . '/../../groups/templates/SuperStreamTestProgress.xlsx' ); // Empty Sheet
            $excel2->setActiveSheetIndex(0);
            $excel2->getActiveSheet()->setCellValue('C4', '4')
                ->setCellValue('C7', '5')
                ->setCellValue('C8', '6')
                ->setCellValue('C9', '7');

            $excel2->setActiveSheetIndex(1);
            $excel2->getActiveSheet()->setCellValue('A7', '4')
                ->setCellValue('C7', '5');
            $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
            $objWriter->save( 'TWAINTestProgress.xls' );
        }
    }

    bp_register_group_extension('CP_Reports_Group_Extension');
}