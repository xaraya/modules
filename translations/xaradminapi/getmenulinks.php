<?php
/**
 * File: $Id$
 *
 * Pass admin links to the admin menu
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Pass individual menu items to the admin menu
 *
 * @return array containing the menulinks for the admin menu items.
 */
function translations_adminapi_getmenulinks()
{
    $menuLinks[] = array('url'   => xarModURL('translations','admin','start'),
                         'title' => xarML('Work on translations'),
                         'label' => xarML('Translate'));
    $menuLinks[] = array('url'   => xarModURL('translations','admin','generate_trans_info'),
                         'title' => xarML('Package a finished translation'),
                         'label' => xarML('Package'));
    $menuLinks[] = array('url'   => xarModURL('translations','admin','show_status'),
                         'title' => xarML('Show the progress status of the locale currently being translated'),
                         'label' => xarML('Progress report'));

    $menuLinks[] = array('url'   => xarModURL('translations','admin','modifyconfig'),
                         'title' => xarML('Modify translation configuration Values'),
                         'label' => xarML('Modify Config'));

    return $menuLinks;
}
?>