<?php
/**
 * File: $Id: xarversion.php 1.11 04/08/28 22:44:30+02:00 marc@marclaptop. $
 *
 * Xaraya CPShop initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarcpshop
 * @author jojodee
 */
$modversion['name']           = 'xarcpshop';
$modversion['id']             = '199';
$modversion['version']        = '0.0.1';
$modversion['displayname']    = xarML('Xaraya Cafe Press');
$modversion['description']    = 'Simple integration of Cafe Press Shops in Xaraya';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'jojodee';
$modversion['contact']        = 'http://xaraya.athomeandabout.com/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array('cpshop::name' => 'name::cpshopitem:shopid');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
?>
