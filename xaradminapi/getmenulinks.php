<?php
/**
 * Build administrative menu links
 *
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @return array containing the menulinks for the main menu items.
 */
function metaweblogapi_adminapi_getmenulinks()
{
    // Security Check
    $menulinks = array();
    //$menulinks[] = array('url'   => xarModURL('moveabletype','admin','modifyconfig'),
    //                         'title' => xarML('Modify the configuration of the moveabletype module'),
    //                         'label' => xarML('Modify Config'));

    return $menulinks;
}
?>