<?php
/**
 * File: $Id:
 */

function netquery_xartables()
{
        $xartable = array();
        $netqueryWhoisTable = xarDBGetSiteTablePrefix() . '_netquery_whois';
        $xartable['netquery_whois'] = $netqueryWhoisTable;

        $xartable['netquery_whois_column'] = array(
                'whois_id'      => $netqueryWhoisTable . '.whois_id',
                'whois_ext'     => $netqueryWhoisTable . '.whois_ext',
                'whois_server'  => $netqueryWhoisTable . '.whois_server');

        return $xartable;
}
?>
