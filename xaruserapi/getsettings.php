<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Get the predefined settings for image processing
 *
 * @return array containing the predefined settings for image processing
 */
function images_userapi_getsettings()
{
    $settings = xarModGetVar('images','phpthumb-settings');
    if (empty($settings)) {
        $settings = array();
        $settings['JPEG 800 x 600'] = array('w' => 800,
                                            'h' => 600,
                                            'f' => 'jpg');
        xarModSetVar('images', 'phpthumb-settings', serialize($settings));
    } else {
        $settings = unserialize($settings);
    }

    return $settings;
}
?>
