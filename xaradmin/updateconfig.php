<?php
/*/
 * update config -- updates the mod vars
 * this function gets its values from modifyconfig
 *
 * @redirects you to the modify config page that sent you here
/*/
function shopping_admin_updateconfig()
{
    // security check
    if (!xarSecurityCheck('AdminArticles')) return;
    // confirm auth key
    if (!xarSecConfirmAuthKey()) return;
    // get the phase we are coming from
    if(!xarVarFetch('phase', 'isset', $phase, NULL, XARVAR_DONT_SET)) return;

    switch ($phase) {
      // from the general & policy config
      case 0:
        // get posts
        if(!xarVarFetch('spolicy', 'isset', $spolicy, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('rpolicy', 'isset', $rpolicy, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('itemsperpage', 'isset', $itemsperpage, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('useritemsperpage', 'isset', $useritemsperpage, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('displaylowstock', 'isset', $displaylowstock, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('lowstock', 'isset', $lowstock, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('recosperpage', 'isset', $recosperpage, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('userrecosperpage', 'isset', $userrecosperpage, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('acceptpaypal', 'isset', $acceptpaypal, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('acceptcredit', 'isset', $acceptcredit, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('acceptbill', 'isset', $acceptbill, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('userecommendations', 'isset', $userecommendations, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('usecategories', 'isset', $usecategories, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('featurecat', 'isset', $featurecat, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('usecomments', 'isset', $usecomments, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('useratings', 'isset', $useratings, '', XARVAR_DONT_SET)) return;
        if(!xarVarFetch('usehitcount', 'isset', $usehitcount, '', XARVAR_DONT_SET)) return;

        // validate some vars
        if ($itemsperpage < 1) {
          $itemsperpage = 1;
        }
        if ($recosperpage < 1) {
          $recosperpage = 1;
        }
        if ($useritemsperpage < 1) {
          $useritemsperpage = 1;
        }
        if ($userrecosperpage < 1) {
          $userrecosperpage = 1;
        }
        if ($lowstock < 1) {
          $lowstock = 1;
        }

        // set vars
        xarModSetVar('shopping', 'spolicy', $spolicy);
        xarModSetVar('shopping', 'rpolicy', $rpolicy);
        xarModSetVar('shopping', 'itemsperpage', $itemsperpage);
        xarModSetVar('shopping', 'useritemsperpage', $useritemsperpage);
        xarModSetVar('shopping', 'displaylowstock', $displaylowstock);
        xarModSetVar('shopping', 'lowstock', $lowstock);
        xarModSetVar('shopping', 'recosperpage', $recosperpage);
        xarModSetVar('shopping', 'userrecosperpage', $userrecosperpage);
        xarModSetVar('shopping', 'acceptpaypal', $acceptpaypal);
        xarModSetVar('shopping', 'acceptcredit', $acceptcredit);
        xarModSetVar('shopping', 'acceptbill', $acceptbill);
        xarModSetVar('shopping', 'userecommendations', $userecommendations);

        // change the status field for all items based on new lowstock setting
        if(!xarModAPIFunc('shopping', 'admin', 'changestatus')) return;

        // enable / disable hooks based on selections
        // cats hooks
        if ($usecategories) {
          if (xarModIsAvailable('categories')) {
            xarModAPIFunc('modules','admin','enablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'categories'));
            xarModSetVar('shopping', 'featurecat', $featurecat);
          }
        } else {
          if (xarModIsAvailable('categories')) {
            xarModAPIFunc('modules','admin','disablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'categories'));
          }
        }
        // comments hooks
        if ($usecomments) {
          if (xarModIsAvailable('comments')) {
            xarModAPIFunc('modules','admin','enablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'comments'));
          }
        } else {
          if (xarModIsAvailable('comments')) {
            xarModAPIFunc('modules','admin','disablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'comments'));
          }
        }
        // ratings hooks
        if ($useratings) {
          if (xarModIsAvailable('ratings')) {
            xarModAPIFunc('modules','admin','enablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'ratings'));
          }
        } else {
          if (xarModIsAvailable('ratings')) {
            xarModAPIFunc('modules','admin','disablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'ratings'));
          }
        }
        // hitcount hooks
        if ($usehitcount) {
          if (xarModIsAvailable('hitcount')) {
            xarModAPIFunc('modules','admin','enablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'hitcount'));
          }
        } else {
          if (xarModIsAvailable('hitcount')) {
            xarModAPIFunc('modules','admin','disablehooks',
                          array('callerModName' => 'shopping', 'hookModName' => 'hitcount'));
          }
        }
        // call updateconfig hooks
        xarModCallHooks('module','updateconfig','shopping',
                        array('module' => 'shopping'));
        break;

      // from the mail config
      case 1:
        // get posts
        if(!xarVarFetch('sendsubmit', 'isset', $sendsubmit, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('sendreview', 'isset', $sendreview, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('sendpaid', 'isset', $sendpaid, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('sendship', 'isset', $sendship, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('sendmod', 'isset', $sendmod, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('senddel', 'isset', $senddel, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminsendsubmit', 'isset', $adminsendsubmit, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminsendmod', 'isset', $adminsendmod, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminsenddel', 'isset', $adminsenddel, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailsubmittitle', 'isset', $mailsubmittitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailreviewtitle', 'isset', $mailreviewtitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailpaidtitle', 'isset', $mailpaidtitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailshiptitle', 'isset', $mailshiptitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailmodtitle', 'isset', $mailmodtitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('maildeltitle', 'isset', $maildeltitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmailsubmittitle', 'isset', $adminmailsubmittitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmailmodtitle', 'isset', $adminmailmodtitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmaildeltitle', 'isset', $adminmaildeltitle, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailsubmit', 'isset', $mailsubmit, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailreview', 'isset', $mailreview, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailpaid', 'isset', $mailpaid, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailship', 'isset', $mailship, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('mailmod', 'isset', $mailmod, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('maildel', 'isset', $maildel, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmailsubmit', 'isset', $adminmailsubmit, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmailmod', 'isset', $adminmailmod, NULL, XARVAR_DONT_SET)) return;
        if(!xarVarFetch('adminmaildel', 'isset', $adminmaildel, NULL, XARVAR_DONT_SET)) return;
        // set vars
        xarModSetVar('shopping', 'sendsubmit', $sendsubmit);
        xarModSetVar('shopping', 'sendreview', $sendreview);
        xarModSetVar('shopping', 'sendpaid', $sendpaid);
        xarModSetVar('shopping', 'sendship', $sendship);
        xarModSetVar('shopping', 'sendmod', $sendmod);
        xarModSetVar('shopping', 'senddel', $senddel);
        xarModSetVar('shopping', 'adminsendsubmit', $adminsendsubmit);
        xarModSetVar('shopping', 'adminsendmod', $adminsendmod);
        xarModSetVar('shopping', 'adminsenddel', $adminsenddel);
        xarModSetVar('shopping', 'mailsubmit', $mailsubmit);
        xarModSetVar('shopping', 'mailreview', $mailreview);
        xarModSetVar('shopping', 'mailpaid', $mailpaid);
        xarModSetVar('shopping', 'mailship', $mailship);
        xarModSetVar('shopping', 'mailmod', $mailmod);
        xarModSetVar('shopping', 'maildel', $maildel);
        xarModSetVar('shopping', 'adminmailsubmit', $adminmailsubmit);
        xarModSetVar('shopping', 'adminmailmod', $adminmailmod);
        xarModSetVar('shopping', 'adminmaildel', $adminmaildel);
        xarModSetVar('shopping', 'mailsubmittitle', $mailsubmittitle);
        xarModSetVar('shopping', 'mailreviewtitle', $mailreviewtitle);
        xarModSetVar('shopping', 'mailpaidtitle', $mailpaidtitle);
        xarModSetVar('shopping', 'mailshiptitle', $mailshiptitle);
        xarModSetVar('shopping', 'mailmodtitle', $mailmodtitle);
        xarModSetVar('shopping', 'maildeltitle', $maildeltitle);
        xarModSetVar('shopping', 'adminmailsubmittitle', $adminmailsubmittitle);
        xarModSetVar('shopping', 'adminmailmodtitle', $adminmailmodtitle);
        xarModSetVar('shopping', 'adminmaildeltitle', $adminmaildeltitle);
        break;
      default:
        return;
        break;
    }
 
    // redirect to the config page that called the update function
    return xarModFunc('shopping', 'admin', 'modifyconfig', array('phase' => $phase));
}
?>