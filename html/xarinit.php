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
    *       xar_tid INT(11) NOT NULL default '0',
    *       xar_tag VARCHAR(100) NOT NULL default '',
    *       xar_allowed INT(11)  NOT NULL default '0',
    *       PRIMARY KEY (xar_cid),
    *       UNIQUE KEY tag (xar_tag))";
    *****************************************************************/
    $fields = array(
    'xar_cid'      => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_tid'      => array('type'=>'integer','null'=>false,'increment'=>false,'default'=>'0'),
    'xar_tag'      => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'xar_allowed'  => array('type'=>'integer','null'=>false,'increment'=>false,'default'=>'0'),
    );

    // Create table
    $query = xarDBCreateTable($htmltable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create index on xar_tag
    $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_tag',
                   'fields'    => array('xar_tid, xar_tag'),
                   'unique'    => TRUE);
    
    // Create index
    $query = xarDBCreateIndex($htmltable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create htmltypes table
    $htmltypestable = $xartable['htmltypes'];

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
    if (!$result) return;

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

    // The default values of the HTML tags are:
    //   0 = Not allowed
    //   1 = Allowed
    //   2 = Allowed with parameters
    $htmltags = array('!--' =>      2,
                      'a' =>        2,
                      'abbr' =>     0,
                      'acronym' =>  0,
                      'address' =>  0,
                      'applet' =>   0,
                      'area' =>     0,
                      'b' =>        2,
                      'base' =>     0,
                      'basefont' => 0,
                      'bdo' =>      0,
                      'big' =>      0,
                      'blockquote' => 2,
                      'br' =>       2,
                      'button' =>   0,
                      'caption' =>  0,
                      'center' =>   2,
                      'cite' =>     0,
                      'code' =>     0,
                      'col' =>      0,
                      'colgroup' => 0,
                      'dd' =>       0,
                      'del' =>      0,
                      'dfn' =>      0,
                      'dir' =>      0,
                      'div' =>      2,
                      'dl' =>       0,
                      'dt' =>       0,
                      'em' =>       2,
                      'embed' =>    0,
                      'fieldset' => 0,
                      'font' =>     0,
                      'form' =>     0,
                      'h1' =>       0,
                      'h2' =>       0,
                      'h3' =>       0,
                      'h4' =>       0,
                      'h5' =>       0,
                      'h6' =>       0,
                      'hr' =>       2,
                      'i' =>        2,
                      'iframe' =>   0,
                      'img' =>      0,
                      'input' =>    0,
                      'ins' =>      0,
                      'isindex' =>  0,
                      'kbd' =>      0,
                      'l' =>        0,
                      'label' =>    0,
                      'legend' =>   0,
                      'li' =>       2,
                      'map' =>      0,
                      'marquee' =>  0,
                      'menu' =>     0,
                      'nl' =>       0,
                      'nobr' =>     0,
                      'object' =>   0,
                      'ol' =>       2,
                      'optgroup' => 0,
                      'option' =>   0,
                      'p' =>        2,
                      'param' =>    0,
                      'pre' =>      2,
                      'q' =>        0,
                      's' =>        0,
                      'samp' =>     0,
                      'script' =>   0,
                      'select' =>   0,
                      'small' =>    0,
                      'span' =>     0,
                      'strike' =>   0,
                      'strong' =>   2,
                      'sub' =>      0,
                      'sup' =>      0,
                      'table' =>    2,
                      'tbody' =>    0,
                      'td' =>       2,
                      'textarea' => 0,
                      'tfoot' =>    0,
                      'th' =>       2,
                      'thead' =>    0,
                      'tr' =>       2,
                      'tt' =>       2,
                      'u' =>        0,
                      'ul' =>       2,
                      'var' =>      0);

    // Insert HTML tags into xar_html table
    foreach ($htmltags as $htmltag=>$allowed) {
        // Get next ID in table
        $nextid = $dbconn->GenId($htmltable);

        // Insert HTML tags
        $query = "INSERT INTO $htmltable (
                        xar_cid,
                        xar_tid,
                        xar_tag,
                        xar_allowed)
                    VALUES (
                        $nextid, 
                        " . xarVarPrepForStore($htmltypeid) . ", 
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
        case '1.0.0':
            // Code to upgrade from version 1.0 goes here
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

            // fall through to the next upgrade
        case '1.0.1':
        case '1.1.0':
            // Code to upgrade from version 1.1 goes here

            // Align the allowed values in xar_html to allowed
            // values in Site.Core.AlloweableHTML
            $query = "UPDATE $htmltable SET xar_allowed=0 WHERE xar_allowed=1";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = "UPDATE $htmltable SET xar_allowed=1 WHERE xar_allowed=2";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = "UPDATE $htmltable SET xar_allowed=2 WHERE xar_allowed=3";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade
        case '1.2.0':
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
            $result = & $dbconn->Execute($query);
            if (!$result) return;

            // Drop current index
            $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_1',
                           'fields'    => array('xar_tag'));
            $query = xarDBDropIndex($htmltable, $index);
            $result = & $dbconn->Execute($query);
            if (!$result) return;
            
            // Set current html tags in xar_html to default type
            $query = "UPDATE $htmltable 
                      SET xar_tid = " . $htmltypeid;

            $result =& $dbconn->Execute($query);
            if (!$result) return;
            
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
        case '1.3.0':
            // Code to upgrade from version 1.3 goes here
            break;

        default:
            // Couldn't find a previous version to upgrade
            return;
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
