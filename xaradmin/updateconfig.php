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
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function ievents_admin_updateconfig()
{
    // Get parameters

    if (!xarVarFetch('shorturl', 'checkbox', $shorturl, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdayofweek', 'int', $startdayofweek, 1, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('quanta', 'int', $quanta, 15, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxcats', 'int', $maxcats, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('days_new', 'int', $days_new, 5, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('days_updated', 'int', $days_updated, 3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_numitems', 'int', $default_numitems, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_numitems', 'int', $max_numitems, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_startdate', 'str:1:', $default_startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_enddate', 'str:1:', $default_enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_daterange', 'str:1:', $default_daterange, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('summary_max_words', 'int', $summary_max_words, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year_range_min', 'int', $year_range_min, -3, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year_range_max', 'int', $year_range_max, 5, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_group', 'str:1:', $default_group, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cal_subscribe_range', 'str:1:', $cal_subscribe_range, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cal_subscribe_numitems', 'int', $cal_subscribe_numitems, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('max_cat_depth', 'int', $max_cat_depth, 2, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_display_format', 'str:1:', $default_display_format, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('category_tree_search', 'checkbox', $category_tree_search, true, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if (!xarSecurityCheck('AdminIEventCal')) {
        return;
    }

    // Check arguments

	if ($shorturl !== true){
		$shorturl = false;
	}
    if (!is_numeric($startdayofweek) || $startdayofweek < 0 || $startdayofweek > 6) {
        $msg = xarML("Invalid value for config variable: startdayofweek");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($quanta) || !in_array($quanta, array(5,10,15,20,30))) {
        $msg = xarML("Invalid value for config variable: quanta");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($maxcats) || $maxcats <= 0) {
        $msg = xarML("Invalid value for config variable: maxcats");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($days_new) || $days_new <= 0) {
        $msg = xarML("Invalid value for config variable: days_new");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($days_updated) || $days_updated <= 0) {
        $msg = xarML("Invalid value for config variable: days_updated");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($default_numitems) || $default_numitems <= 0) {
        $msg = xarML("Invalid value for config variable: default_numitems");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($max_numitems) || $max_numitems <= 0) {
        $msg = xarML("Invalid value for config variable: max_numitems");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
	if ($default_startdate != 'now') {
		$default_startdate = 'now';
	}
	if ($default_enddate != '+6 months') {
		$default_enddate = '+6 months';
	}
	if ($default_daterange != 'next6months') {
		$default_daterange = 'next6months';
	}
    if (!is_numeric($summary_max_words) || $summary_max_words <= 0) {
        $msg = xarML("Invalid value for config variable: summary_max_words");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($year_range_min) || $year_range_min >= 0) {
        $msg = xarML("Invalid value for config variable: year_range_min");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($year_range_max) || $year_range_max <= 0) {
        $msg = xarML("Invalid value for config variable: year_range_max");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
	$groups = array(
        'none' => xarML('No group'),
        'day' => xarML('Daily'),
        'week' => xarML('Weekly'),
        'month' => xarML('Monthly'),
        'quarter' => xarML('Quarterly'),
        'year' => xarML('Annual')
	);
	if (!isset($groups[$default_group])) {
        $msg = xarML("Invalid value for config variable: default_group");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
	}
	if ($cal_subscribe_range != 'window4months') {
		$cal_subscribe_range = 'window4months';
	}
    if (!is_numeric($cal_subscribe_numitems) || $cal_subscribe_numitems <= 0) {
        $msg = xarML("Invalid value for config variable: cal_subscribe_numitems");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!is_numeric($max_cat_depth) || $max_cat_depth <= 0) {
        $msg = xarML("Invalid value for config variable: max_cat_depth");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!in_array($default_display_format, array('list','cal'))) {
        $msg = xarML("Invalid value for config variable: default_display_format");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
	if ($category_tree_search !== true){
		$category_tree_search = false;
	}

    // update the data

    xarmodSetVar('ievents','SupportShortURLs', $shorturl);
    xarmodSetVar('ievents','startdayofweek', $startdayofweek);
    xarmodSetVar('ievents','quanta', $quanta);
    xarmodSetVar('ievents','maxcats', $maxcats);
    xarmodSetVar('ievents','days_new', $days_new);
    xarmodSetVar('ievents','days_updated', $days_updated);
    xarmodSetVar('ievents','default_numitems', $default_numitems);
    xarmodSetVar('ievents','max_numitems', $max_numitems);
    xarmodSetVar('ievents','default_startdate', $default_startdate);
    xarmodSetVar('ievents','default_enddate', $default_enddate);
    xarmodSetVar('ievents','default_daterange', $default_daterange);
    xarmodSetVar('ievents','summary_max_words', $summary_max_words);
    xarmodSetVar('ievents','year_range_min', $year_range_min);
    xarmodSetVar('ievents','year_range_max', $year_range_max);
    xarmodSetVar('ievents','default_group', $default_group);
    xarmodSetVar('ievents','cal_subscribe_range', $cal_subscribe_range);
    xarmodSetVar('ievents','cal_subscribe_numitems', $cal_subscribe_numitems);
    xarmodSetVar('ievents','max_cat_depth', $max_cat_depth);
    xarmodSetVar('ievents','default_display_format', $default_display_format);
    xarmodSetVar('ievents','category_tree_search', $category_tree_search);

    xarResponseRedirect(xarModURL('ievents', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
