<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function gallery_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $id = 1;

    $itemtypes[$id] = array(
        'label' => 'Galleries',
        'title' => xarML('Display Albums'),
        'url'   => xarModURL('gallery','user','view',array('what' => 'albums'))
    );

    $id++;
    $itemtypes[$id] = array(
        'label' => 'Files',
        'title' => xarML('Display Files'),
        'url'   => xarModURL('gallery','user','view',array('what' => 'files'))
    );

    return $itemtypes;
}

?>
