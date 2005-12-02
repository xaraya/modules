<?php
/**
 * File: $Id:
 *
 * Standard function to view items
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
 * view texts
 */
function bible_admin_view()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('EditBible')) return;

    $data = xarModAPIFunc('bible', 'admin', 'menu');

    $data['texts'] = array();

    // scan the texts directory and synchronize list
    xarModAPIFunc('bible', 'admin', 'scantextdir');

    // get texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('bible',
                                                            'admin_itemsperpage')));

    // Check for exceptions
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    if (empty($texts)) return $data;

    // we have texts, so create a pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('bible', 'user', 'countitems'),
        xarModURL('bible', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('bible', 'admin_itemsperpage'));

    // get list of text states
    $states = xarModAPIFunc('bible', 'admin', 'statelist');

    // generate list of options for each text, and replace state ID with state name
    foreach ($texts as $tid => $text) {

        $texts[$tid]['state'] = $states[$text['state']];

        $links = array();
        switch($text['state']) {

        case 0: // Not Installed
            // Install
            $link = array(xarML('Install'));
            if (xarSecurityCheck('AddBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'new',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'modify',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            break;

        case 1: // Inactive
            // Activate
            $link = array(xarML('Activate'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible',
                                    'admin',
                                    'activate',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            // Remove
            $link = array(xarML('Remove'));
            if (xarSecurityCheck('DeleteBible', 0, 'Item', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'delete',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'modify',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            break;

        case 2: // Active
            // Deactivate
            $link = array(xarML('Deactivate'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'deactivate',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'modify',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            break;

        case 3: // New Version
            // Upgrade
            $link = array(xarML('Upgrade'));
            if (xarSecurityCheck('AddBible', 0, 'Text', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'upgrade',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;

            // note: after pressing "Upgrade", status goes to Inactive

            break;

        case 4: // Missing

            // Remove
            $link = array(xarML('Remove'));
            if (xarSecurityCheck('DeleteBible', 0, 'Item', "$text[sname]:$text[tid]")) {
                $link[] = xarModURL('bible', 'admin', 'delete',
                                    array('tid' => $text['tid']));
            }
            $links[] = $link;
            break;
        }
        $texts[$tid]['links'] = $links;
    }
    $data['texts'] = $texts;

    // Return the template variables defined in this function
    return $data;

}

?>
