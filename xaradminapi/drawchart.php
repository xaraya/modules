<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * Utility function to show a pie chart
 *
 * Based on work by:
 * 2D Pie Chart Version 1.0
 * Programer: Xiao Bin Zhao
 * E-mail: love1001_98@yahoo.com
 * Date: 03/31/2001
 * All Rights Reserved 2001.
 *
 * @public
 * @author Richard Cave
 * @param type
 * @param title
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_adminapi_drawchart($args)
{
    // Extract args
    extract($args);

    // Argument check
    if (!isset($type)) {
        $type = 'osnam';
    }
    if (!isset($title)) {
        $title = 'Sniffer Results';
    }

    // Security check
    if(!xarSecurityCheck('ReadSniffer')) return;

    // Set image defaults
    $imageWidth = 300; //image width
    $imageHeight = 340; //image height
    $size = 300; // medium 200 = small, 400 = large
    $labelWidth = 10; //label width, no need to change

    // Check that the GD library is available
    if (!extension_loaded('gd')) {
        return;
    }

    $dataTotal = xarModAPIFunc('sniffer',
                               'user',
                               'countitems');

    $products = xarModAPIFunc('sniffer',
                              'admin',
                              'chartcount',
                              array('type' => $type));

    // Assign values to pie and name variables
    $x = 1;
    foreach ($products as $product) {
        $pie[$x] = $product['groupcount'];
        $name[$x] = $product['productname'];
        // Set name to nothing if empty
        if (empty($name[$x])) {
            $name[$x] = 'unknown';
        }
        $x++;
    }

    // Edit the values for the segments to make them positive integer values.
    $number = count($pie);
    for ($x = 1; $x <= $number; $x++) {
        $pie[$x] = intval($pie[$x]);
        $pie[$x] = abs($pie[$x]);
    }

    // Pass the descriptions thru htmlspecialchars to not mess up
    // the code of the results page.
    for ($x = 1; $x <= $number; $x++) {
        $name[$x] = htmlspecialchars($name[$x]);
    }

    $pic = imagecreate( $imageWidth, $imageHeight ); //make image area

    //color array for chart. addmore colors if you have more data than colors below.
    $hexColor = array("255,255,255",  // white - index 0 is never shown
                      "255,0,0",      // red
                      "0,0,255",      // blue
                      "0,255,0",      // green
                      "255,255,0",    // yellow
                      "255,128,0",    // orange
                      "255,0,255",    // magenta
                      "255,0,128",    // pink
                      "0,255,255",    // cyan
                      "100,100,100",  // grey
                      "128,0,128",    // purple
                      "50,255,115"    // light green
                );

    // If we have more slices, then randomly generate some colors
    if (count($hexColor) < $number+1) {
        srand ((double) microtime() * 1000000);
        for ($x = count($hexColor); $x < $number+1; $x++) {
            $red = rand(0,255);
            $green = rand(0,255);
            $blue = rand(0,255);
            $hexColor[] = "$red,$green,$blue";
        }
    }

    // Allocate colors
    $color = array();
    $white = ImageColorAllocate( $pic, 255, 255, 255 );
    $black = ImageColorAllocate( $pic, 0, 0, 0 );
    $grey = ImageColorAllocate( $pic, 215, 215, 215 );

    // Draw the circle
    imagefill( $pic, 0, 0, $white ); //make image background

    // Make 3D effect
    imagearc($pic, 150, 150, 300, 300, 0, 360, $black);
    imagearc($pic, 150, 190, 300, 300, 0, 180, $black);
    imageline($pic, 0, 150, 0, 190, $black);
    imageline($pic, 300, 150, 300, 190, $black);

    $degree = 0;

    // Calculate the total number and the percentages.
    $total = array_sum($pie);
    for ($x = 1; $x <= $number; $x++) {
        $ppie[$x] = round($pie[$x]*100/$total, 2);
    }

    // Check whether one segment is 100%, if so fill the
    // circle completely with one color and skip the rest of the script.
    if(in_array(100, $ppie)) {
        $x = array_search(100, $ppie);
        // Allocate color
        $hexColorSplit = explode(',',$hexColor[$x]);
        $color[$x] = ImageColorAllocate($pic, $hexColorSplit[0], $hexColorSplit[1], $hexColorSplit[2]);
        // Fill image
        imagefilltoborder($pic, 150, 150, $black, $color[$x]);
    } else {

        // Determine the angle of each segment
        // and the angle in relation to the others.
        for ($x = 1; $x < $number; $x++) {
            $y = $x - 1;
            if ($y == 0 ) {
                $apie[$x] = 360 * ($pie[$x] / $total);
            } else {
                $apie[$x] = 360 * ($pie[$x] / $total) + $apie[$y];
            }
            $rpie[$x] = deg2rad($apie[$x]);
            $xpie[$x] = (sin($rpie[$x]) * 150) + 150;
            $ypie[$x] = 150 - (cos($rpie[$x]) * 150);
        }

        // Draw the lines bordering each segment.
        imageline($pic, 150, 150, 150, 0, $black);
        for ($x = 1; $x < $number; $x++) {
            imageline($pic, 150, 150, $xpie[$x], $ypie[$x], $black);
        }


        // Fill each segment with the appropriate color.
        for ($x = 1; $x <= $number; $x++) {
            // Allocate color
            $hexColorSplit = explode(',',$hexColor[$x]);
            $color[$x] = ImageColorAllocate($pic, $hexColorSplit[0], $hexColorSplit[1], $hexColorSplit[2]);

            $y = $x - 1;
            if ($y == 0 ) {
                $aapie[$x] = ((360 * ($pie[$x] / $total))/2);
            } else {
                $aapie[$x] = ((360 * ($pie[$x] / $total))/2) + $apie[$y];
            }
            $rrpie[$x] = deg2rad($aapie[$x]);
            $xxpie[$x] = (sin($rrpie[$x]) * 150) + 150;
            $yypie[$x] = 150 - (cos($rrpie[$x]) * 150);
            $xcolor[$x] = (150 + $xxpie[$x])/2;
            $ycolor[$x] = (150 + $yypie[$x])/2;
            imagefilltoborder($pic, $xcolor[$x], $ycolor[$x], $black, $color[$x]);
         }
    }

    // Fill the remaining parts of the chart.
    imagefilltoborder($pic, 0, 0, $black, $white);
    imagefilltoborder($pic, 299, 1, $black, $white);
    imagefilltoborder($pic, 1, 339, $black, $white);
    imagefilltoborder($pic, 299, 339, $black, $white);
    imagefilltoborder($pic, 150, 320, $black, $grey);

    // Resize the image to create the 3D effect and the final size.
    // This is where the greatest loss in image quality occurs.
    $size1 = ($size / 10) * 6;
    $pic1 = imagecreate($size * 2, $size1 * 1.5);
    imagecopyresized($pic1, $pic, 0, 0, 0, 0, $size, $size1, 300, 340);
    imagedestroy($pic);
    imageinterlace($pic1, 1);

    // setup for the menu and print title
    $centerX = $size / 2;
    $centerY = $size1 / 2;
    $titleX = $centerX + $size / 2 + 10;
    $titleY = $centerY - $size / 4;
    $labelX = $titleX + $size / 2 + 10;
    $labelY = $titleY + ($labelWidth*2) + 10;

    // Chart title and date
    $black = ImageColorAllocate( $pic1, 0, 0, 0 );
    imagestring( $pic1, 3, $titleX, $titleY, $title, $black );
    imagestring( $pic1, 1, $titleX, $titleY + 14, date( "Y-m-d H:i" ), $black );

    // Create boxes with the color of each segment for the index
    $boxColor = array();
    for ($x = 1; $x <= $number; $x++) {
        // Allocate color
        $hexColorSplit = explode(',',$hexColor[$x]);
        $boxColor[$x] = ImageColorAllocate($pic1, $hexColorSplit[0], $hexColorSplit[1], $hexColorSplit[2]);
        imagefilledrectangle( $pic1, $titleX + 1, $labelY, $titleX + $labelWidth, $labelY + $labelWidth, $boxColor[$x] );
        $segmentText = $name[$x] . "  [" . $pie[$x] . "]  " . $ppie[$x] . "%";
        imagestring( $pic1, 2, $titleX + $labelWidth + 5, $labelY, $segmentText, $black );
        $labelY += $labelWidth + 2;
    }

    // The total
    $labelY += $labelWidth + 2;
    imagestring( $pic1, 3, $titleX, $labelY, "Total:", $black );
    imagestring( $pic1, 3, $titleX + $labelWidth + 60, $labelY, $dataTotal, $black );

    // Display the chart.
    Header( "Content-type: image/jpeg" ); //output image
    imagejpeg( $pic1 );
    imagedestroy( $pic1 ); //remove image from memory
}

?>
