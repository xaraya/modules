<?php

/**
 * File: $Id$
 *
 * Generate a html report based on the parameters
 *
 * @package modules
 * @copyright (C) 2004 Marcel van der Boom 
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @param object $args['connection'] which connection is used for the data for the report
 * @param string $args['xmlfile']    which file contains the report definition
 * @param string $args['action']     what action do we want to tell the report to do (search or output)
*/

function reports_user_html_report($args) 
{
    xarVarFetch('action','str::',$action);
    xarVarFetch('connection','int::',$connection);
    xarVarFetch('xmlfile','str::',$xmlfile);
    extract($args);

    // Use the report xml file as the template and let BL process that
    $data = array();
    $data['action']     = $action;
    $data['connection'] = $connection;
    $data['xmlfile']    = $xmlfile;
    $output = xarTplFile($xmlfile,$data);
    return $output;
}
?>    