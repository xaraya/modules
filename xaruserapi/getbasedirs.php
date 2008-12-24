<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * Get the configured base directories for server images
 *
 * @return array containing the base directories for server images
 */
function images_userapi_getbasedirs()
{
    $basedirs = xarModVars::get('images','basedirs');
    if (!empty($basedirs)) {
        $basedirs = unserialize($basedirs);
    }
    if (empty($basedirs)) {
        $basedirs = array();
        $basedirs[0] = array('basedir'   => 'themes',
                             'baseurl'   => 'themes',
                             'filetypes' => 'gif|jpg|png',
                             'recursive' => true);
        xarModVars::set('images','basedirs',serialize($basedirs));
    }

    return $basedirs;
}
?>
