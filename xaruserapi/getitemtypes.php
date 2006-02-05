<?php
/**
 * Utility function to retrieve the list of item types of this module
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * utility function to retrieve the list of item types of this module
 *
 * @author MichelV
 * @returns array
 * @return array containing the item types and their description
 */
function courses_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Let's see if this is usefull
    $itemtypes[1003] = array('label' => xarVarPrepForDisplay(xarML('Course levels')),
                          'title' => xarVarPrepForDisplay(xarML('Courselevels')),
                          'url'   => xarModURL('courses','admin','view', array('itemtype' => 1003))
                          );

    $itemtypes[1004] = array('label' => xarVarPrepForDisplay(xarML('Student stati')),
                          'title' => xarVarPrepForDisplay(xarML('Studentstati')),
                          'url'   => xarModURL('courses','admin','view', array('itemtype' => 1004))
                          );
    $itemtypes[1005] = array('label' => xarVarPrepForDisplay(xarML('Course years')),
                          'title' => xarVarPrepForDisplay(xarML('Courseyears')),
                          'url'   => xarModURL('courses','admin','view', array('itemtype' => 1005))
                          );
    $types = xarModAPIFunc('courses',
                            'user',
                            'getall_coursetypes');

    foreach($types as $type){
        $itemtypevalue = $type['tid'];
        $itemtypes[$itemtypevalue] = array('label' => $type['coursetype'],
                                           'title' => xarML('Course'),
                                           'url' => xarModURL('courses','user','displaytype',array('tid' => $type['tid'])));
    }
    return $itemtypes;

}

?>
