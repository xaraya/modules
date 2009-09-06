<?php
/**
 * Articles Initialization
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/* WARNING
 * Modification of this file is not supported.
 * Any modification is at your own risk and
 * may lead to inablity of the system to process
 * the file correctly, resulting in unexpected results.
 */
$modversion['name']         = 'articles';
$modversion['id']           = '151';
$modversion['version']      = '1.5.2';
$modversion['displayname']  = 'Articles';
$modversion['description']  = 'Display articles';
$modversion['credits']      = '';
$modversion['help']         = '';
$modversion['changelog']    = '';
$modversion['license']      = '';
$modversion['official']     = 1;
$modversion['author']       = 'mikespub';
$modversion['contact']      = 'http://www.xaraya.com/';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
// this module depends on the categories module
$modversion['dependency']   = array(147);

// Placeholder for translation skeleton, bug 6033
if (false) { 
  return xarML('Articles');
  return xarML('Display articles');
}
?>
