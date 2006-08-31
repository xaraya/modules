<?php
/**
 * Keywords initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * initialise the keywords module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function keywords_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $keywordstable = $xartable['keywords'];
    $restrkeywordstable = $xartable['keywords_restr'];

    xarDBLoadTableMaintenanceAPI();
    $query = xarDBCreateTable($xartable['keywords'],
                             array('xar_id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'xar_keyword'    => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
// TODO: replace with unique id
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemtype'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemid'     => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                  ));

    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // allow several entries for the same keyword here
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_keywords_key',
        'fields'    => array('xar_keyword'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($keywordstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // allow several keywords for the same module item
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_keywords_combo',
        'fields'    => array('xar_moduleid','xar_itemtype','xar_itemid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($keywordstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;


    $query = xarDBCreateTable($xartable['keywords_restr'],
                             array('xar_id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'xar_keyword'    => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
// TODO: replace with unique id
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                  'xar_itemtype'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0')
                                  ));

    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result = &$dbconn->Execute($query);
    if (!$result) return;

     // avoid duplicate keywords for the same module item
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_keywords',
        'fields'    => array('xar_keyword','xar_moduleid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($restrkeywordstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    xarModSetVar('keywords', 'SupportShortURLs', 1);
    xarModSetVar('keywords', 'displaycolumns', 2);
    xarModSetVar('keywords', 'delimiters', ';,');
    xarModSetVar('keywords', 'restricted', 0);
    xarModSetVar('keywords', 'useitemtype', 0);
    xarModSetVar('keywords', 'default', 'xaraya');

    if (!xarModRegisterHook('item', 'new', 'GUI',
                           'keywords', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'create', 'API',
                           'keywords', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'modify', 'GUI',
                           'keywords', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'update', 'API',
                           'keywords', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'delete', 'API',
                           'keywords', 'admin', 'deletehook')) {
        return false;
    }
    if (!xarModRegisterHook('module', 'remove', 'API',
                           'keywords', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModRegisterHook('item', 'display', 'GUI',
                           'keywords', 'user', 'displayhook')) {
        return false;
    }

    if (!xarModRegisterHook('item', 'search', 'GUI',
                           'keywords', 'user', 'search')) {
        return;
    }

/* // TODO: show items you created/edited someday ?
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'keywords', 'user', 'usermenu')) {
        return false;
    }
*/
    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'keywords',
                             'blockType'=> 'keywordsarticles'))) return;
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'keywords',
                             'blockType'=> 'keywordscategories'))) return;

    $instances = array(
                       array('header' => 'external', // this keyword indicates an external "wizard"
                             'query'  => xarModURL('keywords', 'admin', 'privileges'),
                             'limit'  => 0
                            )
                    );
    xarDefineInstance('keywords', 'Item', $instances);

// TODO: tweak this - allow viewing keywords of "your own items" someday ?
// MichelV: Why not have an add privilege in here? Admin to add keywords seems way overdone
    xarRegisterMask('ReadKeywords', 'All', 'keywords', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AddKeywords', 'All', 'keywords', 'Item', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('AdminKeywords', 'All', 'keywords', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    // create the dynamic object that will represent our items
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/keywords/keywords.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('keywords','objectid',$objectid);

    // Initialisation successful
    return true;
}

/**
 * upgrade the keywords module from an old version
 * This function can be called multiple times
 * @return bool
 */
function keywords_upgrade($oldversion)
{
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
        case '1.0.0':

                xarModSetVar('keywords', 'restricted', 0);
                xarModSetVar('keywords', 'default', 'xaraya');

                $dbconn =& xarDBGetConn();
                $xartable =& xarDBGetTables();
                xarDBLoadTableMaintenanceAPI();
                $query = xarDBCreateTable($xartable['keywords_restr'],
                             array('xar_id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'xar_keyword'    => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0')
                                  ));

                if (empty($query)) return; // throw back

                // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
                $result = &$dbconn->Execute($query);
                if (!$result) return;

                if (!xarModRegisterHook('item', 'search', 'GUI',
                        'keywords', 'user', 'search')) {
                    return;
                }

        case '1.0.2':
            //Alter table restr to add itemtype
            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            // Add column 'xar_itemtype' to table
             $query = xarDBAlterTable($xartable['keywords_restr'],
                                     array('command' => 'add',
                                           'field' => 'xar_itemtype',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => '0'));
            $result = & $dbconn->Execute($query);
            if (!$result) return;

            // Register blocks
            if (!xarModAPIFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName'  => 'keywords',
                            'blockType'=> 'keywordsarticles'))) return;
            if (!xarModAPIFunc('blocks',
                    'admin',
                    'register_block_type',
                    array('modName'  => 'keywords',
                            'blockType'=> 'keywordscategories'))) return;

        case '1.0.3':
            xarModSetVar('keywords', 'useitemtype', 0);
        
        case '1.0.4':
            xarRegisterMask('AddKeywords', 'All', 'keywords', 'Item', 'All:All:All', 'ACCESS_COMMENT');

        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * delete the keywords module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @return bool true on success
 */
function keywords_delete()
{
    // delete the dynamic object and its properties
    $objectid = xarModGetVar('keywords','objectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',
                      array('objectid' => $objectid));
        xarModDelVar('keywords','objectid');
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['keywords']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['keywords_restr']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'new', 'GUI',
                           'keywords', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'create', 'API',
                           'keywords', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'modify', 'GUI',
                           'keywords', 'admin', 'modifyhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'update', 'API',
                           'keywords', 'admin', 'updatehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'delete', 'API',
                           'keywords', 'admin', 'deletehook')) {
        return false;
    }

    if (!xarModUnregisterHook('item', 'search', 'GUI',
                              'keywords', 'user', 'search')) {
        return;
    }


    // when a whole module is removed, e.g. via the modules admin screen
    // (set object ID to the module name !)
    if (!xarModUnregisterHook('module', 'remove', 'API',
                           'keywords', 'admin', 'removehook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'display', 'GUI',
                           'keywords', 'user', 'displayhook')) {
        return false;
    }
/* // TODO: show items you created/edited someday ?
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'keywords', 'user', 'usermenu')) {
        return false;
    }
*/
    // Unregister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'keywords',
                             'blockType'=> 'keywordsarticles'))) return;
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'keywords',
                             'blockType'=> 'keywordscategories'))) return;

    // Remove Masks and Instances
    xarRemoveMasks('keywords');
    xarRemoveInstances('keywords');

    // Deletion successful
    return true;
}

?>
