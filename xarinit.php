<?php
// Load Table Maintainance API
sys::import('xaraya.tableddl');

function fulltext_init()
{
    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
    $dbtype = xarSystemVars::get(sys::CONFIG, 'DB.Type');
    $ftable = $tables['fulltext'];
    
    // @TODO: prevent install when db is not MySQL or just skip fulltext indexing ?
    // @CHECKME: is there a way to declare a db type dependency ?

    try {        
        $dbconn->begin();
        /**
         * CREATE TABLE xar_fulltext (
         *   id         integer NOT NULL auto_increment,
         *   module_id  integer default 0,
         *   itemtype   integer default 0,
         *   item_id    integer default 0,
         *   text       text
         *   PRIMARY KEY (id)
         * )
        **/
         $fields = array(
             'id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
             'module_id' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),            
             'itemtype' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
             'item_id' => array('type' => 'integer', 'size' => 11, 'unsigned' => true, 'null' => false, 'default' => '0'),
             'text' => array('type'=>'text', 'size'=>'medium', 'charset' => $charset)
         );

         // Create the fulltext table
         $query = xarDBCreateTable($ftable, $fields);
         $dbconn->Execute($query);

         // each entry should be unique
         $index = array(
             'name'   => 'i_'.$prefix.'_fulltext_combo',
             'fields' => array('module_id', 'itemtype', 'item_id'),
             'unique' => true
         );

         $query = xarDBCreateIndex($ftable,$index);
         $dbconn->Execute($query);

        // @TODO: prevent install when db is not MySQL or just skip this...?
        if ($dbtype == 'mysql' || $dbtype == 'mysqli') {
            // Add fulltext index
            $index = 'i_'.$prefix.'_fulltext_text';
            $query = "ALTER TABLE $ftable ADD FULLTEXT $index (text)";
            $dbconn->Execute($query);
        }
    
         // Let's commit this, since we're gonna do some other stuff
         $dbconn->commit();
    } catch (Exception $e) {
        // Damn
        $dbconn->rollback();
        throw $e;
    }

    xarModRegisterHook('item', 'create', 'api', 'fulltext', 'hooks', 'itemcreate');    
    xarModRegisterHook('item', 'update', 'api', 'fulltext', 'hooks', 'itemupdate');    
    xarModRegisterHook('item', 'delete', 'api', 'fulltext', 'hooks', 'itemdelete');        
    //xarModRegisterHook('item', 'display', 'gui', 'fulltext', 'hooks', 'itemdisplay');
    xarModRegisterHook('module', 'modifyconfig', 'gui', 'fulltext', 'hooks', 'modulemodifyconfig');            
    xarModRegisterHook('module', 'updateconfig', 'api', 'fulltext', 'hooks', 'moduleupdateconfig');
    //xarModRegisterHook('module', 'remove', 'api', 'fulltext', 'hooks', 'moduleremove');

    xarRegisterMask('ReadFulltext','All','fulltext','All','All','ACCESS_READ');
    xarRegisterMask('EditFulltext','All','fulltext','All','All','ACCESS_EDIT');
    xarRegisterMask('ManageFulltext','All','fulltext','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminFulltext','All','fulltext','All','All','ACCESS_ADMIN');
    
    xarModVars::set('fulltext', 'items_per_page', 20);
       
    return true;
}

function fulltext_activate()
{
    return true;
}

function fulltext_upgrade($oldversion)
{
    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
    $dbtype = xarSystemVars::get(sys::CONFIG, 'DB.Type');
    $ftable = $tables['fulltext'];

    switch ($oldversion) {
        
        default:
            // current version
            break;
    }
    return true;
}

function fulltext_delete()
{
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', array('module' => 'fulltext'));
}
?>