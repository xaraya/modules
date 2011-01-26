<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */
/**
 * 
 */
function content_adminapi_getmodels()
{
	
	$paths = xarMod::apiFunc('dynamicdata','admin','browse', array(
		'basedir' =>  sys::code() . 'modules/content/', 
		'filetype' => 'xml'
	));

	foreach ($paths as $path) {
		if (!strstr($path, '-def.xml') && !strstr($path, '-dat.xml')) {
		$name = str_replace('xardata/', '', $path);
		$name = str_replace('.xml', '', $name);
		$val = $name;
		$name = str_replace('_', ' ', $name);
		$name = ucwords($name);
		$names[$val] = $name;
		}
	}

	return $names;

}