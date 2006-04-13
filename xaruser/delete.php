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
function helpdesk_user_delete($args)
{
    xarVarFetch('tid',        'int:1:',  $tid,        null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm',    'isset',   $confirm,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('itemtype',   'int',     $itemtype,   1,     XARVAR_NOT_REQUIRED);

    if( !xarModAPILoad('helpdesk', 'user') ) { return false; }
    if( !xarModAPILoad('security', 'user') ) { return false; }

    /*
        Security check to prevent un authorized users from deleting it
    */
    $has_security = xarModAPIFunc('security', 'user', 'check',
        array(
            'modid'     => xarModGetIDFromName('helpdesk'),
            'itemtype'  => $itemtype,
            'itemid'    => $tid,
            'level'     => SECURITY_WRITE
        )
    );
    if( !$has_security ){  return false; }

    if( !empty($confirm) )
    {
        $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');
        if ( $enforceauthkey && !xarSecConfirmAuthKey() ){ return false; }

        $item = array();
        $item['objectid'] = $tid;
        $item['itemtype'] = $itemtype;
        $item['module'] = 'helpdesk';
        xarModCallHooks('item', 'delete', $tid, $item);

        $result = xarModAPIFunc('helpdesk', 'user', 'delete', array('tid' => $tid));

        xarResponseRedirect(xarModURL('helpdesk', 'user', 'view'));
    }

    $data = array();
    $data['tid'] = $tid;

    return $data;
}
?>