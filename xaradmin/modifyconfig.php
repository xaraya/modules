<?php
/**
 * Admin Configuration function
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2004-2009 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * A a standard function to modify the configuration parameters of tinymce
 *
 */
function tinymce_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;
    //if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basic', XARVAR_NOT_REQUIRED)) return;
    $data = array();
    
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('tinymce','admin','getmenulinks');
    
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['activetinymce'] = xarModGetVar('tinymce','activetinymce');
    $data['defaultconfig'] = xarModGetVar('tinymce','default'); 
    $data['buttonstring'] = xarModGetvar('tinymce','buttonstring');
    $data['itemsperpage'] = xarModGetVar('tinymce','itemsperpage'); 
    $data['tinyloadmode'] = xarModGetVar('tinymce','tinyloadmode');     
    $data['loadoptions'] = array('auto'=>xarML('Automatic'),
                                 'manual' =>xarML('Manual Override')
                            );        
                               
    $data['default'] = xarModGetVar('tinymce','default');
    $configs = array();
    $configs = xarModAPIFunc('tinymce','user','getall');
    $configlist = array();

    if (count($configs) > 0) {
        foreach ($configs as $key=>$config) {
            if ($config['active'] == 1) {
                $configlist[$config['name']]= $config['name'];
            }
        }
    }
    $data['configlist']= $configlist;
    
    $hooks = xarModCallHooks('module', 'modifyconfig', 'tinymce', array('module' => 'tinymce'));
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = join('', $hooks);
        } else {
            $data['hooks'] = $hooks;
        }
    /* Return the template variables defined in this function */
    return $data;
}

?>