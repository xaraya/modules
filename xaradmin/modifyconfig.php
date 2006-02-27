<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function messages_admin_modifyconfig()
{
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('messages','admin','menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
     if(!xarSecurityCheck('AdminMessages')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels and values for display

    $data['itemsperpage'] = xarModGetVar('messages', 'itemsperpage');
    $data['buddylist'] = xarModGetVar('messages', 'buddylist');
    $data['limitsaved'] = xarModGetVar('messages', 'limitsaved');
    $data['limitinbox'] = xarModGetVar('messages', 'limitinbox');
    $data['limitoutbox'] = xarModGetVar('messages', 'limitout');
    $data['smilies'] = xarModGetVar('messages', 'smilies');
    $data['allow_html'] = xarModGetVar('messages', 'allow_html');
    $data['allow_bbcode'] = xarModGetVar('messages', 'allow_bbcode');
    $data['mailsubject'] = xarModGetVar('messages', 'mailsubject');
    $data['fromname'] = xarModGetVar('messages', 'fromname');
    $data['from'] = xarModGetVar('messages', 'from');
    $data['inboxurl'] = xarModGetVar('messages', 'inboxurl');
    $data['serverpath'] = xarModGetVar('messages', 'serverpath');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturls'] = xarModGetVar('messages','SupportShortURLs');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'messages',
                            array('module' => 'messages'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>