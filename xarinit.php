<?php
/**
 * Xaraya BBCode
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author larseneo, Hinrich Donner
*/

xarDBLoadTableMaintenanceAPI();

function bbcode_init() 
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    // Set up module variables
    xarModSetVar('bbcode', 'dolinebreak', 0);
    xarModSetVar('bbcode', 'transformtype', 1);

    $table = $xartable['bbcode'];
    $fields = array('xar_id'            => array('type' => 'integer', 
                                                 'null' => false, 
                                                 'increment' => true, 
                                                 'primary_key' => true),
                    'xar_tag'           => array('type' => 'varchar', 
                                                 'size' => 100, 
                                                 'null' => false, 
                                                 'default' => ''),
                    'xar_name'          => array('type' => 'varchar', 
                                                 'size' => 200, 
                                                 'null' => false, 
                                                 'default' => ''),
                    'xar_description'   => array('type' => 'text'),
                    'xar_transformed'   => array('type' => 'text'));

    $query = xarDBCreateTable($table, $fields);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $index = array('name' => 'i_'.xarDBGetSiteTablePrefix().'_bbcode_1',
        'fields' => array('xar_tag'),
        'unique' => true);
    $query = xarDBCreateIndex($table, $index);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $codes = array(
       array('[u]'         ,'underline','[u]Your Text[/u] will produce', '[u]Your Text[/u]'),
       array('[b]'         ,'bold','[b]Your Text[/b] will produce', '[b]Your Text[/b]'),
       array('[o]'         ,'overline','[o]Your Text[/o] will produce', '[o]Your Text[/o]'),
       array('[lt]'        ,'linethrough','[lt]Your Text[/lt] will produce', '[lt]Your Text[/lt]'),
       array('[sc]'        ,'smallcaps','[sc]Your Text[/sc] will produce', '[sc]Your Text[/sc]'),
       array('[i]'         ,'italics','[i]Your Text[/i] will produce', '[i]Your Text[/i]'),
       array('[sub]'       ,'sub','[sub]Your Text[/sub] will produce', '[sub]Your Text[/sub]'),
       array('[sup]'       ,'sup','[sup]Your Text[/sup] will produce', '[sup]Your Text[/sup]'),
       array('[color]'     ,'color','[color=red]Your Text[/color] will produce', '[color=red]Your Text[/color]'),
       array('[size]'      ,'size','[size=2]Your Text[/size] will produce', '[size=2]Your Text[/size]'),
       array('[img]'       ,'image','[image]http://www.your-url-for-this-image.com[/img] will produce', '[image]http://www.your-url-for-this-image.com[/img]'),
       array('[url]'       ,'url','[url]http://www.your-url.com[/url] will produce', '[url]http://www.your-url.com[/url]'),
       array('[email]'     ,'email','[email]your@email.com[/email] will produce', '[email]your@email.com[/email]'),
       array('[google]'    ,'google','[google]Your Text[/google] will produce', '[google]Your Text[/google]'),
       array('[yahoo]'     ,'yahoo','[yahoo]Your Text[/yahoo] will produce', '[yahoo]Your Text[/yahoo]'),
       array('[msn]'       ,'msn','[msn]Your Text[/msn] will produce', '[msn]Your Text[/msn]'),
       array('[dictionary]','dictionary','[dictionary]Your Text[/dictionary] will produce', '[dictionary]Your Text[/dictionary]'),
       array('[wiki]'      ,'wiki','[wiki]Your Text[/wiki] will produce', '[wiki]Your Text[/wiki]'),
       array('[thesaurus]' ,'thesaurus','[thesaurus]Your Text[/thesaurus] will produce', '[thesaurus]Your Text[/thesaurus]'),
       array('[code]'      ,'code','[code]Your Text[/code] will produce', '[code]Your Text[/code]'),
       array('[quote]'     ,'quote','[quote]Your Text[/quote] will produce', '[quote]Your Text[/quote]'),
       array('[you]'       ,'you','[you] will produce', 'The viewers name, likeso: [you]'));

    foreach ($codes as $code) {
        // Get next ID in table
        $nextId = $dbconn->GenId($table);
        $query = "INSERT INTO $table (xar_id, xar_tag, xar_name, xar_description, xar_transformed) VALUES (?,?,?,?,?)";
        $result =& $dbconn->Execute($query,array($nextId, $code[0],$code[1],$code[2],$code[3]));
        if (!$result) return;
    }

    xarRegisterMask('EditBBCode','All','bbcode','All','All','ACCESS_EDIT');
    xarRegisterMask('OverviewBBCode','All','bbcode','All','All','ACCESS_OVERVIEW');

    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'transform',
                           'API',
                           'bbcode',
                           'user',
                           'transform')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
        
    }
    if (!xarModRegisterHook('item',
                           'formheader',
                           'GUI',
                           'bbcode',
                           'user',
                           'formheader')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
        
    }
    if (!xarModRegisterHook('item',
                           'formaction',
                           'GUI',
                           'bbcode',
                           'user',
                           'formaction')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarModRegisterHook('item',
                           'formdisplay',
                           'GUI',
                           'bbcode',
                           'user',
                           'formdisplay')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!xarModRegisterHook('item',
                           'formarea',
                           'GUI',
                           'bbcode',
                           'user',
                           'formarea')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Initialisation successful
    return true;
}

