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
    xarModSetVar('newsgroups', 'user', '');
    xarModSetVar('newsgroups', 'pass', '');
    xarModSetVar('newsgroups', 'numitems', 50);
    xarModSetVar('newsgroups', 'sortby', '');
    xarModSetVar('newsgroups', 'grouplist', '');
    xarModSetVar('newsgroups', 'listexpire', 3600);
    xarModSetVar('newsgroups', 'groupexpire', 900);

    xarModSetVar('newsgroups', 'wildmat', 'xaraya.*,ddf.*');
    xarModSetVar('newsgroups', 'SupportShortURLs', 0);

    // Register Masks
    xarRegisterMask('ReadNewsGroups','All','newsgroups','All','All','ACCESS_READ');
    xarRegisterMask('SendNewsGroups','All','newsgroups','All','All','ACCESS_COMMENT');
    xarRegisterMask('AdminNewsGroups','All','newsgroups','All','All','ACCESS_ADMIN');

    // Register Block types (this *should* happen at activation/deactivation)
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName'   => 'newsgroups',
                             'blockType' => 'latest'))) return;

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
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                               array('modName'   => 'newsgroups',
                                     'blockType' => 'latest'))) return;

        case '1.0.2':
            // Code to upgrade from version 1.0.2 goes here

        case '2.0.0':
            // Code to upgrade from version 2.0.0 goes here

    }
    return true;
}

function newsgroups_delete()
{
    // UnRegister blocks
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                       array('modName'   => 'newsgroups',
                             'blockType' => 'latest'))) return;

    return true;
}

?>
