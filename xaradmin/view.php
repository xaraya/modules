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
    // security check
    if (!xarSecurityCheck('EditBible')) return;

    // get HTTP vars
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, 0, XARVAR_NOT_REQUIRED)) return;

    // scan the texts directory and synchronize list
    xarModAPIFunc('bible', 'admin', 'scantextdir');

    // now many items should we show?
    if (empty($numitems)) {
        $numitems = xarModGetVar('bible', 'admin_textsperpage');
    }

    // get texts
    $texts = xarModAPIFunc('bible', 'user', 'getall', array(
        'startnum' => $startnum, 'numitems' => $numitems, 'state' => 'all')
    );

    // if no texts, make room for error to show
    if (empty($texts) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // make sure $texts is an array so we don't throw an error below
    if (empty($texts)) $texts = array();

    // we have texts, so create a pager
    $pager = xarTplGetPager(
        $startnum,
        xarModAPIFunc('bible', 'user', 'countitems', array('state' => 'all')),
        xarModURL('bible', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('bible', 'admin_itemsperpage')
    );

    // get list of text states
    $states = xarModAPIFunc('bible', 'admin', 'statelist');

    // generate list of options for each text
    foreach ($texts as $tid => $text) {

        // format certain vars while we're at it
        $texts[$tid]['state'] = $states[$text['state']];

        $links = array();
        switch($text['state']) {

        // Not Installed
        case 0:
            // Install
            $link = array(xarML('Install'));
            if (xarSecurityCheck('AddBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'new', array('tid' => $tid));
            }
            $links[] = $link;

            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'modify', array('tid' => $tid));
            }
            $links[] = $link;
            break;

        case 1: // Inactive
            // Activate
            $link = array(xarML('Activate'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'activate', array('tid' => $tid));
            }
            $links[] = $link;
            // Remove
            $link = array(xarML('Remove'));
            if (xarSecurityCheck('DeleteBible', 0, 'Item', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'delete', array('tid' => $tid));
            }
            $links[] = $link;
            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'modify', array('tid' => $tid));
            }
            $links[] = $link;
            break;

        case 2: // Active
            // Deactivate
            $link = array(xarML('Deactivate'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'deactivate', array('tid' => $tid));
            }
            $links[] = $link;
            // Edit
            $link = array(xarML('Edit'));
            if (xarSecurityCheck('EditBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'modify', array('tid' => $tid));
            }
            $links[] = $link;
            break;

        case 3: // New Version
            // Upgrade
            $link = array(xarML('Upgrade'));
            if (xarSecurityCheck('AddBible', 0, 'Text', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'upgrade', array('tid' => $tid));
            }
            $links[] = $link;

            // note: after pressing "Upgrade", status goes to Inactive

            break;

        case 4: // Missing

            // Remove
            $link = array(xarML('Remove'));
            if (xarSecurityCheck('DeleteBible', 0, 'Item', "$text[sname]:$tid")) {
                $link[] = xarModURL('bible', 'admin', 'delete', array('tid' => $tid));
            }
            $links[] = $link;
            break;
        }
        $texts[$tid]['links'] = $links;
    }

    // initialize template data
    $data = xarModAPIFunc('bible', 'admin', 'menu');

    // set template vars
    $data['pager'] = &$pager;
    $data['texts'] = &$texts;

    return $data;

}

?>
