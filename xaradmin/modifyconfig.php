<?php
/**
 * Images module
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
function images_admin_modifyconfig()
{

    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    xarModAPILoad('images');
    // Generate a one-time authorisation code for this operation

    // get the current module variables for display
    // *********************************************
    // Global
    $data['gdextension'] = extension_loaded ('gd'); // True or false
    $data['libtype']['graphics-library']    = xarModGetVar('images', 'type.graphics-library'); // return gd
    $data['path']['derivative-store']       = xarModGetVar('images', 'path.derivative-store');
    $data['file']['cache-expire']           = xarModGetVar('images', 'file.cache-expire');
    if (!isset($data['file']['cache-expire'])) {
        xarModSetVar('images', 'file.cache-expire', 60);
    }
    $data['file']['imagemagick']            = xarModGetVar('images', 'file.imagemagick');
    if (!isset($data['file']['imagemagick'])) {
        xarModSetVar('images', 'file.imagemagick', '');
    }
    $data['authid']                         = xarSecGenAuthKey();
    $data['library']   = array('GD'          => _IMAGES_LIBRARY_GD,
                               'ImageMagick' => _IMAGES_LIBRARY_IMAGEMAGICK,
                               'NetPBM'      => _IMAGES_LIBRARY_NETPBM);

    $shortURLs = xarModGetVar('images', 'SupportShortURLs');

    $data['shortURLs'] = empty($shortURLs) ? 0 : 1;

    $data['basedirs'] = xarModAPIFunc('images','user','getbasedirs');
    $data['basedirs'][] = array('basedir' => '',
                                'baseurl' => '',
                                'filetypes' => '',
                                'recursive' => false);

    $hooks = xarModCallHooks('module', 'modifyconfig', 'images', array());

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}
?>
