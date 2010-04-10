<?php
/**
 * Admin Configuration function
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2009 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
*/
function tinymce_adminapi_loadconfig($args)
{
    extract($args);
//    if (!xarSecurityCheck('ReadTinyMCE',0)) return;

    $data=array();
    $defaultconfig = xarModGetVar('tinymce','default');
    $config = isset($configname) && !empty($configname)?$configname:$defaultconfig;
    $configarray = xarModAPIFunc('tinymce','user','getall',array('name'=>$config));
    $data['usewysiwyg'] = 0;

    if (is_array($configarray)) {
        $config = current($configarray);
        $options = unserialize($config['options']);
        $data['configname'] = $config['name'];

        $browsers = explode(',',$options['browsers']);      
        //work out whether we need to show the editor - better to do it here instead of the template
        $useragent = xarServerGetVar('HTTP_USER_AGENT');
        if ($config['active'] ==1) { //if it is the default it should be
             foreach($browsers as $browsername) {
                 if (preg_match('/'.$browsername.'/i',$useragent)) {
                    $usewysiwyg = 1;
                 }
             }
            $data['jsstring'] = $config['jsstring'];
            $data['gzstring'] = $config['gzstring'];
            $data['usegzp'] = $options['usegzp'];   
            $data['usebutton'] = $options['useswitch']; 
            $data['editor_selector'] = isset($options['editor_selector'])?$options['editor_selector']:'mceEditor';
            $data['editor_deselector'] = isset($options['editor_deselector'])?$options['editor_deselector']:'mceNoEditor';
            $data['usebutton'] = $options['useswitch'];
            $data['usewysiwyg'] = isset($usewysiwyg)?$usewysiwyg:0;
        }  
    } else {
        $data['usewysiwyg'] = 1;
        $data['editor_selector']= 'mceEditor';
        $data['editor_deselector']= 'mceNoEditor';
        $data['usebutton'] = 0;
    }

    return $data;
}
?>