<?php
/**
 * Version file
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 * @author Mark Lutolf
 * @author Jo Dalle Nogare
 * @link http://xaraya.com/index.php/release/30032.html
 */
$modversion['name']           = 'members';
$modversion['id']             = '30032';
$modversion['version']        = '1.0.0';
$modversion['displayname']    = xarML('Members');
$modversion['description']    = 'Manage members of an organization';
$modversion['credits']        = 'credits.txt';
$modversion['help']           = 'help.txt';
$modversion['changelog']      = 'changelog.txt';
$modversion['license']        = 'license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Marc Lutolf, Jo Dalle Nogare';
$modversion['contact']        = 'http://www.netspan.ch/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Content';
$modversion['category']       = 'Users & Groups';
$modversion['dependency']     = array();
$modversion['securityschema'] = array();
$modversion['dependency'] = array(147,30049,30012);
$modversion['dependencyinfo'] = array(
                                      147 => 'categories',
                                      30049  => 'query',
                                      30012 => 'math'
                                      );
?>