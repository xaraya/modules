<?php
/**
 * File: $Id$
 * 
 * CrossLink initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage xlink
 * @author mikespub
 */
$modversion['name']           = 'CrossLink';
$modversion['id']             = '186';
$modversion['version']        = '1.1.0';
$modversion['description']    = 'Establish relationship between module items and some independent identifier, e.g. for shortcut or foreign key';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'mikespub';
$modversion['contact']        = 'http://www.xaraya.com/';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('XLink::Item' => 'Module ID:Item Type:Item ID');
$modversion['class']          = 'Utility';
$modversion['category']       = 'Miscellaneous';
?>
