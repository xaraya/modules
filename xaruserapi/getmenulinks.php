<?php
/**
 * File: $Id:
 *
 * Utility function to pass individual menu items to the main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function bible_userapi_getmenulinks()
{
    // initialize array of links
    $menulinks = array();

    // Quick Search
    $menulinks['main'] = array(
        'url' => xarModURL('bible', 'user', 'main'),
        'title' => xarML('Quick Search'),
        'label' => xarML('Quick Search')
    );

    // Keyword Search
    $menulinks['search'] = array(
        'url' => xarModURL('bible', 'user', 'search'),
        'title' => xarML('Keyword Search'),
        'label' => xarML('Keyword Search')
    );

    // Passage Lookup
    $menulinks['lookup'] = array(
        'url' => xarModURL('bible', 'user', 'lookup'),
        'title' => xarML('Passage Lookup'),
        'label' => xarML('Passage Lookup')
    );

    // Concordance
    if (false !== ($strongs = xarModAPIFunc(
        'bible', 'user', 'getall',
        array('state' => 2, 'type' => 2, 'order' => 'sname', 'sort' => 'desc')
    ))) {
        $menulinks['concordance'] = array(
            'url' => xarModURL('bible', 'user', 'concordance'),
            'title' => xarML('Concordance'),
            'label' => xarML('Concordance')
        );
    }

    // Library
    $menulinks['library'] = array(
        'url' => xarModURL('bible', 'user', 'library'),
        'title' => xarML('Library'),
        'label' => xarML('Library')
    );

    // Help
    $menulinks['help'] = array(
        'url' => xarModURL('bible', 'user', 'help'),
        'title' => xarML('Help'),
        'label' => xarML('Help')
    );

    return $menulinks;
}

?>
