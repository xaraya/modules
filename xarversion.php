<?php
/**
 * Publications Initialization
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Publications Module
 * @link http://www.netspan.ch
 * @author M. Lutolf
 */
$modversion['name']         = 'publications';
$modversion['id']           = '30065';
$modversion['version']      = '2.0.0';
$modversion['displayname']  = xarML('Publications');
$modversion['description']  = xarML('Manage publications on a Xaraya site');
$modversion['credits']      = '';
$modversion['help']         = '';
$modversion['changelog']    = '';
$modversion['license']      = '';
$modversion['official']     = 1;
$modversion['author']       = 'M. Lutolf';
$modversion['contact']      = 'http://www.netspan.ch/';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency']   = array(147,30046,30012);
$modversion['dependencyinfo'] = array(
                                      147 => 'categories',
                                      30046 => 'listings',
                                      30012 => 'math',
                                      );
?>