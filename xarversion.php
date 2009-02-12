<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2008 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
$modversion['name'] = 'dossier';
$modversion['id'] = '829';
$modversion['version'] = '1.4.0';
$modversion['displayname']    = xarML('Dossier');
$modversion['description'] = 'Contact Dossier and Relationship Manager';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = 'xardocs/help.txt';
$modversion['changelog'] = 'xardocs/changelog.txt';
$modversion['license'] = 'xardocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'St.Ego';
$modversion['contact'] = 'http://www.miragelab.com/';
$modversion['admin'] = 1;
$modversion['user'] = 1;
//$modversion['securityschema'] = array('dossier::item' => '::');
$modversion['class'] = 'Complete';
$modversion['category'] = 'Content';
$modversion['dependency']     = array(147,152,666,934); // uses Categories, Images, Uploads and jQuery
?>
