<?php
/**
 * Set the predefined settings for image processing
 *
 * @param $args array containing the predefined settings for image processing
 * @returns void
 */
function images_adminapi_setsettings($args)
{
    if (empty($args) || !is_array($args)) {
        $args = array();
    }

    xarModSetVar('images','phpthumb-settings',serialize($args));
}
?>
