<?php
/**
 * File: $Id:
 *
 * Standard function to modify configuration parameters
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function bible_admin_modifyconfig()
{
    // security check
    if (!xarSecurityCheck('AdminBible')) return;

    // initialize template vars
    $data = xarModAPIFunc('bible', 'admin', 'menu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels and values for display
    $data['textslabel'] = xarML('Texts per page in admin screens');
    $data['searchverseslabel'] = xarML('Verses per page in keyword searches');
    $data['lookupverseslabel'] = xarML('Verses per page in passage lookups');
    $data['wordslabel'] = xarML('Words per page on dictionary index');
    $data['textdirlabel'] = xarML('Directory to find the texts in');
    $data['shorturlslabel'] = xarML('Enable short URLs');
    $data['altdblabel'] = xarML('Use alternate database for storage of texts?');
    $data['altdbtypelabel'] = xarML('Type');
    $data['altdbhostlabel'] = xarML('Host');
    $data['altdbnamelabel'] = xarML('Name');
    $data['altdbunamelabel'] = xarML('Username');
    $data['altdbpasslabel'] = xarML('Password');


    $data['textsvalue'] = xarModGetVar('bible', 'admin_textsperpage');
    $data['searchversesvalue'] = xarModGetVar('bible', 'user_searchversesperpage');
    $data['lookupversesvalue'] = xarModGetVar('bible', 'user_lookupversesperpage');
    $data['wordsvalue'] = xarModGetVar('bible', 'user_wordsperpage');

    $data['textdirvalue'] = xarModGetVar('bible', 'textdir');
    $data['shorturlschecked'] = xarModGetVar('bible', 'SupportShortURLs') ?
    'checked' : '';
    $data['altdbchecked'] = xarModGetVar('bible', 'altdb') ?
    'checked' : '';
    $data['altdbtypevalue'] = xarModGetVar('bible', 'altdbtype');
    $data['altdbhostvalue'] = xarModGetVar('bible', 'altdbhost');
    $data['altdbnamevalue'] = xarModGetVar('bible', 'altdbname');
    $data['altdbunamevalue'] = xarModGetVar('bible', 'altdbuname');
    $data['altdbpassvalue'] = xarModGetVar('bible', 'altdbpass');

    $data['updatebutton'] = xarML('Update Configuration');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'bible',
        array('module' => 'bible'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}

?>
