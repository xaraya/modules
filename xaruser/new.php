<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
  Creates a new ticket

  @author Brian McGilligan
  @returns A new ticket form
*/
function helpdesk_user_new()
{
    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $data['allowusercheckstatus']  = xarModGetVar('helpdesk', 'User can check status');
    $data['allowusersubmitticket'] = xarModGetVar('helpdesk', 'User can Submit');
    $data['allowanonsubmitticket'] = xarModGetVar('helpdesk', 'Anonymous can Submit');
    $data['allowcloseonsubmit']    = xarModGetVar('helpdesk', 'AllowCloseOnSubmit');
    $data['readaccess']            = xarSecurityCheck('readhelpdesk', 0);
    $data['editaccess']            = xarSecurityCheck('edithelpdesk', 0);
    $data['adminaccess']           = xarSecurityCheck('adminhelpdesk', 0);
    $data['openedbydefaulttologgedin']   = xarModGetVar('helpdesk', 'OpenedByDefaultToLoggedIn');
    $data['assignedtodefaulttologgedin'] = xarModGetVar('helpdesk', 'AssignedToDefaultToLoggedIn');
    $data['userisloggedin']        = xarUserIsLoggedIn();
    $data['menu'] = xarModFunc('helpdesk', 'user', 'menu');
    $data['enabledimages'] = xarModGetVar('helpdesk', 'Enable Images');

    xarModAPILoad('helpdesk');

    // Maybe use a security check here
    if( !$data['allowanonsubmitticket'] && !xarUserIsLoggedIn() ){ return false; }

    if (!xarVarFetch('itemtype', 'int', $itemtype, 1, XARVAR_NOT_REQUIRED)) return;

    $data['username'] = xarUserGetVar('uname');
    $data['name']     = xarUserGetVar('name');
    $data['userid']   = xarUserGetVar('uid');

    if($data['userisloggedin'])
    {
        $data['email']    = xarUserGetVar('email');
        $data['phone']    = ""; //xarUserGetVar('phone');
    }
    else
    {
        $data['email']    = "";
        $data['phone']    = ""; //xarUserGetVar('phone');
    }

    /*
    * These funcs should be rethought once we get the rest working
    */
    $data['priority'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => PRIORITY_ITEMTYPE)
    );

    $data['sources'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => SOURCE_ITEMTYPE)
    );

    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets',
        array('itemtype' => STATUS_ITEMTYPE)
    );

    if( $data['editaccess'] )
    {
        $data['reps'] = xarModAPIFunc('helpdesk', 'user', 'gets',
            array('itemtype' => REPRESENTATIVE_ITEMTYPE)
        );
        $data['users'] = xarModAPIFunc('roles', 'user', 'getall');
    }

    /*
        Get the companies the current user has access to
    */
    $data['groups'] = xarModAPIFunc('helpdesk', 'user', 'get_companies',
        array(
            'parent' => 'Companies',
        )
    );

    $item = array();
    $item['module']   = 'helpdesk';
    $item['itemtype'] = $itemtype;
    $item['multiple'] = false;
    $item['returnurl'] = xarModURL('helpdesk', 'user', 'main');
    $hooks = xarModCallHooks('item', 'new', $itemtype, $item, 'helpdesk');
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['action']  = xarModURL('helpdesk', 'user', 'create');
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');

    return xarTplModule('helpdesk', 'user', 'new', $data);
}
?>
