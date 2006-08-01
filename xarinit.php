<?php
/**
 * init file for installing/upgrading formantibot module
 *
 * @package Modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @link http://xaraya.com/index.php/release/761.html  
 * @subpackage formantibot
 */

/**
 * Initializes the module, adding to the list of
 * currently initialized modules
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function formantibot_init()
{

    // heigh of security image
    $settings['image_width'] = 230;

    // width of security image
    $settings['image_height'] = 40;

    // how many letters in the code
    $settings['code_length'] = 7;

    // path to ttf font to use
    $settings['ttf_file'] = "modules/formantibot/fonts/elephant.ttf";

    // size of the font
    $settings['font_size'] = 20;

    // minimum angle in degress of letter. counter-clockwise direction
    $settings['text_angle_minimum'] = -20;

    // maximum angle in degrees of letter. clockwise direction
    $settings['text_angle_maximum'] = 20;

    // position (in pixels) on the x axis where text starts
    $settings['text_x_start'] = 9;

    // the shortest distance in pixels letters can be from eachother (a very small value will cause overlapping)
    $settings['text_minimum_distance'] = 30;

    // the longest distance in pixels letters can be from eachother
    $settings['text_maximum_distance'] = 33;

    // images background color.  set each red, green, and blue to a 0-255 value
    $settings['image_bg_color'] = array("red" => 255, "green" => 255, "blue" => 255);

    // the color of the text
    $settings['text_color'] = array("red" => 128, "green" => 128, "blue" => 255);

    // draw a shadow for the text (gives a 3d raised bolder effect)
    $settings['shadow_text'] = false;

    // true for the ability to use transparent text, false for normal text
    $settings['use_transparent_text'] = true;

    // 0 to 100, 0 being completely opaque, 100 being completely transparent
    $settings['text_transparency_percentage'] = 15;

    // set to true to draw horizontal and vertical lines on the image
    $settings['draw_lines'] = TRUE;

    // color of the horizontal and vertical lines through the image
    $settings['line_color'] = array("red" => 204, "green" =>204, "blue" => 255);

    // distance in pixels the lines will be from eachother.
    $settings['line_distance'] = 12;

    // set to true to draw lines at 45 and -45 degree angles over the image  (makes x's)
    $settings['draw_angled_lines'] = TRUE;

    // set to true to draw the lines on top of the text, otherwise the text will be on the lines
    $settings['draw_lines_over_text'] = true;

    // age (in minutes) of files containing unused codes to be deleted
    $settings['prune_minimum_age'] = 15;

    // set this to a unique string, this prevents users guessing filenames and make data more secure
    $settings['hash_salt'] = "fg7hg3yg3fd90oi4i";

    xarModSetVar('formantibot', 'settings', serialize($settings));
    xarModSetVar('formantibot', 'savedCode', '');
    xarModSetVar('formantibot', 'SupportShortURLs', 1);

    // Register Security Mask
    xarRegisterMask('FormAntiBot-Admin', 'All','formantibot', 'All', 'All', 'ACCESS_ADMIN', 'Administrate Form Anti-bot');

      // Initialisation successful
    return TRUE;
}

/**
 * Removes the module from the current list of installed modules
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function formantibot_delete()
{
      //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();


      // Remove Masks and Instances
    xarRemoveMasks('formantibot');
    xarRemoveInstances('formantibot');
    xarModDelAllVars('formantibot');

      // Deletion successful
    return TRUE;

}

/**
 * Upgrades the module from a previous version to a new one
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function formantibot_upgrade($oldversion)
{
      // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
              // Code to upgrade from version 1.0 goes here
              // Register blocks
        case '1.1':
              // Code to upgrade from version 1.1 goes here
        case '2.5':
              // Code to upgrade from version 2.5 goes here
            break;
    }
    return TRUE;
}
?>
