<?php
/**
 * Let user set which categorical events this user wants to be alerted about via email.
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 */

/**
 * Let user set which categorical events this user wants to be alerted about via email.
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 *
 * @author Jodie Razdrh/John Kevlin/David St.Clair/MichelV
 * @param $action
 * @param $cats
 * @todo
 */
function julian_user_alerts($args)
{
    extract ($args);

    if (!xarVarFetch('action',   'str',    $action,   '')) return;
    //Get the categories from the form.
    if (!xarVarFetch('cats',     'array',  $cats,     array(),     XARVAR_NOT_REQUIRED)) return;
    // TODO: Where is this good for? remove?
    if (!xarVarFetch('cal_date', 'int:0:8',$cal_date, date("Ymd"), XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('ReadJulian')) return;

    //store the categories the user has selected for alerts
    if (!strcmp($action,'update')) {
        // remove user var if empty
        if (empty($cats)) {
            xarModDelUserVar('julian', 'alerts');
        }
        $cats = serialize($cats);

        // Set the subscriptions
        if (xarModSetUserVar('julian', 'alerts', $cats) !== true) {
            return;
        }
        //redirect the user back to the previous page
        $back_link = xarSessionGetVar('lastview');
        xarResponseRedirect($back_link);
    }
    // View the form

    // Replace this with xarModGetUserVar
    //$useralerts = xarModAPIFunc('julian','user','getsubscriptions');
    $useralerts = array();
    $useralerts = xarModGetUserVar('julian','alerts');

    if (!empty($useralerts)) {
        $useralerts = unserialize($useralerts);
    } else {
        $useralerts = array();
    }

    // Get the categories of Julian
    $categories = xarModAPIFunc('julian','user','getcategories');
    foreach ($categories as $cid => $info) {
       $categories[$cid]['checked'] = in_array($cid, $useralerts) ? true  : false;
    }

    $data = array();
    $data['categories'] = $categories;
    // TODO: Where is this good for? remove?
    $data['cal_date']   = $cal_date;

    return $data;
}
?>
