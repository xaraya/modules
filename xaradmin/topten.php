<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_topten($args)
{
    extract($args);

    if (!xarVarFetch('top10view', 'int', $top10view, $top10view, XARVAR_NOT_REQUIRED)) return;

    $uid = xarUserGetVar('uid');

    $data = array();

    $items = array();

    switch($top10view) {
        case 2: // Top 10 Clients
            $contacts = xarModAPIFunc('xproject', 'user', 'top10clients',
                                    array('memberid' => $memberid));
            if (!isset($contacts) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

            $data['heading'] = "Top 10 Clients";

            $data['field1name'] = "Client";
            $data['field2name'] = "Amount";
            $data['field3name'] = "Stage";

            foreach($contacts as $contactinfo) {
                $items[] = array('field1value' => $contactinfo['displayName'],
                                'field2value' => $contactinfo['company'],
                                'field3value' => $contactinfo['fname']);
            }

            break;

        case 3: // Top 10 Current Projects


            break;

        case 4: // Top 10 Active Projects


            break;

        case 5: // Top 10 New Projects


            break;

        case 1: // Top 10 Leads
        default:


    }

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['items'] = $items;

    return $data;
}

?>