function bbcode_upgrade($oldversion) 
{
    switch ($oldversion) {
        case '1.0':
        case '1.0.0':
            $modversion['admin']            = 1;
            xarModSetVar('bbcode', 'dolinebreak', 0);
            xarModSetVar('bbcode', 'transformtype', 1);
            xarRegisterMask('EditBBCode','All','bbcode','All','All','ACCESS_EDIT');
            // Code to upgrade from version 1.3 goes here
            // Remove module hooks
            if (!xarModUnregisterHook('item',
                                      'formfooter',
                                      'GUI',
                                      'bbcode',
                                      'user',
                                      'formfooter')) {
                $msg = xarML('Could not un-register hook');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            break;

        case '1.1':
        case '1.1.0':
            $modversion['user']             = 1;
            break;
        case '1.1.1':
            $modversion['user']             = 0;
            break;
        case '1.1.2':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $table = $xartable['bbcode'];
            $fields = array('xar_id'            => array('type' => 'integer', 
                                                         'null' => false, 
                                                         'increment' => true, 
                                                         'primary_key' => true),
                            'xar_tag'           => array('type' => 'varchar', 
                                                         'size' => 100, 
                                                         'null' => false, 
                                                         'default' => ''),
                            'xar_name'          => array('type' => 'varchar', 
                                                         'size' => 200, 
                                                         'null' => false, 
                                                         'default' => ''),
                            'xar_description'   => array('type' => 'text'),
                            'xar_transformed'   => array('type' => 'text'));

            $query = xarDBCreateTable($table, $fields);
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $index = array('name' => 'i_'.xarDBGetSiteTablePrefix().'_bbcode_1',
                'fields' => array('xar_tag'),
                'unique' => true);
            $query = xarDBCreateIndex($table, $index);
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $codes = array("'[u]','underline','[u]Your Text[/u] will produce', '[u]Your Text[/u]'",
                           "'[b]','bold','[b]Your Text[/b] will produce', '[b]Your Text[/b]'",
                           "'[o]','overline','[o]Your Text[/o] will produce', '[o]Your Text[/o]'",
                           "'[lt]','linethrough','[lt]Your Text[/lt] will produce', '[lt]Your Text[/lt]'",
                           "'[sc]','smallcaps','[sc]Your Text[/sc] will produce', '[sc]Your Text[/sc]'",
                           "'[i]','italics','[i]Your Text[/i] will produce', '[i]Your Text[/i]'",
                           "'[sub]','sub','[sub]Your Text[/sub] will produce', '[sub]Your Text[/sub]'",
                           "'[sup]','sup','[sup]Your Text[/sup] will produce', '[sup]Your Text[/sup]'",
                           "'[color]','color','[color=red]Your Text[/color] will produce', '[color=red]Your Text[/color]'",
                           "'[size]','size','[size=2]Your Text[/size] will produce', '[size=2]Your Text[/size]'",
                           "'[img]','image','[image]http://www.your-url-for-this-image.com[/img] will produce', '[image]http://www.your-url-for-this-image.com[/img]'",
                           "'[url]','url','[url]http://www.your-url.com[/url] will produce', '[url]http://www.your-url.com[/url]'",
                           "'[email]','email','[email]your@email.com[/email] will produce', '[email]your@email.com[/email]'",
                           "'[google]','google','[google]Your Text[/google] will produce', '[google]Your Text[/google]'",
                           "'[yahoo]','yahoo','[yahoo]Your Text[/yahoo] will produce', '[yahoo]Your Text[/yahoo]'",
                           "'[msn]','msn','[msn]Your Text[/msn] will produce', '[msn]Your Text[/msn]'",
                           "'[dictionary]','dictionary','[dictionary]Your Text[/dictionary] will produce', '[dictionary]Your Text[/dictionary]'",
                           "'[wiki]','wiki','[wiki]Your Text[/wiki] will produce', '[wiki]Your Text[/wiki]'",
                           "'[thesaurus]','thesaurus','[thesaurus]Your Text[/thesaurus] will produce', '[thesaurus]Your Text[/thesaurus]'",
                           "'[code]','code','[code]Your Text[/code] will produce', '[code]Your Text[/code]'",
                           "'[quote]','quote','[quote]Your Text[/quote] will produce', '[quote]Your Text[/quote]'",
                           "'[you]','you','[you] will produce', 'The viewers name, likeso: [you]'");

            foreach ($codes as $code) {
                // Get next ID in table
                $nextId = $dbconn->GenId($table);
                $query = "INSERT INTO $table VALUES ($nextId,$code)";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
            }
            xarRegisterMask('OverviewBBCode','All','bbcode','All','All','ACCESS_OVERVIEW');
            break;

        case '2.0.0':
            //fall through
            //3rd point upgrade to signify update of bbcode parser to 0.3.3
            //Also signifies upgrade of syntax highlighter to 1.5.1
        case '2.0.1':
            //3rd point upgrade to signify update of transform API
        case '2.0.2': //current version
        
    }
    return true;
}

function bbcode_delete() 
{
    // Drop all ModVars
    xarModDelAllVars('bbcode');
    xarRemoveMasks('bbcode');
    xarRemoveInstances('bbcode');

    // Drop the table
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['bbcode'];
    $query = xarDBDropTable($table);
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                             'transform',
                             'API',
                             'bbcode',
                             'user',
                             'transform')) {
        $msg = xarML('Could not un-register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formaction',
                           'GUI',
                           'bbcode',
                           'user',
                           'formaction')) {
        $msg = xarML('Could not un-register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formdisplay',
                           'GUI',
                           'bbcode',
                           'user',
                           'formdisplay')) {
        $msg = xarML('Could not un-register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Remove module hooks
    if (!xarModUnregisterHook('item',
                           'formarea',
                           'GUI',
                           'bbcode',
                           'user',
                           'formarea')) {
        $msg = xarML('Could not un-register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Deletion successful
    return true;
}
?>