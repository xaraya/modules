<?php
//$Id: xarversion.php,v 1.11 2005/06/24 09:31:47 michelv01 Exp $

$modversion['name']           = 'Julian';
$modversion['id']             = '319';
$modversion['version']        = '0.2.2';
$modversion['description']    = 'Event Calendar for keeping track of events';
$modversion['credits']        = 'xardocs/credits.txt';
$modversion['help']           = 'xardocs/help.txt';
$modversion['changelog']      = 'xardocs/changelog.txt';
$modversion['license']        = 'xardocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Michel V., John Kevlin, Jodie Razdrh, David St.Clair, Roger Raymond (xarCalendar)';
$modversion['contact']        = 'http://sourceforge.net/projects/julian/';
$modversion['admin']          = 1;
$modversion['user']           = 1;
$modversion['class']          = 'Complete';
$modversion['category']       = 'Content';
$modversion['dependency']     = array(4, 147); //Making overlib and categories module required
$modversion['securityschema'] = array('julian::event'     => 'Event Title::Event ID',
                                      'julian::category'  => 'Category Name::Category ID',
                                      'julian::topic'     => 'Topic Name::Topic ID',
                                      'julian::user'      => 'User Name::User ID',
                                      'julian::sharing'   => 'User Name::User ID',
                                      'julian::'          => '::');

?>
