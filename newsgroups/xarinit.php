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

    xarModSetVar('newsgroups', 'wildmat', 'xaraya.*,ddf.*');
    xarModSetVar('newsgroups', 'SupportShortURLs', 0);

    // Register Masks
    xarRegisterMask('ReadNewsGroups','All','newsgroups','All','All','ACCESS_READ');
    xarRegisterMask('SendNewsGroups','All','newsgroups','All','All','ACCESS_COMMENT');
    xarRegisterMask('AdminNewsGroups','All','newsgroups','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

function newsgroups_activate()
{
    return true;
}

/**
 * upgrade the newsgroups module from an old version
 */
function newsgroups_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            xarRegisterMask('SendNewsGroups','All','newsgroups','All','All','ACCESS_COMMENT');

        case '1.0.1':
            // Code to upgrade from version 1.0.1 goes here

        case '2.0.0':
            // Code to upgrade from version 2.0.0 goes here

    }
    return true;
}

function newsgroups_delete()
{
    return true;
}

?>
