<?php
/**
 * File: $Id$
 * 
 * Xaraya NewsGroups
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage  NewsGroups Module
 * @author John Cox
*/

/**
 * initialise the NewsGroups module
 */
function newsgroups_init()
{

    xarModSetVar('newsgroups', 'server', 'news.xaraya.com');
    xarModSetVar('newsgroups', 'port', 119);
    xarModSetVar('newsgroups', 'numitems', 50);

    // Register Masks
    xarRegisterMask('ReadNewsGroups','All','newsgroups','All','All','ACCESS_READ');
    xarRegisterMask('AdminNewsGroups','All','newsgroups','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

function newsgroups_activate()
{
    return true;
}


function newsgroups_delete()
{
    return true;
}

?>