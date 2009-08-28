<?php 

/**
 * Xarpages version information.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

$modversion['name']           = 'xarpages';
$modversion['id']             = '160';
$modversion['version']        = '2.0.1';
$modversion['displayname']    = 'Xarpages';
$modversion['description']    = 'Static pages administration';
$modversion['help']           = 'xardocs/privileges.txt';
$modversion['changelog']      = 'xardocs/news.txt';
$modversion['official']       = true;
$modversion['author']         = 'Jason Judge';
$modversion['contact']        = 'http://www.academe.co.uk/';
$modversion['admin']          = true;
$modversion['user']           = true;
$modversion['securityschema'] = array();
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
// 147 = 'categories'; 182 = 'Dynamic Data';
$modversion['dependency']     = array(147, 182);
if (false) {
    xarML('Xarpages');
    xarML('Static pages administration');
}
?>