<?php
/**
 *
 * Version information
 *
 */
    $modversion['name']           = 'foo';
    $modversion['id']             = '30000';
    $modversion['version']        = '1.0.0';
    $modversion['displayname']    = xarML('Foo');
    $modversion['description']    = xarML('A generic Web 2.0 module');
    $modversion['credits']        = 'credits.txt';
    $modversion['help']           = 'help.txt';
    $modversion['changelog']      = 'changelog.txt';
    $modversion['license']        = 'license.txt';
    $modversion['official']       = false;
    $modversion['author']         = 'Marc Lutolf';
    $modversion['contact']        = 'http://www.netspan.ch/';
    $modversion['admin']          = true;
    $modversion['user']           = true;
    $modversion['class']          = 'Complete';
    $modversion['category']       = 'Content';
    $modversion['securityschema'] = array();
    $modversion['dependency'] = array(30012, 30046);
    $modversion['dependencyinfo'] = array(
                                          30012 => 'math',
                                          30046 => 'listings',
                                          );
?>