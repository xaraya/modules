<?php
/**
 * xarTinyMCE initialization
 *
 * @package modules
 * @copyright (C) 2004-2010 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Initialise the tinymce module
 *
 * @access public
 * @param none
 * @returns bool true
 */
function tinymce_init()
{
    xarDBLoadTableMaintenanceAPI();
    $dbconn = xarDBGetConn();
    $xartables = xarDBGetTables();

    $tinymcetable = $xartables['tinymce'];        
    //create the configuration settings table
    $fields = array('xar_id'        => array('type' => 'integer', 'null' => false,  'increment' => true, 'primary_key' => true),
                    'xar_name'      => array('type' => 'varchar', 'size' => 32,     'null' => false,     'default'=>''),
                    'xar_desc'      => array('type' => 'varchar', 'size' => 255,    'null' => false,     'default'=>''),
                    'xar_jsstring'  => array('type' => 'text',    'size'=>'medium'),
                    'xar_gzstring'  => array('type' => 'text',    'size'=>'medium'),
                    'xar_conftype'  => array('type' => 'integer', 'size'=>'small',  'default'=>'0'),
                    'xar_options'   => array('type' => 'text',    'size'=>'medium'),
                    'xar_active'    => array('type' => 'integer', 'size'=>'small', 'default'=>'0')
    );
    $query = xarDBCreateTable($tinymcetable,$fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result =& $dbconn->Execute($query);
    if (!$result) return;    
        
    $baseurl = xarServerGetBaseURL();
    
    $default ='a:22:{s:5:"iname";s:7:"default";s:4:"desc";s:21:"Default configuration";s:6:"active";b:1;s:9:"useswitch";b:1;s:8:"autoload";b:1;s:6:"usegzp";b:1;s:8:"browsers";s:23:"msie,gecko,opera,safari";s:17:"editor_deselector";s:11:"mceNoEditor";s:4:"mode";s:9:"textareas";s:5:"theme";s:8:"advanced";s:8:"language";s:2:"en";s:7:"plugins";a:11:{i:0;s:8:"advimage";i:1;s:7:"advlink";i:2;s:8:"emotions";i:3;s:10:"fullscreen";i:4;s:10:"loremipsum";i:5;s:9:"pagebreak";i:6;s:5:"paste";i:7;s:5:"print";i:8;s:13:"searchreplace";i:9;s:12:"spellchecker";i:10;s:5:"table";}s:17:"document_base_url";s:25:"http://test3.macahaa.net/";s:16:"invalid_elements";s:0:"";s:27:"theme_advanced_blockformats";s:58:"p,address,pre,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp";s:31:"theme_advanced_toolbar_location";s:3:"top";s:33:"theme_advanced_statusbar_location";s:6:"bottom";s:27:"theme_advanced_buttons1_add";s:47:"search,replace,pastetext,pasteword,spellchecker";s:27:"theme_advanced_buttons2_add";s:35:"print,fullscreen,emotions,pagebreak";s:27:"theme_advanced_buttons3_add";s:34:"liststyle,tablecontrols,loremipsum";s:23:"theme_advanced_resizing";b:1;s:6:"custom";s:0:"";}';
    
    $jsstring = '
browsers: "msie,gecko,opera,safari",
editor_deselector: "mceNoEditor",
mode: "textareas",
theme: "advanced",
plugins: "advimage,advlink,advlist,emotions,fullscreen,loremipsum,pagebreak,paste,print,searchreplace,spellchecker,table",
invalid_elements: "",
theme_advanced_blockformats: "p,address,pre,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
theme_advanced_toolbar_location: "top",
theme_advanced_statusbar_location: "bottom",
theme_advanced_buttons1_add: "search,replace,pastetext,pasteword,spellchecker",
theme_advanced_buttons2_add: "print,fullscreen,emotions,pagebreak",
theme_advanced_buttons3_add: "tablecontrols,loremipsum",
theme_advanced_resizing: true,
language: "en"';
    $jsstring = addslashes($jsstring);
    $gzstring ='
theme : "advanced",
plugins : "advimage,advlink,advlist,emotions,fullscreen,loremipsum,pagebreak,paste,print,searchreplace,spellchecker,table",
disk_cache : true, 
language :  "en"';
                
    $gzstring = addslashes($gzstring);
    //insert a default instance config
    $nextId = $dbconn->GenId($tinymcetable);
    $query = "INSERT INTO $tinymcetable
                         (xar_id, xar_name,xar_desc, xar_jsstring, xar_gzstring, xar_conftype, xar_options, xar_active)
                  VALUES (?, 'default','Default tinymce configuration','$jsstring','$gzstring',0,'$default',1)";
    $result = $dbconn->Execute($query,array($nextId));
    if (!isset($result)) return;
    
    /* Set default module vars */
    xarModSetVar('tinymce', 'activetinymce',false);
    xarModSetVar('tinymce', 'default','default');  
    xarModSetVar('tinymce', 'tinyloadmode','auto');   
    xarModSetVar('tinymce', 'itemsperpage', 30);
    xarModSetVar('tinymce', 'useadvanced', false);    
    xarModSetVar('tinymce','defaultconfig',$default);//original default
    //setup the main array of config values
    include 'modules/tinymce/xarincludes/configarray.php';
    
    $configs = serialize($configs);
    xarModSetVar('tinymce','configs',$configs);
    
    //setup the main array of button values
    include 'modules/tinymce/xarincludes/buttonarray.php';
    
    $buttons = serialize($buttonarray);
    xarModSetVar('tinymce','buttons',$buttons);    
    
    /* let's set a default button switch  */
    $buttonon = xarML('Turn On');
    $buttonoff = xarML('Turn Off');
    $buttonswitch  = '
        function mce_button_toggle(form_element_id, button_o) 
        {
            if (tinyMCE.activeEditor == null) {
                load_tinymce();
                button_o.value = "'.$buttonoff.'";
            } else {
                if (tinyMCE.get(form_element_id).isHidden()) {
                    tinyMCE.get(form_element_id).show();
                    button_o.value = "'.$buttonoff.'";
                } else {
                    tinyMCE.get(form_element_id).hide();
                    button_o.value = "'.$buttonon.'";
                }
            }
          return false;
        }';
    xarModSetvar('tinymce','buttonstring',$buttonswitch);
    
    //Define instances for masks
    $query1 = "SELECT DISTINCT xar_id, xar_name FROM  $tinymcetable";
            $instances = array(
                        array('header' => 'Instance ID:',
                                'query' => $query1,
                                'limit' => 20
                            )
                    );
            xarDefineInstance('tinymce', 'Instance', $instances);    
   
    /* Set masks */
    xarRegisterMask('ViewTinyMCE','All','tinymce','instance','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadTinyMCE','All','tinymce','instance','All','ACCESS_READ');
    xarRegisterMask('EditTinyMCE','All','tinymce','instance','All','ACCESS_EDIT');
    xarRegisterMask('AddTinyMCE','All','tinymce','instance','All','ACCESS_ADD');
    xarRegisterMask('DeleteTinyMCE ','All','tinymce','instance','All','ACCESS_DELETE');
    xarRegisterMask('AdminTinyMCE','All','tinymce','instance','All','ACCESS_ADMIN');

    return tinymce_upgrade('3.0.0');
}

/**
 * Separate activation routines if necessary
 *
 * @access public
 * @param none $
 * @returns bool
 */
function tinymce_activate()
{
    /* Activate successful */
    return true;
}

/**
 * Upgrade the tinymce module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function tinymce_upgrade($oldversion)
{

    switch ($oldversion) {
        // Upgrading from 3.0.0
        case '3.0.0':
            
            /*
            // BETA version upgrade.
            // Add the active field to the database table
            $dbconn =& xarDBGetConn();
            $xartables =& xarDBGetTables();
            $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
            $tinymcetable = $xartables['tinymce'];
            $active_field = 'xar_active SMALLINT DEFAULT 0';
            $result = $datadict->addColumn($tinymcetable, $active_field);
            if (!$result) return FALSE;
            
            // Assume all current instances are active...
            $iconfigs = xarModAPIFunc('tinymce', 'user', 'getall');
            foreach ($iconfigs as $name => $options) {
                $options['active'] = 1;
                if (!xarModAPIFunc('tinymce', 'admin', 'update', $options)) return false;
            }
            */

            // Update the config array to include autoloading
            include 'modules/tinymce/xarincludes/configarray.php';
            $configs = serialize($configs);
            xarModSetVar('tinymce','configs',$configs);

            // The toggle button needs to run the init method if TinyMCE hasn't been initialized on page load
            $buttonon = xarML('Turn On');
            $buttonoff = xarML('Turn Off');
            $buttonswitch  = '
                function mce_button_toggle(form_element_id, button_o) 
                {
                    if (tinyMCE.activeEditor == null) {
                        load_tinymce();
                        button_o.value = "'.$buttonoff.'";
                    } else {
                        if (tinyMCE.get(form_element_id).isHidden()) {
                            tinyMCE.get(form_element_id).show();
                            button_o.value = "'.$buttonoff.'";
                        } else {
                            tinyMCE.get(form_element_id).hide();
                            button_o.value = "'.$buttonon.'";
                        }
                    }
                  return false;
                }';
            xarModSetvar('tinymce','buttonstring',$buttonswitch);
            
        case '3.0.1':
        case '3.0.2': 
        case '3.0.3': // version update to reflect updated code, no db changes
        xarModAPIFunc('tinymce','admin','resetdefault',array('resettype'=>'configs'));
        case '3.0.4': // version update to reflect updated code, no db changes
       break;
    }
    return true;
}

/**
 * Delete the tinymce module
 *
 * @access public
 * @param none $
 * @returns bool true
 */
function tinymce_delete()
{
    xarDBLoadTableMaintenanceAPI();    
    $dbconn = xarDBGetConn();
    $xartables = xarDBGetTables();
    $tinymcetable = $xartables['tinymce'];
    
    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartables['tinymce']);
    if (empty($query)) return; // throw back
    
    xarModDelAllVars('tinymce');

    // Remove Masks and Instances
    xarRemoveMasks('tinymce');
    xarRemoveInstances('tinymce');
    return true;
}

?>