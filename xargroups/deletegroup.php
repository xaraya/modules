<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_groups_deletegroup()
{
    if (!xarSecConfirmAuthKey()) return;
    if (!xarVarFetch('gid',      'id', $gid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $gid = $objectid;
    }


    $item = xarModAPIFunc('xproject',
                          'groups',
                          'get',
                          array('gid' => $gid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if (!xarSecurityCheck('DeleteXProject', 1, 'Group', "All:All:All")) {// TODO: Add gid in here
        return;
    }
    if (empty($confirm)) {
        // Add menu?
        $data = array();

        /* Specify for which item you want confirmation */
        $data['gid'] = $gid;

        /* Add some other data you'll want to display in the template */
        $data['item'] = $item;
        $data['namevalue'] = xarVarPrepForDisplay($item['name']); 
        // More prep?

        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();
        // Get members for this group
        $members = xarModAPIFunc ('xproject', 'groups', 'getmembers', array('gid'=>$gid));
        if (count($members) > 0) {
            $data['members'] = $members;
            $data['nummembers'] = count($members);
        } else {
            $data['members'] ='';
            $data['nummembers'] = 0;
        }
        
        /* Return the template variables defined in this function */
        return $data;
    }
    if (xarModAPIFunc('xproject',
             'groups',
             'deletegroup', array('gid' => $gid))) {

        xarSessionSetVar('statusmsg', xarML('Group Deleted'));
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}
?>