<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Query Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function query_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminQuery')) {
        $menulinks[] = Array('url'   => xarModURL('query',
                                                  'admin',
                                                  'overview'),
                              'title' => xarML('Short overview of the Query module'),
                              'label' => xarML('Overview'));
        $menulinks[] = Array('url'   => xarModURL('query',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the query configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}

?>