<?php
/**
 * File: $Id$
 *
 * Xaraya html
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage html Module
 * @author John Cox
*/


/**
 * Initialize the html module
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return true on success, false on failure
 * @raise none
 */
function html_init()
{
    // Set up module variables
    xarModSetVar('html', 'itemsperpage', 20);

    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Create html table
    $htmltable = $xartable['html'];

    /*****************************************************************
    * $query = "CREATE TABLE $htmltable (
    *       xar_cid INT(11) NOT NULL auto_increment,
    *       xar_tag VARCHAR(100) NOT NULL default '',
    *       xar_allowed INT(11)  NOT NULL default '0',
    *       PRIMARY KEY (xar_cid),
    *       UNIQUE KEY tag (xar_tag))";
    *****************************************************************/
    $fields = array(
    'xar_cid'      => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_tag'      => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'xar_allowed'  => array('type'=>'integer','null'=>false,'increment'=>false,'default'=>'0'),
    );

    // Create table
    $query = xarDBCreateTable($htmltable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create index on xar_tag
    $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_tag',
                   'fields'    => array('xar_tag'),
                   'unique'    => TRUE);
    
    // Create index
    $query = xarDBCreateIndex($htmltable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // The default values of the HTML tags are:
    //   1 = Not allowed
    //   2 = Allowed
    //   3 = Allowed with parameters
    $htmltags = array('!--' =>      3,
                      'a' =>        3,
                      'abbr' =>     1,
                      'acronym' =>  1,
                      'address' =>  1,
                      'applet' =>   1,
                      'area' =>     1,
                      'b' =>        3,
                      'base' =>     1,
                      'basefont' => 1,
                      'bdo' =>      1,
                      'big' =>      1,
                      'blockquote' => 3,
                      'br' =>       3,
                      'button' =>   1,
                      'caption' =>  1,
                      'center' =>   3,
                      'cite' =>     1,
                      'code' =>     1,
                      'col' =>      1,
                      'colgroup' => 1,
                      'dd' =>       1,
                      'del' =>      1,
                      'dfn' =>      1,
                      'dir' =>      1,
                      'div' =>      3,
                      'dl' =>       1,
                      'dt' =>       1,
                      'em' =>       3,
                      'embed' =>    1,
                      'fieldset' => 1,
                      'font' =>     1,
                      'form' =>     1,
                      'h1' =>       1,
                      'h2' =>       1,
                      'h3' =>       1,
                      'h4' =>       1,
                      'h5' =>       1,
                      'h6' =>       1,
                      'hr' =>       3,
                      'i' =>        3,
                      'iframe' =>   1,
                      'img' =>      1,
                      'input' =>    1,
                      'ins' =>      1,
                      'isindex' =>  1,
                      'kbd' =>      1,
                      'l' =>        1,
                      'label' =>    1,
                      'legend' =>   1,
                      'li' =>       3,
                      'map' =>      1,
                      'marquee' =>  1,
                      'menu' =>     1,
                      'nl' =>       1,
                      'nobr' =>     1,
                      'object' =>   1,
                      'ol' =>       3,
                      'optgroup' => 1,
                      'option' =>   1,
                      'p' =>        3,
                      'param' =>    1,
                      'pre' =>      3,
                      'q' =>        1,
                      's' =>        1,
                      'samp' =>     1,
                      'script' =>   1,
                      'select' =>   1,
                      'small' =>    1,
                      'span' =>     1,
                      'strike' =>   1,
                      'strong' =>   3,
                      'sub' =>      1,
                      'sup' =>      1,
                      'table' =>    3,
                      'tbody' =>    1,
                      'td' =>       3,
                      'textarea' => 1,
                      'tfoot' =>    1,
                      'th' =>       3,
                      'thead' =>    1,
                      'tr' =>       3,
                      'tt' =>       3,
                      'u' =>        1,
                      'ul' =>       3,
                      'var' =>      1);

    // Insert HTML tags into xar_html table
    foreach ($htmltags as $htmltag=>$allowed) {
        // Get next ID in table
        $nextid = $dbconn->GenId($htmltable);

        // Insert HTML tags
        $query = "INSERT INTO $htmltable (
                        xar_cid,
                        xar_tag,
                        xar_allowed)
                    VALUES (
                        $nextid, 
                        '" . xarVarPrepForStore($htmltag) . "', 
                        " . xarVarPrepForStore($allowed) . ")";

        $result =& $dbconn->Execute($query);
    
        // Check for errors
        if (!$result) return;
    }

    // Register Masks
    xarRegisterMask('ReadHTML','All','html','All','All','ACCESS_READ');
    xarRegisterMask('EditHTML','All','html','All','All','ACCESS_EDIT');
    xarRegisterMask('AddHTML','All','html','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteHTML','All','html','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminHTML','All','html','All','All','ACCESS_ADMIN');

    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'transform-input',
                           'API',
                           'html',
                           'user',
                           'transforminput')) return;

    if (!xarModRegisterHook('item',
                           'transform',
                           'API',
                           'html',
                           'user',
                           'transformoutput')) return;


    // Initialisation successful
    return true;
}

