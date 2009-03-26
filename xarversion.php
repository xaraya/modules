<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
$modversion['name'] = 'labAffiliate';
$modversion['id'] = '919';
$modversion['version'] = '1.2.1';
$modversion['displayname']    = xarML('labAffiliate');
$modversion['description'] = xarML('Affiliate program creation, migration and tracking.');
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'St.Ego';
$modversion['contact'] = 'http://www.miragelab.com/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('labAffiliate::' => 'Affiliate Name::Affiliate ID');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Global';
?>