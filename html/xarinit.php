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

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the autolinks module
 */
function html_init()
{

    // Set up module variables
    xarModSetVar('html', 'itemsperpage', 20);

    $enhanced = 'a:21:{s:3:"!--";i:2;s:1:"a";i:2;s:1:"b";i:2;s:10:"blockquote";i:2;s:2:"br";i:1;s:6:"center";i:1;s:3:"div";i:2;s:2:"em";i:1;s:2:"hr";i:2;s:1:"i";i:2;s:2:"li";i:2;s:2:"ol";i:2;s:1:"p";i:2;s:3:"pre";i:2;s:6:"strong";i:2;s:2:"tt";i:2;s:2:"ul";i:2;s:5:"table";i:2;s:2:"td";i:2;s:2:"th";i:2;s:2:"tr";i:2;}';

    xarModSetVar('html', 'AllowedHTML', $enhanced);

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];

    // There was not, create table
    /*****************************************************************
    * $query = "CREATE TABLE $htmltable (
    *       xar_lid INT(11) NOT NULL auto_increment,
    *       xar_tag VARCHAR(100) NOT NULL default '',
    *       xar_title VARCHAR(100) NOT NULL default '',
    *       xar_url VARCHAR(200) NOT NULL default '',
    *       xar_comment VARCHAR(200) NOT NULL default '',
    *       PRIMARY KEY (xar_lid),
    *       UNIQUE KEY tag (xar_tag))";
    *****************************************************************/
    $fields = array(
    'xar_cid'      => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_tag'      => array('type'=>'varchar','size'=>100,'null'=>false,'default'=>''),
    'xar_allowed'  => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    );

    $query = xarDBCreateTable($htmltable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_'.xarDBGetSiteTablePrefix().'_html_1',
                   'fields'    => array('xar_tag'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($htmltable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $htmltags = array('!--',
                      'a',
                      'abbr',
                      'acronym',
                      'address',
                      'applet',
                      'area',
                      'b',
                      'base',
                      'basefont',
                      'bdo',
                      'big',
                      'blockquote',
                      'br',
                      'button',
                      'caption',
                      'center',
                      'cite',
                      'code',
                      'col',
                      'colgroup',
                      'del',
                      'dfn',
                      'dir',
                      'div',
                      'dl',
                      'dd',
                      'dt',
                      'em',
                      'embed',
                     'fieldset',
                      'font',
                      'form',
                      'h1',
                     'h2',
                      'h3',
                      'h4',
                      'h5',
                      'h6',
                      'hr',
                      'i',
                      'iframe',
                      'img',
                      'input',
                      'ins',
                      'isindex',
                      'kbd',
                      'label',
                      'legend',
                      'l',
                      'li',
                      'map',
                      'marquee',
                      'menu',
                     'nl',
                      'nobr',
                      'object',
                      'ol',
                      'optgroup',
                      'option',
                     'p',
                      'param',
                      'pre',
                      'q',
                     's',
                      'samp',
                      'script',
                      'select',
                      'small',
                     'span',
                      'strike',
                      'strong',
                     'sub',
                      'sup',
                      'table',
                      'tbody',
                      'td',
                      'textarea',
                     'tfoot',
                      'th',
                      'thead',
                      'tr',
                     'tt',
                      'u',
                      'ul',
                      'var');

    foreach ($htmltags as $htmltag) {
        $id_allowedvar = $dbconn->GenId($htmltable);
        $query = "INSERT INTO $htmltable VALUES ($id_allowedvar,'$htmltag', 1)";
        $result =& $dbconn->Execute($query);
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


    // Initialisation successful
    return true;
}

/**
 * upgrade the smiley module from an old version
 */
function html_upgrade($oldversion)
{

    // Upgrade dependent on old version number
    switch ($oldversion) {

        // TODO: version numbers - normalise.
        case '1.0':
            // Set up module hooks
            if (!xarModRegisterHook('item',
                                   'transform-input',
                                   'API',
                                   'html',
                                   'user',
                                   'transforminput')) return;
            return true;
            break;
    }

    return false;
}

/**
 * delete the smiley module
 */
function html_delete()
{

    // Drop the table
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $htmltable = $xartable['html'];
    $query = xarDBDropTable($htmltable );
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove module variables
    xarModDelVar('html', 'itemsperpage');
    xarModDelVar('html', 'AllowedHTML');

    // Remove Masks and Instances
    xarRemoveMasks('html');
    xarRemoveInstances('html');

    // Deletion successful
    return true;
}

?>