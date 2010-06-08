<?php
/**
 * Display module overview
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 14 Oct 2005
 */
function path_admin_test()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminPath',0)) return;

	$time1 = microtime();

	$somepath = 'apples/red';   
	$res = xarMod::apiFunc('path','user','path2action',array('path' =>$somepath));
	
	$time2 = microtime();

	$data['elapsed'] = $time2 - $time1;
	$data['res'] = xarMod::apiFunc('path','user','action2querystring',array('action' =>$res));

    return xarTplModule('path','admin','test', $data);
}

?>