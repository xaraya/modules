<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Manage the tables in publications
 *
 * @return array with the tables used in publications
 */
function publications_xartables()
{
    $xartable['publications'] = xarDB::getPrefix() . '_publications';
    $xartable['publications_types'] = xarDB::getPrefix() . '_publications_types';

    // Return table information
    return $xartable;
}

?>
