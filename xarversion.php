<?php
/**
 * File: $Id
 *
 * Articles System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage articles module
 * @author mikespub
*/

/* WARNING
 * Modification of this file is not supported.
 * Any modification is at your own risk and
 * may lead to inablity of the system to process
 * the file correctly, resulting in unexpected results.
 */
$modversion['name'] = 'articles';
$modversion['id'] = '151';
$modversion['version'] = '1.5.1';
$modversion['displayname'] = xarML('Articles');
$modversion['description'] = 'Display articles';
$modversion['credits'] = '';
$modversion['help'] = '';
$modversion['changelog'] = '';
$modversion['license'] = '';
$modversion['official'] = 1;
$modversion['author'] = 'mikespub';
$modversion['contact'] = 'http://www.xaraya.com/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
// TODO: improve how to specify & match against multiple categories !!
$modversion['securityschema'] = array('articles::Article' => 'Publication Type ID:Category ID:Author ID:Article ID');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
// this module depends on the categories module
$modversion['dependency'] = array(147);
?>
