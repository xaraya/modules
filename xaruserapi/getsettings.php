<?php
/**
 * Get the predefined settings for image processing
 *
 * @returns array
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
