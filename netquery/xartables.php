<?php
/**
 * File: $Id:
 */

function netquery_xartables()
{
        $xartable = array();

        $netqueryExecTable = xarDBGetSiteTablePrefix() . '_netquery_exec';
        $xartable['netquery_exec'] = $netqueryExecTable;
        $xartable['netquery_exec_column'] = array(
                'exec_id'       => $netqueryExecTable . '.exec_id',
                'exec_type'     => $netqueryExecTable . '.exec_type',
                'exec_local'    => $netqueryExecTable . '.exec_local',
                'exec_winsys'   => $netqueryExecTable . '.exec_winsys',
                'exec_remote'   => $netqueryExecTable . '.exec_remote',
                'exec_remote_t' => $netqueryExecTable . '.exec_remote_t');

        $netqueryWhoisTable = xarDBGetSiteTablePrefix() . '_netquery_whois';
        $xartable['netquery_whois'] = $netqueryWhoisTable;
        $xartable['netquery_whois_column'] = array(
                'whois_id'      => $netqueryWhoisTable . '.whois_id',
                'whois_ext'     => $netqueryWhoisTable . '.whois_ext',
                'whois_server'  => $netqueryWhoisTable . '.whois_server');

        return $xartable;
}
?>
