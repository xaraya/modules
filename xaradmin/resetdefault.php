<?php
/**
 * Admin Configuration function
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2008-2009 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Reset default
 *
 * @param str    $args['resettype']  default - configs
 * @return array Array of issues, or false on failure
 */
function tinymce_admin_resetdefault($args)
{
    // Get arguments from argument array
    extract($args);
    
    if (!xarSecurityCheck('AdminTinyMCE', 1)) {
        return;
    }
    if (!xarVarFetch('resettype',   'strlist:,:pre:lower:trim:passthru:enum:default:configs:formmode', $resettype, 'configs', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',   'str:0:255', $returnurl, '', XARVAR_NOT_REQUIRED)) return;    
    if (!xarVarFetch('useadvanced',  'checkbox',  $useadvanced,         false, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('id',     'int',  $id,         NULL, XARVAR_DONT_SET)) return;    
    
    if (!isset($resettype) || empty($resettype))  {
        $msg = xarML('Invalid #(1)  function #(2) in module #(4)',
                    'resettype','user', 'reset', 'tinymce');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    $args['resettype'] = $resettype;
    $args['useadvanced'] = $useadvanced;
    $args['id'] = $id;
    
    /*
    $defaultsettings = xarModGetVar('tinymce','defaultconfig');
    //get the list of available configs
    $configs = xarModGetVar('tinymce','configs');

    if ($resettype == 'formmode') {
        xarModSetUserVar('tinymce', 'useadvanced', $useadvanced);

    } elseif ($resettype == 'default') {
        $baseurl = xarServerGetBaseURL();
    
    $default = array(   'iname'             => 'default',
                        'desc'              => 'Default configuration',
                        'useswitch'         => 'true',
                        'usegzp'            => 'false',
                        'autoload'          => 'true',
                        'active'            => 'true',
                        'mode'              => 'textareas',
                        'theme'             =>  'advanced',
                        'document_base_url' =>  $baseurl,
                        'browsers' =>'msie,gecko,opera,safari',
                        'editor_deselector' => 'mceNoEditor',      
                        'inline_styles'             =>  'true',
                        'apply_source_formatting'   =>  'true', 
                        'remove_linebreaks'         =>  'true',
                        'theme_advanced_blockformats' =>   'p,address,pre,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp',
                        'theme_advanced_toolbar_location'   =>  'top',
                        'theme_advanced_statusbar_location' =>  'bottom',
                        'theme_advanced_resizing'           =>  'true',
                        'theme_advanced_resize_horizontal'  =>  'true',
                        'theme_advanced_path'               =>  'true',
                        'plugins'           =>   'searchreplace,print,advimage,advlink,table,paste,pagebreak,loremipsum,spellchecker,fullscreen,emotions,blockquote',        
                        'theme_advanced_buttons1_add' => 'search,replace,pastetext,pasteword,spellchecker',
                        'theme_advanced_buttons2_add' =>'print,fullscreen,emotions,pagebreak',
                        'theme_advanced_buttons3_add' =>'liststyle,tablecontrols,loremipsum',
                        'entity_encoding'=>'raw',
                        'language' => 'en' 
                        );
    $default = serialize($default);
    $jsstring = '
            browsers: "msie,gecko,opera,safari",
            editor_deselector: "mceNoEditor",
            mode: "textareas",
            theme: "advanced",
            plugins: "advimage,advlink,emotions,fullscreen,loremipsum,pagebreak,paste,print,searchreplace,spellchecker,table",
            invalid_elements: "",
            theme_advanced_blockformats: "p,address,pre,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
            theme_advanced_toolbar_location: "top",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_buttons1_add: "search,replace,pastetext,pasteword,spellchecker",
            theme_advanced_buttons2_add: "print,fullscreen,emotions,pagebreak",
            theme_advanced_buttons3_add: "liststyle,tablecontrols,loremipsum",
            theme_advanced_resizing: true,
            language: "en"';

    $instance['jsstring'] =$jsstring;
    $gzstring = '
        theme : "advanced",
        plugins : "advimage,advlink,emotions,fullscreen,loremipsum,pagebreak,paste,print,searchreplace,spellchecker,table",
        disk_cache : true, 
        language :  "en"';
    $instance['gzstring'] = $gzstring;
    $id = 1;
    $instance['id'] = $id;
    $instance['name'] = 'default';
    $instance['desc'] = 'Default configuration';
    $instance['conftype'] = 0;
    $instance['options'] = $default;
    $instance['active'] = TRUE;    
    
    $updatediconfig=xarModAPIFunc('tinymce','admin','update', $instance);    
    xarModSetVar('tinymce','defaultconfig',$default);
    } elseif ($resettype == 'configs') {//todo
        //setup the main array of config values
        include 'modules/tinymce/xarincludes/configarray.php';
    
        $configs = serialize($configs);
        xarModSetVar('tinymce','configs',$configs);
        
        //setup the main array of button values
        include 'modules/tinymce/xarincludes/buttonarray.php';
    
        $buttons = serialize($buttonarray);
        xarModSetVar('tinymce','buttons',$buttons);   
    
    }
    */
    xarModAPIFunc('tinymce','admin','resetdefault',$args);
    if (!isset($returnurl) || empty($returnurl)) {
        $returnurl = xarModURL('tinymce','admin','manageconfigs',array('action'=>'modify','id'=>1));
    }
    xarResponseRedirect($returnurl);
    return;
}

?>