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

    $data['itemsperpage'] = xarModVars::get('messages', 'itemsperpage');
    $data['buddylist'] = xarModVars::get('messages', 'buddylist');
    $data['limitsaved'] = xarModVars::get('messages', 'limitsaved');
    $data['limitinbox'] = xarModVars::get('messages', 'limitinbox');
    $data['limitoutbox'] = xarModVars::get('messages', 'limitout');
    $data['smilies'] = xarModVars::get('messages', 'smilies');
    $data['allow_html'] = xarModVars::get('messages', 'allow_html');
    $data['allow_bbcode'] = xarModVars::get('messages', 'allow_bbcode');
    $data['mailsubject'] = xarModVars::get('messages', 'mailsubject');
    $data['fromname'] = xarModVars::get('messages', 'fromname');
    $data['from'] = xarModVars::get('messages', 'from');
    $data['inboxurl'] = xarModVars::get('messages', 'inboxurl');
    $data['serverpath'] = xarModVars::get('messages', 'serverpath');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));

    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturls'] = xarModVars::get('messages','SupportShortURLs');

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