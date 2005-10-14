<?php

/**
 * File: $Id$
 *
 * Module versioning information for Black List module
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 */

$modversion['name'] = 'BlackList';
$modversion['id'] = '519';
$modversion['version'] = '0.1.0';
$modversion['displayname']    = xarML('BlackList');
$modversion['description'] = 'Manage list of blocked domains. Useful for referrer and comments.';
$modversion['credits'] = 'xardocs/credits.txt';
$modversion['help'] = '';
$modversion['changelog'] = '';
$modversion['license'] = '';
$modversion['official'] = 1;
$modversion['author'] = 'Carl P. Corliss (aka Rabbitt)';
$modversion['contact'] = 'carl.corliss@xaraya.com';
$modversion['admin'] = 1;
$modversion['user'] = 0;
$modversion['securityschema'] = array('BlackList::' => '');
$modversion['class'] = 'Utility';
$modversion['category'] = 'Content';
$modversion['requires'] = array();
?>
