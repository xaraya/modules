<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
function pubsub_user_usermenu($args)
{
    extract($args);
    // old usermenu approach
    xarVarFetch('action','str:1:',$action,'menu',XARVAR_NOT_REQUIRED);

    // set by user-account template in roles
    xarVarFetch('phase','notempty', $phase, '', XARVAR_NOT_REQUIRED);
    if (!empty($phase)) {
        $action = $phase;
    }

    switch($action) {
        case 'menu':
            return xarTplModule('pubsub','user','usermenu');
            break;

        case 'form':
        case 'list':
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Your Subscriptions')));
            $items = xarModAPIFunc('pubsub','user','getsubscriptions',
                                   array('userid' => xarUserGetVar('uid')));
            if (!isset($items)) return;
            // get the itemtype descriptions if available
            $todo = array();
            foreach ($items as $id => $item) {
                if (!empty($item['itemtype'])) {
                    $todo[$item['modname']] = 1;
                }
            }
            if (count($todo) > 0) {
                $itemtypes = array();
                foreach ($todo as $modname => $val) {
                    // Get the list of all item types for this module (if any)
                    $mytypes = xarModAPIFunc($modname,'user','getitemtypes',
                                             // don't throw an exception if this function doesn't exist
                                             array(), 0);
                    if (!empty($mytypes)) {
                        // save the label, title and url for each itemtype
                        $itemtypes[$modname] = $mytypes;
                    }
                }
                foreach ($items as $id => $item) {
                    if (!empty($item['itemtype']) && !empty($itemtypes[$item['modname']]) && !empty($itemtypes[$item['modname']][$item['itemtype']])) {
                        // set the itemtype to its corresponding label for display
                        $items[$id]['itemtype'] = $itemtypes[$item['modname']][$item['itemtype']]['label'];
                    }
                }
            }
            return xarTplModule('pubsub','user','usermenu',
                                array('action' => 'list',
                                      'items' => $items));
            break;

        case 'unsub':
            if (!xarVarFetch('pubsubid','int:1:',$pubsubid)) return;
            $items = xarModAPIFunc('pubsub','user','getsubscriptions',
                                   array('userid' => xarUserGetVar('uid')));
            if (!isset($items)) return;
            if (!isset($items[$pubsubid])) {
                 $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                              'pubsubid', 'usermenu', 'Pubsub');
                 xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                 new SystemException($msg));
                 return;
            }
            if (!xarModAPIFunc('pubsub',
                               'user',
                               'deluser',
                               array('pubsubid' => $pubsubid))) {
                 $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                              'deluser', 'usermenu', 'Pubsub');
                 xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                 new SystemException($msg));
                 return;
             }
             xarResponseRedirect(xarModURL('pubsub','user','usermenu',
                                           array('action' => 'list')));
             return true;

            break;

        default:
            break;
    }
    return xarML('unknown action');
}

?>
