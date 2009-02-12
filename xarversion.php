<?php
/**
 * labAccounting
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage labAccounting
 * @link http://xaraya.com/index.php/release/706.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
$modversion['name'] = 'labAccounting';
$modversion['id'] = '706';
$modversion['version'] = '1.1.5';
$modversion['displayname']    = xarML('labAccounting');
$modversion['description'] = 'General ledger accounting using journals';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'St.Ego';
$modversion['contact'] = 'http://www.miragelab.com/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
//$modversion['securityschema'] = array('labaccounting:Journal' => 'Journal ID:Journal Type:Account Name', 'labaccounting:Ledger' => 'Ledger ID:Ledger Type:Account Name');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
$modversion['dependency'] = array(829,934); /* This module depends on the Dossier and jQuery modules */

?>
