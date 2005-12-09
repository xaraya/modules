<?php
/**
 * Xaraya/SHIM login manager module.  Migrates Xaraya logins
 * and permissions to the SHIM (SISSI release and later).
 * Based on DPLink by Adam Donnison <adam@saki.com.au>;
 * which is based on pnDProject.
 * @link http://www.dotproject.com/
 */
/**
 * Xaraya wrapper module for DotProject
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
$modversion['name']         = 'xardplink';
$modversion['id']           = '591';
$modversion['version']      = '0.8.0';
$modversion['displayname']  = xarML('XarDPLink');
$modversion['description']  = 'Xaraya to dotProject login manager';
$modversion['credits']      = 'xardocs/credits.txt';
$modversion['help']         = 'xardocs/readme.txt';
$modversion['changelog']    = 'xardocs/changelog.txt';
$modversion['license']      = 'GPL';
$modversion['official']     = 0;
$modversion['author']       = 'MichelV';
$modversion['contact']      = 'michelv@xaraya.com';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Utility'; /* Complete|Utility|Miscellaneous available for non-core */
$modversion['category']     = 'Global';  /* Global|Content|User & Group|Miscellaneous available for non-core */
?>
