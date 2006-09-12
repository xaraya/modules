<?php
/**
 * Utility function to retrieve the list of item types of this module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * utility function to retrieve the list of item types of this module
 *
 * @return array containing the item types and their description
 */
function release_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Use the extension type as itemtypes
    $exttypes = xarModAPIFunc('release', 'user','getexttypes');

    foreach($exttypes as $etype=>$ename>){

        $itemtypevalue = $etype;
        $itemtypes[$etype] = array('label' => $ename,
                                   'title' => xarML('Extension Type'),
                                   'url' => xarModURL('release','user','display',array('type' => $etype)));
    }
    return $itemtypes;

}
?>