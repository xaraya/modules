<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/147.html
 * @author Johnny Robeson <johnny@xaraya.com>
 */
/**
 * Modify configuration for a module - hook for ('module','modifyconfig','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function smilies_admin_modifyconfighook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'modifyconfighook', 'smilies');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $itemtype = 0;
    if (isset($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    $data = array();
    
    // Have we set any vars for this itemtype yet? 
    // if not, get them from the default mod vars
    $data['image_folder'] = xarModGetVar($modname,'image_folder.' . $itemtype);
    if (!isset($data['image_folder'])) {
        $data['image_folder'] = xarModGetVar('smilies','image_folder');
        $data['skiptags'] = join(',',unserialize(xarModGetVar('smilies','skiptags')));
    } else {
        $data['image_folder'] = xarModGetVar($modname,'image_folder.' . $itemtype);
        $data['skiptags'] = join(',',unserialize(xarModGetVar($modname,'skiptags.' . $itemtype)));
    }
    
    $data['modname'] = $modname;

    return xarTplModule('smilies','admin','modifyconfighook', $data);
}
?>
