<?php
/**
 * Reports module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage reports
 * @link http://xaraya.com/index.php/release/4704.html
 * @author Marcel van der Boom <marcel@hsdev.com>
 */
/**
 * Table definitions
 *
 */
function reports_xartables()
{
    // Initialise table array
    $xartable = array();

    // What are the different prefixes?
    $prefix = xarDBGetSystemTablePrefix();
    $syscolprefix = "xar_";

    // Main reports tables
    $tab="reports";
    $systab =$prefix . "_" . $tab;

    // Column names
    $xartable[$tab] = $systab;
    $xartable[$tab.'_column'] = array(
                                     'id'          => $syscolprefix . 'id',
                                     'conn_id'     => $syscolprefix . 'conn_id',
                                     'name'        => $syscolprefix . 'name',
                                     'description' => $syscolprefix . 'description',
                                     'xmlfile'     => $syscolprefix . 'xmlfile'
                                     );

    $tab="report_connections";
    $systab = $prefix . "_" . $tab;
    // Column names
    $xartable[$tab] = $systab;
    $xartable[$tab.'_column'] = array(
                                     'id'          => $syscolprefix . 'id',
                                     'name'        => $syscolprefix . 'name',
                                     'description' => $syscolprefix . 'description',
                                     'server'      => $syscolprefix .'server',
                                     'type'        => $syscolprefix. 'type',
                                     'database'    => $syscolprefix . 'database',
                                     'user'        => $syscolprefix. 'user',
                                     'password'    => $syscolprefix . 'password');


    // Return table information
    return $xartable;
}

?>