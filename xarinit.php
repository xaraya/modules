<?php
/**
 * Xaraya NewsGroups
 * 
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
    // Default values
    xarModSetVar('newsgroups', 'server', 'news.xaraya.com');
    xarModSetVar('newsgroups', 'port', 119);
    xarModSetVar('newsgroups', 'user', '');
    xarModSetVar('newsgroups', 'pass', '');
    xarModSetVar('newsgroups', 'numitems', 30);
    xarModSetVar('newsgroups', 'sortby', '');
    xarModSetVar('newsgroups', 'grouplist', '');

    xarModSetVar('newsgroups', 'wildmat', 'xaraya.te*');
    xarModSetVar('newsgroups', 'SupportShortURLs', 0);

    // Register Masks
    xarRegisterMask('ReadNewsGroups','All','newsgroups','All','All','ACCESS_READ',
                    xarML('Read messages in newsgroups')
    );
    xarRegisterMask('AdminNewsGroups','All','newsgroups','All','All','ACCESS_ADMIN',
                    xarML('Administer the Newsgroups module')
    );

    // Initialisation calls upgrade for reducing duplicate code
    return newsgroups_upgrade('1.0.0');
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

        case '1.0.1':
            xarModSetVar('newsgroups', 'listexpire', 3600);
            xarModSetVar('newsgroups', 'groupexpire', 900);
            xarModSetVar('newsgroups', 'messageexpire', '');
            xarModSetVar('newsgroups', 'cachesize', 500000);

            // Register Block types (this *should* happen at activation/deactivation)
            if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                               array('modName'   => 'newsgroups',
                                     'blockType' => 'latest'))) return;

        case '1.0.2':
            // Code to upgrade from current version 1.0.3 goes here
            xarRegisterMask('SendNewsGroups','All','newsgroups','All','All','ACCESS_EDIT',
                            xarML('Post messages in newsgroups'));
            xarRegisterMask('DeleteNewsGroups','All','newsgroups','All','All','ACCESS_DELETE',
                            xarML('Delete messages in newsgroups')
            );
        case '1.0.3':

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
