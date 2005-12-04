<?php
/**
 * create a new userpoints type
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * create a new userpoints type
 *
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @param $args['hits'] optional hit count for the item (not used in hook calls)
 * @returns int
 * @return hitcount item ID on success, void on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_admin_createptype($args)
{

    extract($args);

    if (!xarVarFetch('pmodule', 'str:1:', $pmodule, $pmodule,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'str:1:', $itemtype, $itemtype,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('paction', 'str:1:', $paction, $paction,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpoints', 'str:1:', $tpoints, $tpoints,XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $extrainfo = xarModAPIFunc('userpoints','admin','createptype',array('pmodule'=>$pmodule,'itemtype'=>$itemtype,'paction'=>$paction,'tpoints'=>$tpoints));
    xarResponseRedirect(xarModURL('userpoints', 'admin', 'pointstypes'));
    return true;
}

?>