/**
 * Upgrade the html module from an old version
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return true on success, false on failure
 * @raise none
 */
function html_upgrade($oldversion)
{
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            // Set up module hooks
            if (!xarModRegisterHook('item',
                                   'transform-input',
                                   'API',
                                   'html',
                                   'user',
                                   'transforminput')) return false;

            if (!xarModRegisterHook('item',
                                   'transform',
                                   'API',
                                   'html',
                                   'user',
                                   'transformoutput')) return false;

            // fall through to the next upgrade
        case '1.1':
            // Code to upgrade from version 1.1 goes here

            // Align the allowed values in xar_html to allowed
            // values in Site.Core.AlloweableHTML
            $query = "UPDATE $htmltable SET xar_allowed=0 WHERE xar_allowed=1";
            $result =& $dbconn->Execute($query);
            if (!$result) return false;

            $query = "UPDATE $htmltable SET xar_allowed=1 WHERE xar_allowed=2";
            $result =& $dbconn->Execute($query);
            if (!$result) return false;

            $query = "UPDATE $htmltable SET xar_allowed=2 WHERE xar_allowed=3";
            $result =& $dbconn->Execute($query);
            if (!$result) return false;

            // fall through to the next upgrade
        case '1.2':
            // Code to upgrade from version 1.2 goes here
            
            // Create htmltypes table
            /*****************************************************************
            * $query = "CREATE TABLE $htmltypestable (
            *       xar_id INT(11) NOT NULL auto_increment,
            *       xar_type VARCHAR(20) NOT NULL default ''
            *       PRIMARY KEY (xar_type),
            *       UNIQUE KEY tag (xar_name))";
            *****************************************************************/
            $fields = array(
                'xar_id'       => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
                'xar_type'     => array('type'=>'varchar','size'=>20,'null'=>false,'default'=>'')
            );

            // Create table
            $query = xarDBCreateTable($htmltypestable, $fields);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Create index on xar_type
            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_type',
                           'fields'    => array('xar_type'),
                           'unique'    => TRUE);

            $query = xarDBCreateIndex($htmltypestable, $index);
            $result =& $dbconn->Execute($query);

            // Insert HTML types into xar_htmltypes table
            $defaulttype = 'html';

            // Get the next ID in the table
            $nextid = $dbconn->GenId($htmltypestable);

            // Insert html
            $query = "INSERT INTO $htmltypestable (
                         xar_id,
                         xar_type)
                     VALUES (
                        $nextid, 
                        '" . xarVarPrepForStore($defaulttype) ."')";

            $result =& $dbconn->Execute($query);

            // Check for error
            if (!$result) return;

            // Get the ID of the item that was inserted
            $htmltypeid = $dbconn->PO_Insert_ID($htmltypestable, 'xar_id');

            // Add the column 'xar_tid' to the xar_html table
             $query = xarDBAlterTable($htmltable,
                                     array('command' => 'add',
                                           'field' => 'xar_tid',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => $htmltypeid));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Drop current index
            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_tag',
                           'fields'    => array('xar_tag'));
            $query = xarDBDropIndex($htmltable, $index);
            $result =& $dbconn->Execute($query);
            
            // Set current html tags in xar_html to default type
            $query = "UPDATE $htmltable 
                      SET xar_tid = " . $htmltypeid;

            $result =& $dbconn->Execute($query);
            if (!$result) return false;
            
            // Create new index on xar_html table
            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html',
                           'fields'    => array('xar_tid, xar_tag'),
                           'unique'    => TRUE);
    
            // Create index
            $query = xarDBCreateIndex($htmltable, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade
        case '1.3':
            // Code to upgrade from version 1.3 goes here
            break;
    }

    return true;
}

/**
 * Delete the html module
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return true on success, false on failure
 * @raise none
 */
function html_delete()
{
    // Remove module variables
    xarModDelVar('html', 'itemsperpage');

    // Remove Masks and Instances
    xarRemoveMasks('html');
    xarRemoveInstances('html');

    // Get the database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['html'] );
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['htmltypes']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Deletion successful
    return true;
}

?>
