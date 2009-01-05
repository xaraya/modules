<?php
/**
 * Predefined settings for image processing
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
 * Set the predefined settings for image processing
 *
 * @author mikespub
 * @param $args array containing the predefined settings for image processing
 * @return void
 */
function images_adminapi_setsettings($args)
{
    if (empty($args) || !is_array($args)) {
        $args = array();
    }

    xarModSetVar('images','phpthumb-settings',serialize($args));
}
?>