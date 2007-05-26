<?php

/**
 * Initialize the module
 */
function sitesearch_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $prefix   =  xarDBGetSiteTablePrefix();
    
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "
		keywords  c(255) NotNull DEFAULT '',
		count          I NotNull DEFAULT 1,
		last_search    I NotNull DEFAULT 0
    ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($xartable['sitesearch_query_log'], $fields);
    if (!$result) {return;}

    $result = $datadict->createIndex(
        "i_{$prefix}_sitesearch_query_log_keywords",
        $xartable['sitesearch_query_log'],
        array('keywords')
    );
    if (!$result) {return;}

    /* 
        Register blocks 
    */
    if( !xarModAPIFunc('blocks', 'admin', 'register_block_type',
        array(
            'modName' => 'sitesearch',
            'blockType' => 'search')
        )
    ){ return; }
    
    
    /**
        Set hooks
    */
    if (!xarModRegisterHook('item','create','API', 'sitesearch', 'admin', 'indexpage'))
    {
        return false;
    }
    if (!xarModRegisterHook('item','update','API', 'sitesearch', 'admin', 'indexpage'))
    {
        return false;
    }

    $base = ('var/sitesearch');
    if( !file_exists($base) && is_writable('var') ){ mkdir($base, '755'); }
    $database = $base . '/databases';
    if( !file_exists($database) && is_writable($base) ){ mkdir($database, '755'); }
    
    /**
        Setup some default module vars
    */
    xarModSetVar('sitesearch', 'HLBeg', '<font color="000088"><b>');
    xarModSetVar('sitesearch', 'HLEnd', '</b></font>');
    
    /**
        Register the module components that are privileges objects
        Format is
        xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
    */
    xarRegisterMask('RunSiteSearch',  'All','sitesearch','All','All','ACCESS_READ');
    xarRegisterMask('AdminSiteSearch','All','sitesearch','All','All','ACCESS_ADMIN');
   
    // Initialisation successful
    return true;
}


/**
 * Upgrade the module from an old version
 */
function sitesearch_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $schemaFile = 'modules/sitesearch/xardata/tables.xml';

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.9.1':
            if (!xarModRegisterHook('item','create','API', 'sitesearch', 'admin', 'indexpage'))
            {
                return false;
            }
            if (!xarModRegisterHook('item','update','API', 'sitesearch', 'admin', 'indexpage'))
            {
                return false;
            }
            xarModSetVar('sitesearch', 'HLBeg', '<font color="000088"><b>');
            xarModSetVar('sitesearch', 'HLEnd', '</b></font>');

        case '0.9.5':     
        case '0.9.8':     
        case '0.9.9':     
        case '1.0.0':    
            if( !xarModAPIFunc('blocks', 'admin', 'register_block_type',
                array(
                    'modName' => 'sitesearch',
                    'blockType' => 'search')
                )
            ){ return; }
         
        case '1.0.1':     
               
            break;

        default:
            // Couldn't find a previous version to upgrade
            return;
    }

    // Update successful
    return true;
}


/**
 * Delete the module
 */
function sitesearch_delete()
{
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    
    /* Drop the sitesearch tables */
    $result = $datadict->dropTable($xartable['sitesearch_query_log']);
    if( !$result ){ return false; }

    if( !xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
        array(
            'modName' => 'sitesearch',
            'blockType' => 'search')
        )
    ){ return; }
    
    
    if (!xarModUnregisterHook('item','create','API', 'sitesearch', 'admin', 'indexpage')) 
    {
        return false;
    }
    if (!xarModUnregisterHook('item','update','API', 'sitesearch', 'admin', 'indexpage')) 
    {
        return false;
    }

    // Delete All module vars
    xarModDelAllVars('sitesearch');

    // Remove Masks and Instances
    xarRemoveMasks('sitesearch');
    xarRemoveInstances('sitesearch');

    // Deletion successful
    return true;
}
?>
