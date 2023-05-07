<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Initialize the html module
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return bool true on success, false on failure
 * @throws none
 */
function html_init()
{
    // Set up module variables
    xarModVars::set('html', 'itemsperpage', 20);
    xarModVars::set('html', 'transformtype', 1);

    // Load Table Maintainance API
    sys::import('xaraya.tableddl');

    // Set up database tables
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Create html table
    $htmltable = $xartable['html'];

    /*****************************************************************
    * $query = "CREATE TABLE $htmltable (
    *       id integer unsigned NOT NULL auto_increment,
    *       tid INT(11) NOT NULL default '0',
    *       tag VARCHAR(100) NOT NULL default '',
    *       allowed INT(11)  NOT NULL default '0',
    *       PRIMARY KEY (id),
    *       UNIQUE KEY tag (tag))";
    *****************************************************************/
    $fields = array(
    'id'      => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
//    'id'      => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'tid'      => array('type'=>'integer','null'=>false,'increment'=>false,'default'=>'0'),
    'tag'      => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'allowed'  => array('type'=>'integer','null'=>false,'increment'=>false,'default'=>'0'),
    );

    // Create table
    $query = xarTableDDL::createTable($htmltable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create index on tag
    $index = array('name'      => 'i_'.xarDB::getPrefix().'_html_tag',
                   'fields'    => array('tid, tag'),
                   'unique'    => TRUE);

    // Create index
    $query = xarTableDDL::createIndex($htmltable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create htmltypes table
    $htmltypestable = $xartable['htmltypes'];

    /*****************************************************************
    * $query = "CREATE TABLE $htmltypestable (
    *       id integer unsigned NOT NULL auto_increment,
    *       type VARCHAR(20) NOT NULL default ''
    *       PRIMARY KEY (type),
    *       UNIQUE KEY tag (xar_name))";
    *****************************************************************/
    $fields = array(
    'id'       => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
//    'id'       => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'type'     => array('type'=>'varchar','size'=>20,'null'=>false,'default'=>'')
    );

    // Create table
    $query = xarTableDDL::createTable($htmltypestable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Create index on type
    $index = array('name'      => 'i_'.xarDB::getPrefix().'_html_type',
                   'fields'    => array('type'),
                   'unique'    => TRUE);

    $query = xarTableDDL::createIndex($htmltypestable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Insert HTML types into xar_htmltypes table
    $defaulttype = 'html';

    // Get the next ID in the table
    $nextid = $dbconn->GenId($htmltypestable);

    // Insert html
    $query = "INSERT INTO $htmltypestable (
                id,
                type)
              VALUES (?, ?)";

    $result =& $dbconn->Execute($query, array($nextid, (string) $defaulttype));

    // Check for error
    if (!$result) return;

    // Get the ID of the item that was inserted
    $htmltypeid = $dbconn->PO_Insert_ID($htmltypestable, 'id');

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
                      'blink' =>    0,
                      'blockquote' => 2,
                      'br' =>       2,
                      'button' =>   0,
                      'caption' =>  0,
                      'center' =>   0,
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
                        id,
                        tid,
                        tag,
                        allowed)
                    VALUES (?, ?, ?, ?)";

        $bindvars = array( $nextid,
                          (int) $htmltypeid,
                          (string) $htmltag,
                          (int) $allowed);

        $result =& $dbconn->Execute($query, $bindvars);

        // Check for errors
        if (!$result) return;
    }

    // Register Masks
    xarMasks::register('ReadHTML','All','html','All','All','ACCESS_READ');
    xarMasks::register('EditHTML','All','html','All','All','ACCESS_EDIT');
    xarMasks::register('AddHTML','All','html','All','All','ACCESS_ADD');
    xarMasks::register('ManageHTML','All','html','All','All','ACCESS_DELETE');
    xarMasks::register('AdminHTML','All','html','All','All','ACCESS_ADMIN');

    // Set up module hooks
    if (!xarModHooks::register('item',
                           'transform-input',
                           'API',
                           'html',
                           'user',
                           'transforminput')) return;

    if (!xarModHooks::register('item',
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
 * @throws none
 */
function html_upgrade($oldversion)
{
    // Load Table Maintainance API
    sys::import('xaraya.tableddl');

    // Set up database tables
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0 goes here
            // Set up module hooks
            if (!xarModHooks::register('item',
                                   'transform-input',
                                   'API',
                                   'html',
                                   'user',
                                   'transforminput')) return;

            if (!xarModHooks::register('item',
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
            $query = "UPDATE $htmltable SET allowed=0 WHERE allowed=1";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = "UPDATE $htmltable SET allowed=1 WHERE allowed=2";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $query = "UPDATE $htmltable SET allowed=2 WHERE allowed=3";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade
        case '1.2.0':
            // Code to upgrade from version 1.2 goes here

            // Create htmltypes table
            /*****************************************************************
            * $query = "CREATE TABLE $htmltypestable (
            *       id integer unsigned NOT NULL auto_increment,
            *       type VARCHAR(20) NOT NULL default ''
            *       PRIMARY KEY (type),
            *       UNIQUE KEY tag (xar_name))";
            *****************************************************************/
            $fields = array(
                'id'       => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
//                'id'       => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
                'type'     => array('type'=>'varchar','size'=>20,'null'=>false,'default'=>'')
            );

            // Create table
            $query = xarTableDDL::createTable($htmltypestable, $fields);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Create index on type
            $index = array('name'      => 'i_'.xarDB::getPrefix().'_html_type',
                           'fields'    => array('type'),
                           'unique'    => TRUE);

            $query = xarTableDDL::createIndex($htmltypestable, $index);
            $result =& $dbconn->Execute($query);

            // Insert HTML types into xar_htmltypes table
            $defaulttype = 'html';

            // Get the next ID in the table
            $nextid = $dbconn->GenId($htmltypestable);

            // Insert html
            $query = "INSERT INTO $htmltypestable (
                         id,
                         type)
                     VALUES (?, ?)";

            $result =& $dbconn->Execute($query, array( $nextid, (string) $defaulttype));

            // Check for error
            if (!$result) return;

            // Get the ID of the item that was inserted
            $htmltypeid = $dbconn->PO_Insert_ID($htmltypestable, 'id');

            // Add the column 'tid' to the xar_html table
             $query = xarTableDDL::alterTable($htmltable,
                                     array('command' => 'add',
                                           'field' => 'tid',
                                           'type' => 'integer',
                                           'null' => false,
                                           'default' => $htmltypeid));
            $result = & $dbconn->Execute($query);
            if (!$result) return;

            // Drop current index
            $index = array('name'      => 'i_'.xarDB::getPrefix().'_html_1',
                           'fields'    => array('tag'));
            $query = xarTableDDL::dropIndex($htmltable, $index);
            $result = & $dbconn->Execute($query);
            if (!$result) return;

            // Set current html tags in xar_html to default type
            $query = "UPDATE $htmltable
                      SET tid = " . $htmltypeid;

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Create new index on xar_html table
            $index = array('name'      => 'i_'.xarDB::getPrefix().'_html',
                           'fields'    => array('tid, tag'),
                           'unique'    => TRUE);

            // Create index
            $query = xarTableDDL::createIndex($htmltable, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // fall through to the next upgrade
        case '1.3':
        case '1.3.0':
            xarModVars::set('html', 'transformtype', 1);
            // Code to upgrade from version 1.3 goes here
            break;

        case '1.4':
        case '1.4.0':
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
 * @throws none
 */
function html_delete()
{

    // Remove module variables
    xarModVars::delete('html', 'itemsperpage');
    xarMasks::removemasks('html');
    xarPrivileges::removeInstances('html');

    // Get the database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    sys::import('xaraya.tableddl');

    // Generate the SQL to drop the table using the API
    $query = xarTableDDL::dropTable($xartable['html'] );
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Generate the SQL to drop the table using the API
    $query = xarTableDDL::dropTable($xartable['htmltypes']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set the html back to safe html tags
    $allowedhtml = 'a:25:{s:3:"!--";s:1:"2";s:1:"a";s:1:"2";s:1:"b";s:1:"2";s:10:"blockquote";s:1:"2";s:2:"br";s:1:"2";s:6:"center";s:1:"2";s:3:"div";s:1:"2";s:2:"em";s:1:"2";s:4:"font";i:0;s:2:"hr";s:1:"2";s:1:"i";s:1:"2";s:3:"img";i:0;s:2:"li";s:1:"2";s:7:"marquee";i:0;s:2:"ol";s:1:"2";s:1:"p";s:1:"2";s:3:"pre";s:1:"2";s:4:"span";i:0;s:6:"strong";s:1:"2";s:2:"tt";s:1:"2";s:2:"ul";s:1:"2";s:5:"table";s:1:"2";s:2:"td";s:1:"2";s:2:"th";s:1:"2";s:2:"tr";s:1:"2";}';

    // I'm lazy.  So shoot me, one time thing:
    $allowedhtml = unserialize($allowedhtml);

    xarConfigVars::set(null,'Site.Core.AllowableHTML', $allowedhtml);
    // Deletion successful
    return true;
}
?>