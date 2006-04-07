<?php
/**
* eBulletin - newsletter based on contents on your site
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
$modversion['name']           = 'eBulletin';
$modversion['id']             = '557';
$modversion['version']        = '1.2.0';
$modversion['displayname']    = xarML('eBulletin');
$modversion['description']    = 'Electronic bulletins based on site content';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Curtis Farnham';
$modversion['contact']        = 'curtis@farnham.com, http://xaraya.curtisfarnham.com/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array(
    'ebulletin::Block' => 'Block Title',
    'ebulletin::Publication' => 'Publication Name:publication ID'
);
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(771); // mail module
?>
