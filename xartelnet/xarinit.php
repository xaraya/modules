<?php
/*
xartelnet init routine
pretty, ain't it?
author david marsel taylor
email support@phatcom.net
*/

function xartelnet_init()
{
    // Set up module variables
    xarModSetVar('xartelnet','host', 'www.google.com');
    xarModSetVar('xartelnet','port', '80');
    xarModSetVar('xartelnet','add_html_to_newline','1');
    xarModSetVar('xartelnet','timeout', '30');
    xarModSetVar('xartelnet','debug', '0');
    xarModSetVar('xartelnet','prompt', '</html>');
    xarRegisterMask('OverviewXarTelnet','All','xartelnet','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('AdminXarTelnet','All','xartelnet','All','All','ACCESS_ADMIN');
						
						
    return true;
}

function xartelnet_upgrade($oldversion)
{
    return true;
}

function xartelnet_delete()
{
    xarModDelVar('xartelnet','host');
    xarModDelVar('xartelnet','port');
    xarModDelVar('xartelnet','add_html_to_newline');
    xarModDelVar('xartelnet','timeout');
    xarModDelVar('xartelnet','debug');
    xarModDelVar('xartelnet','prompt');
    xarRemoveMasks('xartelnet');
    return true;
}
?>