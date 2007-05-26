<?php
/**
    Site Search
 
    @copyright (C) 2003-2005 by Envision Net, Inc.
    @license GPL (http://www.gnu.org/licenses/gpl.html)
    @link http://www.envisionnet.net
    @author Brian McGilligan <brian@envisionnet.net>
 
    @package Xaraya eXtensible Management System
    @subpackage Site Search module
*/

$modversion['name'] = 'Site Search';
$modversion['id'] = '260';
$modversion['version'] = '1.0.1';
$modversion['description'] = 'Advanced search engined based on xapian.';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Brian McGilligan';
$modversion['contact'] = 'brian@envisionnet.net';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('sitesearch::All' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Global';

// this module depends on the categories module
$modversion['dependency'] = array();
?>
