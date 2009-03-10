<?php
/**
 * Version information for Translations
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
*/

$modversion['name'] = 'Translations';
$modversion['id'] = '77';
$modversion['version'] = '0.2.0';
$modversion['displayname']    = 'Translations';
$modversion['description'] = 'Translations handling';
$modversion['official'] = 1;
$modversion['author'] = 'Marco Canini';
$modversion['contact'] = 'marco@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('translations::' => 'Locale string:Backend name:Module name');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';

if (false) {
xarML('Translations');
xarML('Translations handling');
}
?>