<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
$modversion['name']         = 'messages';
$modversion['id']           = '6';
$modversion['version']      = '1.9.0';
$modversion['displayname']  = xarML('Messages');
$modversion['description']  = 'Xaraya Messages module';
$modversion['credits']      = 'docs/credits.txt';
$modversion['help']         = 'docs/help.txt';
$modversion['changelog']    = 'docs/changelog.txt';
$modversion['license']      = 'docs/license.txt';
$modversion['official']     = 1;
$modversion['author']       = 'XarayaGeek';
$modversion['contact']      = 'http://www.XarayaGeek.com/';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Admin';
$modversion['category']     = 'Content';
$modversion['dependency']   = array(14, 30012, 30049);
$modversion['dependencyinfo'] = array(
                                      30012 => 'math',
                                      30049  => 'query',
                                      );

?>
