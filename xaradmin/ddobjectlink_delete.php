<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
function subitems_admin_ddobjectlink_delete($args)
{
    extract($args);

    if(!xarVarFetch('objectid','int:1:',$objectid)) return;
    if(!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminSubitems')) return;

    if ($confirm) {
        if (!xarSecConfirmAuthKey()) return;

        if(!xarModAPIFunc('subitems','admin','ddobjectlink_delete',array('objectid' => $objectid))) return;

        xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));
        return true;
    }

    $data = xarModAPIFunc('subitems','admin','menu');
    $item = xarModAPIFunc('subitems','user','ddobjectlink_get',array('objectid' => $objectid));
    // nothing to see here
    if (empty($item)) return xarML('This item does not exist');

    // We retrieved by objectid, so there should only be one (cant have the same subobject linked twice)
    $data = array_merge($item[0],$data);
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                array('objectid' => $objectid));

    $data['label'] = xarML('Unknown');
    if (!empty($objectinfo)) $data['label'] = $objectinfo['label'];
    return $data;
}

?>
