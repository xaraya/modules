<?php

function pubsub_user_usermenu()
{
    xarVarFetch('action','str:1:',$action,'menu',XARVAR_NOT_REQUIRED);

    switch($action) {
        case 'menu':
            return xarTplModule('pubsub','user','usermenu');
            break;

        case 'list':
            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Your Subscriptions')));
            $items = xarModAPIFunc('pubsub','user','getsubscriptions',
                                   array('userid' => xarUserGetVar('uid')));
            if (!isset($items)) return;
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
                 xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                 new SystemException($msg));
                 return;
            }
            if (!xarModAPIFunc('pubsub',
                               'user',
                               'deluser',
                               array('pubsubid' => $pubsubid))) {
                 $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
                              'deluser', 'usermenu', 'Pubsub');
                 xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
