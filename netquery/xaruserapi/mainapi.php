<?php
/**
 * File: $Id:
 */

function netquery_userapi_mainapi()
{ 
    $settings = array(); 
    $settings['maintitle']  = xarVarPrepForDisplay(xarML('Netquery'));
    $settings['subtitle']   = xarVarPrepForDisplay(xarML('Click "Go" for any Netquery option'));
    $settings['domainlabel'] = xarVarPrepForDisplay(xarML('Whois Domain Name (No www.)'));
    $settings['extlabel'] = xarVarPrepForDisplay(xarML('Domain'));
    $settings['whoisiplabel'] = xarVarPrepForDisplay(xarML('Whois IP Address'));
    $settings['lookuplabel'] = xarVarPrepForDisplay(xarML('Lookup IP Address or Host Name'));
    $settings['diglabel'] = xarVarPrepForDisplay(xarML('Lookup (Dig) IP or Host Name'));
    $settings['pinglabel'] = xarVarPrepForDisplay(xarML('Ping IP Address or Host Name'));
    $settings['countlabel'] = xarVarPrepForDisplay(xarML('Count'));
    $settings['traceroutelabel'] = xarVarPrepForDisplay(xarML('Traceroute IP or Host Name'));
    $settings['serverlabel'] = xarVarPrepForDisplay(xarML('Query Port for Server'));
    $settings['portnumlabel'] = xarVarPrepForDisplay(xarML('Port'));
    return $settings;
} 
?>
