<?php
/**
 * IEvents module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage IEvents Module
 * @link http://xaraya.com/index.php/release/986.html
 * @author Jason Judge
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function ievents_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminIEventCal')) {
        return;
    }

    $data = array();

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    $data['shorturl'] = xarModGetVar('ievents', 'SupportShortURLs');
    $data['startdayofweek'] = xarModGetVar('ievents', 'startdayofweek');
    $data['quanta'] = xarModGetVar('ievents', 'quanta');
    $data['maxcats'] = xarModGetVar('ievents', 'maxcats');
    $data['days_new'] = xarModGetVar('ievents', 'days_new');
    $data['days_updated'] = xarModGetVar('ievents', 'days_updated');
    $data['default_numitems'] = xarModGetVar('ievents', 'default_numitems');
    $data['max_numitems'] = xarModGetVar('ievents', 'max_numitems');
    $data['default_startdate'] = xarModGetVar('ievents', 'default_startdate');
    $data['default_enddate'] = xarModGetVar('ievents', 'default_enddate');
    $data['default_daterange'] = xarModGetVar('ievents', 'default_daterange');
    $data['summary_max_words'] = xarModGetVar('ievents', 'summary_max_words');
    $data['year_range_min'] = xarModGetVar('ievents', 'year_range_min');
    $data['year_range_max'] = xarModGetVar('ievents', 'year_range_max');
    $data['default_group'] = xarModGetVar('ievents', 'default_group');
    $data['cal_subscribe_range'] = xarModGetVar('ievents', 'cal_subscribe_range');
    $data['cal_subscribe_numitems'] = xarModGetVar('ievents', 'cal_subscribe_numitems');
    $data['max_cat_depth'] = xarModGetVar('ievents', 'max_cat_depth');
    $data['default_display_format'] = xarModGetVar('ievents', 'default_display_format');
    $data['category_tree_search'] = xarModGetVar('ievents', 'category_tree_search');

	// get some other data from ievents_userapi_params()
	$params = xarModAPIFunc('ievents','user','params', array('knames' => 'grouplist,quantas,display_formats,locale'));
	
	extract($params);

    $data['quantas'] = $quantas;
    $data['weekdays'] = $locale['days']['long'];
    $data['groups'] = $grouplist;
    $data['display_formats'] = $display_formats;

    return $data;
}

?>
