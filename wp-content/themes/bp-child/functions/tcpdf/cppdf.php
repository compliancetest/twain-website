<?php

// Include the main TCPDF library
require_once('tcpdf.php');

class CPPDF extends TCPDF {


    //Page header
    public function Header()
    {
        // Background color
        $this->Rect(0, 0, 210, 20, 'F', '', $fill_color = array(91, 117, 182));

        $drummond_group = K_PATH_IMAGES . "drummond-group.png";
        $this->Image($drummond_group, 4, 4, 45, 0, 'PNG', home_url(), 'N', false, $dpi = 300, '', false, false, 0, false, false, false, false);

    }


    //Page Footer
    public function Footer()
    {
        // Fill footer with background color
        $this->Rect(0, 278, 210, 40, 'F', '', $fill_color = array(91, 117, 182));

        // Position at 19 mm from bottom
        $this->SetY(-14);

        // Set font
        $this->SetTextColor(255, 255, 255);

        // Left link
        $this->Write(10, home_url(), home_url(), false, 'L', true);

        // Right logo
        $image_file = K_PATH_IMAGES . "header-logo.png";
        $this->Image($image_file, 180, 283, 38, 0, 'PNG', '', 'N', true, $dpi = 300, 'R', false, false, 0, false, false, false, false);
    }
}