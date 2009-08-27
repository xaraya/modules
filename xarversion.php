<?php
/**
 * File: $Id: s.xarinit.php 1.22 03/01/26 20:03:00-05:00 John.Cox@mcnabb. $
 *
 * Categories System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage categories module
 * @author Jim McDonald, Flávio Botelho <nuncanada@xaraya.com>, mikespub <postnuke@mikespub.net>
*/
    $modversion['name'] = 'categories';
    $modversion['id'] = '147';
    $modversion['version'] = '2.5.0';
    $modversion['displayname']    = xarML('Categories');
    $modversion['description'] = 'Categorised data utility';
    $modversion['credits'] = 'xardocs/credits.txt';
    $modversion['help'] = 'xardocs/help.txt';
    $modversion['changelog'] = 'xardocs/changelog.txt';
    $modversion['license'] = 'xardocs/license.txt';
    $modversion['official'] = 1;
    $modversion['author'] = 'Jim McDonald';
    $modversion['contact'] = 'http://www.mcdee.net/';
    $modversion['admin'] = true;
    $modversion['user'] = false;
    $modversion['class'] = 'Utility';
    $modversion['category'] = 'Content';
    $modversion['securityschema'] = array('categories::category' => 'Category name::Category ID',
                                      'categories::item' => 'Category ID:Module ID:Item ID');
    $modversion['dependency'] = array(
                                      30049,
                                      );
    $modversion['dependencyinfo'] = array(
                                          30049  => 'query',
                                          );
?>