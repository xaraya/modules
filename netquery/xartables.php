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
        $netqueryFlagsTable = xarDBGetSiteTablePrefix() . '_netquery_flags';
        $xartable['netquery_flags'] = $netqueryFlagsTable;
        $xartable['netquery_flags_column'] = array(
                'flag_id'  => $netqueryFlagsTable . '.flag_id',
                'flagnum'  => $netqueryFlagsTable . '.flagnum',
                'keyword'  => $netqueryFlagsTable . '.keyword',
                'fontclr'  => $netqueryFlagsTable . '.fontclr',
                'backclr'  => $netqueryFlagsTable . '.backclr',
                'lookup_1' => $netqueryFlagsTable . '.lookup_1',
                'lookup_2' => $netqueryFlagsTable . '.lookup_2');
        $netqueryWhoisTable = xarDBGetSiteTablePrefix() . '_netquery_whois';
        $xartable['netquery_whois'] = $netqueryWhoisTable;
        $xartable['netquery_whois_column'] = array(
                'whois_id'     => $netqueryWhoisTable . '.whois_id',
                'whois_ext'    => $netqueryWhoisTable . '.whois_ext',
                'whois_server' => $netqueryWhoisTable . '.whois_server');
        $netqueryPortsTable = xarDBGetSiteTablePrefix() . '_netquery_ports';
        $xartable['netquery_ports'] = $netqueryPortsTable;
        $xartable['netquery_ports_column'] = array(
                'port_id'  => $netqueryPortsTable . '.port_id',
                'port'     => $netqueryPortsTable . '.port',
                'protocol' => $netqueryPortsTable . '.protocol',
                'service'  => $netqueryPortsTable . '.service',
                'comment'  => $netqueryPortsTable . '.comment',
                'flag'     => $netqueryPortsTable . '.flag');
        $netqueryLGRequestTable = xarDBGetSiteTablePrefix() . '_netquery_lgrequest';
        $xartable['netquery_lgrequest'] = $netqueryLGRequestTable;
        $xartable['netquery_lgrequest_column'] = array(
                'request_id' => $netqueryLGRequestTable . '.request_id',
                'request'    => $netqueryLGRequestTable . '.request',
                'command'    => $netqueryLGRequestTable . '.command',
                'handler'    => $netqueryLGRequestTable . '.handler',
                'argc'       => $netqueryLGRequestTable . '.argc');
        $netqueryLGRouterTable = xarDBGetSiteTablePrefix() . '_netquery_lgrouter';
        $xartable['netquery_lgrouter'] = $netqueryLGRouterTable;
        $xartable['netquery_lgrouter_column'] = array(
                'router_id'       => $netqueryLGRouterTable . '.router_id',
                'router'          => $netqueryLGRouterTable . '.router',
                'address'         => $netqueryLGRouterTable . '.address',
                'username'        => $netqueryLGRouterTable . '.username',
                'password'        => $netqueryLGRouterTable . '.password',
                'zebra'           => $netqueryLGRouterTable . '.zebra',
                'zebra_port'      => $netqueryLGRouterTable . '.zebra_port',
                'zebra_password'  => $netqueryLGRouterTable . '.zebra_password',
                'ripd'            => $netqueryLGRouterTable . '.ripd',
                'ripd_port'       => $netqueryLGRouterTable . '.ripd_port',
                'ripd_password'   => $netqueryLGRouterTable . '.ripd_password',
                'ripngd'          => $netqueryLGRouterTable . '.ripngd',
                'ripngd_port'     => $netqueryLGRouterTable . '.ripngd_port',
                'ripngd_password' => $netqueryLGRouterTable . '.ripngd_password',
                'ospfd'           => $netqueryLGRouterTable . '.ospfd',
                'ospfd_port'      => $netqueryLGRouterTable . '.ospfd_port',
                'ospfd_password'  => $netqueryLGRouterTable . '.ospfd_password',
                'bgpd'            => $netqueryLGRouterTable . '.bgpd',
                'bgpd_port'       => $netqueryLGRouterTable . '.bgpd_port',
                'bgpd_password'   => $netqueryLGRouterTable . '.bgpd_password',
                'ospf6d'          => $netqueryLGRouterTable . '.ospf6d',
                'ospf6d_port'     => $netqueryLGRouterTable . '.ospf6d_port',
                'ospf6d_password' => $netqueryLGRouterTable . '.ospf6d_password',
                'use_argc'        => $netqueryLGRouterTable . '.use_argc');
        return $xartable;
}
?>