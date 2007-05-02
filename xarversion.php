<?php
/**
 * File: $Id$
 *
 * Ping initialization functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
 * @author John Cox
 */
$modversion['name']           = 'Ping';
$modversion['id']             = '810';
$modversion['version']        = '1.0.3';
$modversion['displayname']    = xarML('Ping');
$modversion['description']    = 'Pings various weblog tracking sites to notify them of recently added content';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 0;
$modversion['author']         = 'John Cox';
$modversion['contact']        = 'http://www.wyome.com';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['securityschema'] = array('Ping::Item' => 'Module ID:Item Type:Item ID');
$modversion['class']          = 'Utility';
$modversion['category']       = 'Miscellaneous';
$modversion['dependency']     = array(743,744);
?>