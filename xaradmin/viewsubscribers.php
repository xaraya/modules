<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * View publications
 */
function ebulletin_admin_viewsubscribers()
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    // get HTTP vars
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'str:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'enum:name:pubname:email', $order, 'name', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sort', 'enum:ASC:DESC', $sort, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('stype', 'enum:reg:non', $stype, 'reg', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_col', 'enum:name:pubname:email', $filter_col, 'name', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_type', 'enum:starts:ends:contains:equals', $filter_type, 'contains', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter_text', 'str:0:', $filter_text, '', XARVAR_NOT_REQUIRED)) return;

    // get other vars
    if (empty($numitems) || !is_numeric($numitems)) {
        $numitems = xarSessionGetVar('ebulletin_subsperpage');
        if (empty($numitems)) {
            $numitems = xarModGetVar('ebulletin', 'admin_subsperpage');
        }
    } else {
        xarSessionSetVar('ebulletin_subsperpage', $numitems);
    }

    // assemble filter
    if (empty($filter_text)) {
        $filter = array('col' => '', 'type' => '', 'text' => '');
    } else {
        $filter = array('col' => $filter_col, 'type' => $filter_type, 'text' => $filter_text);
    }

    switch($stype) {

    // non-registered subscribers
    case 'non':

        // get subscribers
        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_non',
            array(
                'startnum' => $startnum,
                'numitems' => $numitems,
                'order'    => $order,
                'sort'     => $sort,
                'filter'   => $filter
            )
        );
        if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // get pager
        $pager = xarTplGetPager(
            $startnum,
            xarModAPIFunc('ebulletin', 'user', 'countsubscribers_non', array('filter' => $filter)),
            xarServerGetCurrentURL(array('startnum' => '%%')),
            $numitems
        );
        break;

    // registered subscribers
    case 'reg':
    default:

        // get subscribers
        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_reg',
            array(
                'startnum' => $startnum,
                'numitems' => $numitems,
                'order'    => $order,
                'sort'     => $sort,
                'filter'   => $filter
            )
        );
        if (empty($subscribers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // get pager
        $pager = xarTplGetPager(
            $startnum,
            xarModAPIFunc('ebulletin', 'user', 'countsubscribers_reg', array('filter' => $filter)),
            xarServerGetCurrentURL(array('startnum' => '%%')),
            $numitems
        );
    }

    // get remaining vars
    $showaddlink = xarSecurityCheck('AddeBulletin', 0) ? true : false;
    $nextsort = ($sort == 'ASC') ? 'DESC' : 'ASC';
    $sort_img = xarTplGetImage('s_'.strtolower($sort).'.png');
    $currenturl = xarServerGetCurrentURL();
    $authid = xarSecGenAuthKey();
    $filter_cols = array(
        'email'   => xarML('Email'),
        'name'    => xarML('Name'),
        'pubname' => xarML('Publication'),
    );
    $filter_types = array(
        'contains' => xarML('Contains'),
        'starts'   => xarML('Starts With'),
        'ends'     => xarML('Ends With'),
        'equals'   => xarML('Equals'),
    );

    // set template vars
    $data = array();
    $data['subscribers']  = $subscribers;
    $data['pager']        = $pager;
    $data['showaddlink']  = $showaddlink;
    $data['stype']        = $stype;
    $data['sort']         = $sort;
    $data['nextsort']     = $nextsort;
    $data['sort_img']     = $sort_img;
    $data['order']        = $order;
    $data['currenturl']   = $currenturl;
    $data['authid']       = $authid;
    $data['filter']       = $filter;
    $data['filter_cols']  = $filter_cols;
    $data['filter_types'] = $filter_types;
    $data['filter_text']  = $filter_text;
    $data['numitems']     = $numitems;

    return $data;

}

?>
