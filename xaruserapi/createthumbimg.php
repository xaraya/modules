<?php
/**
 * Initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 *Create a thumbnail for an image
 * @PARAM:
         'file'       - Original File
        'thumbwidth' - Width of thumbnail
        'thumbheight'- Height of thumbnail
        'newfile'    - Name of new Filer
*/
function uploads_userapi_createthumbimg($args)
{
    extract($args);

    $netpbm_path = xarModGetVar('uploads', 'netpbm_path');

    if( isset($netpbm_path) && ($netpbm_path != '') )
    {
        createthumbNetPBM( $file, $thumbwidth, $thumbheight, $newfile );
    } else {
        createthumb( $file, $thumbwidth, $thumbheight, $newfile );
    }

}




function ImageCreateFrom($file)
{
    $IMAGE_PROPERTIES = GetImageSize($file);

    switch( $IMAGE_PROPERTIES[2] )
    {
        case 1:
            $im = @imagecreatefromgif ($file); /* Attempt to open */
            break;
        case 2:
            $im = @imagecreatefromjpeg ($file); /* Attempt to open */
            break;
        case 3:
            $im = @imagecreatefrompng ($file); /* Attempt to open */
            break;
        default:
            $im = false;
    }
    if (!$im) { /* See if it failed */
        $im = imagecreate (150, 30); /* Create a blank image */
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring ($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}




function createthumb($IMAGE_SOURCE,$THUMB_X,$THUMB_Y,$OUTPUT_FILE)
{

    $BACKUP_FILE = $OUTPUT_FILE . "_backup.jpg";
    copy($IMAGE_SOURCE,$BACKUP_FILE);


    $SRC_IMAGE = ImageCreateFrom($BACKUP_FILE);
    $SRC_X = ImageSX($SRC_IMAGE);
    $SRC_Y = ImageSY($SRC_IMAGE);

    if (($THUMB_Y == "0") AND ($THUMB_X == "0")) {
      return(0);
    } elseif ($THUMB_Y == "0") {
      $SCALEX = $THUMB_X/($SRC_X-1);
      $THUMB_Y = $SRC_Y*$SCALEX;
    } elseif ($THUMB_X == "0") {
      $SCALEY = $THUMB_Y/($SRC_Y-1);
      $THUMB_X = $SRC_X*$SCALEY;
    }

    $THUMB_X = (int)($THUMB_X);
    $THUMB_Y = (int)($THUMB_Y);

    $DEST_IMAGE = imagecreatetruecolor($THUMB_X,$THUMB_Y);


    unlink($BACKUP_FILE);
    if (!ImageCopyResampleBicubic($DEST_IMAGE, $SRC_IMAGE, 0, 0, 0, 0, $THUMB_X, $THUMB_Y, $SRC_X, $SRC_Y)) {

      imagedestroy($SRC_IMAGE);
      imagedestroy($DEST_IMAGE);
      return(0);
    } else {
      imagedestroy($SRC_IMAGE);

    if (ImageJPEG($DEST_IMAGE,$OUTPUT_FILE))
    {
       imagedestroy($DEST_IMAGE);
       return(1);
    }
      imagedestroy($DEST_IMAGE);
    }
    return(0);


} # end createthumb


function LoadJpeg ($imgname)
{
    $im = @imagecreatefromjpeg ($imgname); /* Attempt to open */
    if (!$im) { /* See if it failed */
        $im  = imagecreate (150, 30); /* Create a blank image */
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc  = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring ($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}
/*
function ImageCopyResampleBicubic (&$dst_img, &$src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
{
  ImagePaletteCopy ($dst_img, $src_img);
  $rX = $src_w / $dst_w;
  $rY = $src_h / $dst_h;
  $w = 0;
  for ($y = $dst_y; $y < $dst_h; $y++) {
    $ow = $w; $w = round(($y + 1) * $rY);
    $t = 0;
    for ($x = $dst_x; $x < $dst_w; $x++) {
      $r = $g = $b = 0; $a = 0;
      $ot = $t; $t = round(($x + 1) * $rX);
      for ($u = 0; $u < ($w - $ow); $u++) {
        for ($p = 0; $p < ($t - $ot); $p++) {
          $c = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $ot + $p, $ow + $u));
          $r += $c['red'];
          $g += $c['green'];
          $b += $c['blue'];
          $a++;
        }
      }
      ImageSetPixel ($dst_img, $x, $y, ImageColorClosest ($dst_img, $r / $a, $g / $a, $b / $a));
    }
  }
}*/

function ImageCopyResampleBicubic (&$dst_img, &$src_img, $dst_x,
    $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    /*
    port to PHP by John Jensen July 10 2001 (updated June 13, 2002 by tim@smoothdeity.com) --
    original code (in C, for the PHP GD Module) by jernberg@fairytale.se
    */
    {
        $palsize = ImageColorsTotal ($src_img);
        for ($i = 0; $i < $palsize; $i++)
            {  // get palette.
            $colors = ImageColorsForIndex ($src_img, $i);
            ImageColorAllocate ($dst_img, $colors['red'], $colors['green'], $colors['blue']);
            }

        $scaleX = ($src_w - 1) / $dst_w;
        $scaleY = ($src_h - 1) / $dst_h;

        $scaleX2 = (int) ($scaleX / 2);
        $scaleY2 = (int) ($scaleY / 2);

        $dstSizeX = imagesx( $dst_img );
        $dstSizeY = imagesy( $dst_img );
        $srcSizeX = imagesx( $src_img );
        $srcSizeY = imagesy( $src_img );


        for ($j = 0; $j < ($dst_h - $dst_y); $j++) {
            $sY = (int) ($j * $scaleY) + $src_y;
            $y13 = $sY + $scaleY2;

            $dY = $j + $dst_y;

            if (($sY > $srcSizeY) or ($dY > $dstSizeY))
                break 1;


            for ($i = 0; $i < ($dst_w - $dst_x); $i++) {
                $sX = (int) ($i * $scaleX) + $src_x;
                $x34 = $sX + $scaleX2;

                $dX = $i + $dst_x;

                if (($sX > $srcSizeX) or ($dX > $dstSizeX))
                    break 1;

                $color1 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $y13));
                $color2 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $sY));
                $color3 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $y13));
                $color4 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $sY));

                $red = ($color1['red'] + $color2['red'] + $color3['red'] + $color4['red']) / 4;
                $green = ($color1['green'] + $color2['green'] + $color3['green'] + $color4['green']) / 4;
                $blue = ($color1['blue'] + $color2['blue'] + $color3['blue'] + $color4['blue']) / 4;

                ImageSetPixel ($dst_img, $dX, $dY,
                    ImageColorClosest ($dst_img, $red, $green, $blue));
            }
        }
        return true;
    }

