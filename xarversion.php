<?php
/**
 * Encyclopedia Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @link http://xaraya.com/index.php/release/221.html
 * @author Marc Lutolf
 * @originalauthor Rebecca Smallwood, http://orodruin.sourceforge.net
 */

/* WARNING
 * Modification of this file is not supported.
 * Any modification is at your own risk and
 * may lead to inablity of the system to process
 * the file correctly, resulting in unexpected results.
 */
$modversion['name']           = 'encyclopedia';
$modversion['id']             = '221';
$modversion['version']        = '0.9';
$modversion['displayname']    = xarML('Encyclopedia');
$modversion['description']    = 'Small structured information repository';
$modversion['displaydescription'] = xarML('Small structured information repository');
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Marc Lutolf';
$modversion['contact']        = 'marcinmilan@xaraya.com';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
// This module depends on categories and comments
$modversion['dependency'] = array(147, 182);
?>