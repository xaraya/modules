<?php
/**
 * File: $Id:
 * 
 * Generate the common menu configuration
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
 * generate the common menu configuration
 */
function bible_userapi_menu($args)
{
	extract($args);

	if (!isset($func)) $func = '';

    $menu = array();

    $menu['menutitle'] = xarML('Bible Search');
	$menu['func'] = $func;

    // make array of links
    $menu['menulinks'] = array();
    $menu['menulinks']['main'] = array(xarML('Quick Search'),
                                 xarModURL('bible', 'user', 'main'));
    $menu['menulinks']['search'] = array(xarML('Keyword Search'),
                                 xarModURL('bible', 'user', 'search'));
    $menu['menulinks']['lookup'] = array(xarML('Passage Lookup'),
                                 xarModURL('bible', 'user', 'lookup'));

    // strong's concordance
    $strongs = xarModAPIFunc('bible', 'user', 'getall',
               array('state' => 2, 'type' => 2, 'order' => 'sname', 'sort' => 'desc'));
    if (!empty($strongs)) {
        $menu['menulinks']['concordance'] = array(xarML('Concordance'),
                                    xarModURL('bible', 'user', 'concordance'));
    }

    $menu['menulinks']['library'] = array(xarML('Library'),
                                 xarModURL('bible', 'user', 'library'));
    $menu['menulinks']['help'] = array(xarML('Help'),
                                 xarModURL('bible', 'user', 'help'));


    // set status placeholder here so we don't have to do it elsewhere
    $menu['status'] = '';

    return $menu;
} 

?>
