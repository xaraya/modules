<?php
/*/
 * shopping/xarinit.php 1.00 July 25th 2003 jared_rich@excite.com
 *
 * Shopping Module Version File
 *
 * copyright (C) 2003 by Jared Rich
 * license GPL <http://www.gnu.org/licenses/gpl.html>
 * author: Jared Rich
/*/
$modversion['name']           = 'Shopping';
$modversion['id']             = '9119';
$modversion['version']        = '0.1.0';
$modversion['displayname']    = xarML('Shopping');
$modversion['description']    = 'E-Commerce Module';
$modversion['credits']        = 'xardocs/credits.doc';
$modversion['help']           = 'xardocs/help.doc';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'Jared Rich';
$modversion['contact']        = 'jared_rich@excite.com';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['securityschema'] = array('Shopping::Orders'          => 'Order ID::User ID::User Name',
                                      'Shopping::Items'           => 'Item ID::Item Name',
                                      'Shopping::Recommendations' => 'Recommendation ID::User ID',
                                      'Shopping::Blocks'          => 'Block Title');
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
?>