// **********************
// ** NetPBM Support
// *****************

function createthumbnetpbm( $file, $thumbwidth, $thumbheight, $newfile )
{
    // Path to NetPBM installation
    $bin_path = xarModGetVar('uploads', 'netpbm_path');

    // Create thumb from $file and store it as $newfile
    $absname = $file;
    $thumbname = $newfile;


    // Get image info
    $info = getimagesize($absname);
    $imagewidth = $info[0];
    $imageheight = $info[1];

    // Workout thumbnail width/height
    $new_w = $thumbwidth;
    $new_h = $thumbheight;
    if( !isset( $new_h ) || ($new_h == 0) )
    {
        $scale = ($imagewidth / $new_w);
        $new_h = round($imageheight / $scale);
    }

    if( !isset( $new_w ) || ($new_w == 0) )
    {
        $scale = ($thumbheight / $new_h);
        $new_w = round($imagewidth / $scale);
    }

    // determine file formats
    switch($info[2])
    {
        // GIF
        case 1:
            $topnm = "giftopnm";
            $tothumb = "ppmtogif";
            $quant = "ppmquant 256";
            break;
        // JPEG
        case 2:
            $topnm = "jpegtopnm";
            $tothumb = "ppmtojpeg";
            $quant = "";
            break;
        // PNG
        case 3:
            $topnm = "pngtopnm";
            $tothumb = "pnmtopng";
            $quant = "ppmquant 256";
            break;
    }
    // switch on file type to figure out which executables we need
    // build the shell command
    $cmd = $bin_path . $topnm . " \"" . $absname . "\" | ";
    $cmd .= $bin_path . "pnmscale -xysize " . $new_w . " " . $new_h . " | ";
    if( $quant != "" )
    {
        $cmd .= $bin_path . $quant . " | ";
    }
    $cmd .= $bin_path . $tothumb . " > \"" . $thumbname . "\"";
    // create the path to the thumbnail, if necessary, and execute the shell command
    // to create the thumbnail file
/*
    echo $cmd;
    echo "<hr/>";
//    exit();
    echo "Executing...<br/>";
    echo "<pre>";
//    passthru($cmd);
    exit();
    exit();
*/

    exec($cmd);
}


?>
