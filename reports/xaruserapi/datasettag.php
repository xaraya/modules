<?php


/**
 * File: $Id$
 *
 * Tag handler for <xar:reports-dataset/> tag.
 *
 * This is the handler for the 'render' method for the tag.
 * the tag is supposed to have tow attributes:
 * sql - sql statement
 * connection - a connection id 
 * vars - bind variables
 *
 * it returns an array with the dataset returned from connection id
 * with the specified sql statement
 * @package modules
 * @copyright (C) 2004 Marcel van der Boom 
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @todo forget about this, this sucks
*/

function reports_userapi_datasettag($args)
{
    extract($args);

    // Return a dynamic piece of code which gets an array of results for this
    // specific tag spec.
    $code ='';
    $code = '&$_bl_repres;'."\n";
    $code .= '$_bl_repconn =& xarModApiFunc(\'reports\',\'user\',\'getconnectionobject\',array(\'conn_id\' => $connection));'."\n";
    $code .= '$_bl_repconn->SetFetchMode(ADODB_FETCH_ASSOC);'."\n";
    $code .= '$_bl_repres  = $_bl_repconn->Execute('.$sql.','.$vars.')'."\n";
    return $code;
}
?>