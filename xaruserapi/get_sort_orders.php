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

*/
function gallery_userapi_get_sort_orders($args)
{
    $orders = array(
        'file_id'         => 'File ID'
        , 'name'          => 'Name'
        , 'created'       => 'Created'
        , 'modified'      => 'Modified'
        , 'display_order' => 'Manual'
    );

    return $orders;
}
?>