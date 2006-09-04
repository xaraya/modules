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
/*

*/
function gallery_userapi_get_default_album_settings($args)
{
    $settings = array(
        'show_date'        => '0'
        , 'items_per_page' => xarModGetVar('gallery', 'items_per_page')
        , 'cols_per_page'  => xarModGetVar('gallery', 'cols_per_page')
        , 'file_width'     => '200px'
        , 'file_quality'   => 90
        , 'sort_order'     => xarModGetVar('gallery', 'sort')
        , 'sort_type'      => xarModGetVar('gallery', 'sort_order')
        , 'watermark_id'   => null
    );

    return $settings;
}
?>