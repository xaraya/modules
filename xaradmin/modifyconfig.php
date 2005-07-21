<?php

/*/
 * modify config -- get module variable values and passes them to the template
 *
 * @returns template variables
/*/
function shopping_admin_modifyconfig($args)
{
    // security check
    if (!xarSecurityCheck('AdminArticles')) return;

    // check for argument
    if (!xarVarFetch('phase', 'isset', $phase, 0, XARVAR_NOT_REQUIRED)) {return;}

    // extract args
    extract($args);

    // init template var
    $data = array();
    $data['phase'] = $phase;
    $data['authid'] = xarSecGenAuthKey();

    // set labels for buttons
    $data['resetlabel'] = xarML('Reset');
    $data['submitlabel'] = xarML('Update');

    switch ($phase) {
      // general & policy config
      default:
      case 0:
        // set link urls
        $data['mailurl'] = xarModURL('shopping','admin','modifyconfig',array('phase' => 1));
        // policy vars
        $data['spolicy'] = xarModGetVar('shopping', 'spolicy');
        $data['rpolicy'] = xarModGetVar('shopping', 'rpolicy');
        // general vars
        $data['itemsperpage'] = xarModGetVar('shopping', 'itemsperpage');
        $data['useritemsperpage'] = xarModGetVar('shopping', 'useritemsperpage');
        $data['displaylowstock'] = xarModGetVar('shopping', 'displaylowstock')?'checked':'';
        $data['lowstock'] = xarModGetVar('shopping', 'lowstock');
        $data['recosperpage'] = xarModGetVar('shopping', 'recosperpage');
        $data['userrecosperpage'] = xarModGetVar('shopping', 'userrecosperpage');
        $data['acceptpaypal'] = xarModGetVar('shopping', 'acceptpaypal')?'checked':'';
        $data['acceptcredit'] = xarModGetVar('shopping', 'acceptcredit')?'checked':'';
        $data['acceptbill'] = xarModGetVar('shopping', 'acceptbill')?'checked':'';
        $data['userecommendations'] = xarModGetVar('shopping', 'userecommendations')?'checked':'';


        // check for hooks
        $modhooks = xarModAPIFunc('modules', 'admin', 'gethookedmodules', array('hookModName' => 'categories'));
        if (isset($modhooks['shopping'])) {
          $data['usecategories'] = 'checked';
          if ((xarModGetVar('shopping', 'number_of_categories') > 0) && (xarModGetVar('shopping', 'mastercids') != "")) {
            $data['featurecat'] = xarModAPIFunc('shopping', 'admin', 'makefeatureselect');
          }
        } else {
          $data['usecategories'] = '';
        }
        $modhooks = xarModAPIFunc('modules', 'admin', 'gethookedmodules', array('hookModName' => 'comments'));
        if (isset($modhooks['shopping'])) {
          $data['usecomments'] = 'checked';
        } else {
          $data['usecomments'] = '';
        }
        $modhooks = xarModAPIFunc('modules', 'admin', 'gethookedmodules', array('hookModName' => 'ratings'));
        if (isset($modhooks['shopping'])) {
          $data['useratings'] = 'checked';
        } else {
          $data['useratings'] = '';
        }
        $modhooks = xarModAPIFunc('modules', 'admin', 'gethookedmodules', array('hookModName' => 'hitcount'));
        if (isset($modhooks['shopping'])) {
          $data['usehitcount'] = 'checked';
        } else {
          $data['usehitcount'] = '';
        }

        if (!empty($data['usecategories'])) {
          // call modifyconfig hooks with module
          $data['hooks'] = xarModCallHooks('module', 'modifyconfig', 'shopping',
                                           array('module' => 'shopping'));
        }
        break;

      // mail config
      case 1:
        // set link urls
        $data['genurl'] = xarModURL('shopping','admin','modifyconfig');
        // mail send vars
        $data['sendsubmit'] = xarModGetVar('shopping', 'sendsubmit')?'checked':'';
        $data['sendreview'] = xarModGetVar('shopping', 'sendreview')?'checked':'';
        $data['sendpaid'] = xarModGetVar('shopping', 'sendpaid')?'checked':'';
        $data['sendship'] = xarModGetVar('shopping', 'sendship')?'checked':'';
        $data['sendmod'] = xarModGetVar('shopping', 'sendmod')?'checked':'';
        $data['senddel'] = xarModGetVar('shopping', 'senddel')?'checked':'';
        $data['adminsendsubmit'] = xarModGetVar('shopping', 'adminsendsubmit')?'checked':'';
        $data['adminsendmod'] = xarModGetVar('shopping', 'adminsendmod')?'checked':'';
        $data['adminsenddel'] = xarModGetVar('shopping', 'adminsenddel')?'checked':'';
        // mail message vars
        $data['mailsubmittitle'] = xarModGetVar('shopping', 'mailsubmittitle');
        $data['mailsubmit'] = xarModGetVar('shopping', 'mailsubmit');
        $data['mailreviewtitle'] = xarModGetVar('shopping', 'mailreviewtitle');
        $data['mailreview'] = xarModGetVar('shopping', 'mailreview');
        $data['mailpaidtitle'] = xarModGetVar('shopping', 'mailpaidtitle');
        $data['mailpaid'] = xarModGetVar('shopping', 'mailpaid');
        $data['mailshiptitle'] = xarModGetVar('shopping', 'mailshiptitle');
        $data['mailship'] = xarModGetVar('shopping', 'mailship');
        $data['mailmodtitle'] = xarModGetVar('shopping', 'mailmodtitle');
        $data['mailmod'] = xarModGetVar('shopping', 'mailmod');
        $data['maildeltitle'] = xarModGetVar('shopping', 'maildeltitle');
        $data['maildel'] = xarModGetVar('shopping', 'maildel');
        $data['adminmailsubmittitle'] = xarModGetVar('shopping', 'adminmailsubmittitle');
        $data['adminmailsubmit'] = xarModGetVar('shopping', 'adminmailsubmit');
        $data['adminmailmodtitle'] = xarModGetVar('shopping', 'adminmailmodtitle');
        $data['adminmailmod'] = xarModGetVar('shopping', 'adminmailmod');
        $data['adminmaildeltitle'] = xarModGetVar('shopping', 'adminmaildeltitle');
        $data['adminmaildel'] = xarModGetVar('shopping', 'adminmaildel');
        break;
    }

    return $data;
}
?>
