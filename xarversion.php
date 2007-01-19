<?php
/**
 * JpGraph Module - wrapper for JpGraph
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
$modversion['name']           = 'jpgraph'; /* lowercase, no spaces or special chars */
$modversion['id']             = '819';
$modversion['version']        = '0.3.0'; /* three point version number */
$modversion['displayname']    = xarML('JpGraph');
$modversion['description']    = 'Wrapper for the JpGraph library';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 1;
$modversion['author']         = 'MichelV, Jason, Random';
$modversion['contact']        = 'http://www.xaraya.com/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Utility'; /* Complete|Utility|Miscellaneous|Authentication are available options for non-core */
$modversion['category']       = 'Miscellaneous';  /* Global|Content|User & Group|Miscellaneous available for non-core */

// this module requires the gd extension (for now)
$modversion['extensions']     = array('gd');
?>