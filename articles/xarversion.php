<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
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
$modversion['name'] = 'articles';
$modversion['id'] = '151';
$modversion['version'] = '1.5.1';
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
