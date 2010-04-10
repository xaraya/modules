<?php
/**
 * Admin Configuration function
 *
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2004-2010 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function tinymce_admin_manageconfigs()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;
    $data = array();
    $iconfigs = array();
    $itemvars = array();
    
    // Get the list of available configs
    $configs = xarModGetVar('tinymce','configs');
    $configarray = unserialize($configs);

    // Loop through all the input parameters and add them to $itemvars
    foreach($configarray as $category =>$config) {
        foreach($config as $item=>$value) {
            if ($value[1] =='text' || $value[1] =='dropdown' || $value[1] == 'checkboxlist' || $value[1] =='textarea') {
                $value[3] = "{$value[3]}";
            } elseif ($value[1] =='boolean') {
                $value[3]= ($value[3]=='true')? TRUE:FALSE;
            }
            if (!xarVarFetch("$item", "$value[2]", $itemvars[$item], $value[3],XARVAR_NOT_REQUIRED)) return;
        }
    }
    
    // Now get our other vars
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str:0:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('action', 'enum:new:create:modify:update:delete:confirm',  $action, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('id', 'int',  $id, NULL, XARVAR_DONT_SET)) return;
    
    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['defaultinstance'] =  xarModGetVar('tinymce','default');
    $data['menulinks'] = xarModAPIFunc('tinymce','admin','getmenulinks');    
    $data['authid'] = xarSecGenAuthKey();
    $useadvanced = xarModGetUserVar('tinymce','useadvanced');
    $data['useadvanced'] = $useadvanced;
    $itemsperpage = xarModGetVar('tinymce', 'itemsperpage');
    $numitems = xarModAPIFunc('tinymce','user','countitems');

    // Prepare options for dropdowns
    // The options in these dropdowns are determined by the files and directories present in the filesystem
    $lists = array('themes', 'langs', 'plugins');
    $skip_array = array('.','..','SCCS','CVS','.svn','.DS_Store','_MTN','index.htm','index.php','index.html','readme.txt');
    foreach($lists as $list) {
        $list_var_name = $list.'list';
        $$list_var_name = array();
        $include_path = dirname(dirname(__FILE__)) . "/xarincludes/".$list;
        $handle = opendir($include_path);
        while (false !== ($file = readdir($handle))) {
            // The skip array defines which file types not to include in the drop downs
            if (in_array($file, $skip_array)) continue;

            // Regular files need the extension trimming off
            if (is_file($include_path.'/'.$file)) {
                $extension_pos = strrpos($file, '.');
                $file = substr_replace($file, '', -($extension_pos + 1));
            }

            // Add the file to the options list
            ${$list_var_name}[$file] = $file;
        }
        closedir($handle);
    }
    
    // Setup the buttonlists
    $buttons = xarModGetVar('tinymce','buttons');
    $buttonarray = unserialize($buttons);   
    $data['buttonarray'] = $buttonarray;
     
    foreach ($buttonarray as $buttoncat => $buttons) {
        foreach($buttons as $button=>$butoptions) {
            if ($buttoncat == 'Plugins') {
                if (in_array($button,$pluginslist)) {
                    $ops = implode(', ',$butoptions);
                    $pluginslist[$button] = "<span title=\"Buttons for this plugin (add in Adv Theme button lists): $ops\">$button</span>";
                }
            
            }
        }
    }

    
    $data['buttonlist'] = implode(', ',array_keys($buttonarray['Buttons']));
    $plugininfo = '';
    foreach($buttonarray['Plugins'] as $plugin=>$plugbuttons) { 
       if (in_array($plugin, array_keys($pluginslist))) {
           $plugininfo .= $plugin;
           $plugininfo .= ' ('.implode(',  ',$plugbuttons).'), ';
       } 
    }
    $data['plugininfo'] = substr($plugininfo,0,-2); //remove last comma and space

    $data['theme_options'] = $themeslist;
    $data['language_options'] = $langslist;
    $data['plugins_options'] = $pluginslist;
    $data['directionality_options'] = array('ltr'=>xarML('Left to right'), 'rtl'=>xarML('Right to left'));
    $data['entity_encoding_options'] = array('named'=>xarML('Named'),'numeric'=>xarML('Numeric'),'raw'=>xarML('Raw'));  
    $data['theme_advanced_toolbar_location_options'] = array('top'=>xarML('Top'),'bottom'=>xarML('Bottom'),'external' =>xarML('External'));
    $data['theme_advanced_toolbar_align_options'] = array('left'=>xarML('Left'),'right'=>xarML('right'),'center'=>xarML('Center'));
    $data['theme_advanced_statusbar_location_options'] = array('none'=>xarML('None'),'top'=>xarML('Top'),'bottom'=>xarML('Bottom'));
    $data['mode_options'] = array('none'=>xarML('None'),
                            'textareas'=>xarML('All Text areas'),
                          'specific_textareas' =>xarML('Specific Text Areas'),
                          'exact'           =>xarML('Specified elements')
                         );  
    $data['dialog_type_options'] = array('window'=>xarML('Window'),'modal'=>xarML('Modal'));
    $data['theme_advanced_containers_default_align_options'] = array('left'=>xarML('Left'),'center'=>xarML('Center'),'right'=>xarML('Right'));    
    $data['theme_advanced_layout_manager_options'] = array('SimpleLayout'=>xarML('Simple Layout'), 'RowLayout'=>xarML('Row Layout'), 'CustomLayout'=> xarML('Custom layout'));   
    $data['buttonarray'] = $buttonarray;
    
    // Get all the current defined instances
    $iconfigs = xarModAPIFunc('tinymce','user', 'getall',
                            array('startnum' => $startnum,
                                  'numitems' => $itemsperpage));    
                                   
    $optionlist = array();
    
    if (!isset($action)) {
        $action = 'view';
        xarSessionSetVar('statusmsg','');
    }

    if (!isset($iid) && $action =='view') {
         xarSessionSetVar('statusmsg','');
    }

    // Add a pager for instances
    $data['pager'] = xarTplGetPager($startnum, $numitems, xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => $action, 'startnum' => '%%')), $itemsperpage);

    // We only need these for new instances - the default
    $default = $iconfigs[$data['defaultinstance']];

    $baseurl = xarServerGetBaseURL();
    
    // Prepare simple or advanced form variables and make available to add/create and modify/update processes
    foreach ($configarray  as $cat=>$configs) {
        foreach ($configs as $key=>$values) {
            if ((!isset($useadvanced) || ($useadvanced != TRUE)) && ($values[6] =='advform')) {
                unset($configarray[$cat][$key]);
            }
        }
    } 

    // Now take action as necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm') {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        // Creating or updating a configuration
        if ($action == 'create' || $action == 'update') {
            // The item options for the instance option
            $options = array(); 
            $invalid = array();
            // A name must be provided for the editor instance
            $name = trim($itemvars['iname']);
            

            // Store the instance name without spaces
            $itemvars['iname'] = str_replace(' ','_',strtolower($name));

            // Compare the $itemvars against the default config array
            // Anything marked as required, and anything different to the config array value, goes in as a new value
            foreach ($configarray as $categories => $params) {
                foreach ($params as $param => $value) {
                    // Fix the 'boolean' default vars for comparison
                    if ($value[1] == 'boolean') {
                        $comparison = ($value[3] == 'true') ? TRUE : FALSE;
                    } else {
                        $comparison = $value[3];
                    }

                    // Include it and the new value if it is required (1)
                    // Include it if the new value is different to the default conf value
                    if (($value[0] == 1) || ($itemvars[$param] != trim($comparison))) {
                        if (($value[0] == 1) && trim($itemvars[$param]) == '') {
                            $options[$param] = ($value[1] =='boolean') ? $itemvars[$param] : $comparison;
                        } elseif (($value[1] =='boolean') && ($comparison != $itemvars[$param])) {
                            $options[$param] = $itemvars[$param];
                        } else {
                            $options[$param] = $itemvars[$param];
                        }
                    }
                }
            }

            //now create the other config instance options
   
            //create the gz string
             
            $pluginstring = is_array($itemvars['plugins']) ?implode(',',$itemvars['plugins']): $itemvars['plugins'];
            if (isset($options['editor_selector'])) {
                $gzstring = '
                    theme : "'.$itemvars['theme'].'",
                    editor_selector : "'.$options['editor_selector'].'",
                    plugins : "'.$pluginstring.'",
                    disk_cache : true, 
                    language :  "'.$itemvars['language'].'"';
            } else {
                $gzstring = '
                    theme : "'.$itemvars['theme'].'",
                    plugins : "'.$pluginstring.'",
                    disk_cache : true, 
                    language :  "'.$itemvars['language'].'"';            
            }

            //create the js string;
            
            $jstring = '';
            foreach ($configarray as $categories => $params) {
                // Instance options don't affect how TinyMCE is rendered
                if ($categories == 'Instance') continue;

                foreach ($params as $param => $value) {
                    // This option parameter hasn't changed from the default, skip it
                    if (!in_array($param, array_keys($options))) continue;

                    switch ($value[1]) {
                        case 'boolean':
                            $jstring .= ($options[$param] == 1) ? "\n$param: true," : "\n$param: false,";
                            break;
                        case 'numeric':
                            $jstring .="\n$param: $options[$param],";
                            break;
                        case 'textarea':
                            if ($param == 'custom') {
                                $jstring .= $options[$param]; //just include it as it should be js already
                            } else {
                                // It is a comma separated list of values separated with whitespace for wrap
                                // We want to remove the whitespace
                                $newval = preg_replace('/[ |\s]*\s+/','',$options[$param]);
                                $jstring .="\n$param: \"$options[$param]\",";
                            }
                            break;
                        case 'checkboxlist':  
                            $newval = is_array($options[$param]) ?implode(',',$options[$param]):$options[$param];
                            $jstring .="\n$param: \"$newval\",";
                            break;                                      
                        default:
                            if ($param != 'language') {
                                $jstring .="\n$param: \"$options[$param]\",";
                            }
                    }
                }
            }

          if (empty($name)) { //handle the error now we have the values captured - name must not be empty
                xarSessionSetVar('statusmsg',xarML('You must enter a valid short name'));
                $invalid['iname'] = xarML('You must enter a unique, short Name');
                $data['authid'] = xarSecGenAuthKey();
                $data['iname'] = 'nameme';
                $data['idesc'] = '';
                $data['buttonlabel'] = xarML('Create');
                $data['managetype'] = xarML('Create Form');
                $data['link'] = xarModURL('tinymce','admin','manageconfigs', array('action' => 'create'));
                $data['invalid'] = $invalid;
                $data['action'] = $action;
                $data['iconfigs']=$iconfigs;
                $data['configarray'] = $configarray;
                $data['currentoptions'] = $options;
                $data['iconfiglink'] = xarModURL('tinymce','admin','manageconfigs');
                $data['configurls'] = array();
                $data['action'] = 'new';
                $data['newurl'] = xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'new'));
                return xarTplModule('tinymce','admin','manageconfigs',$data);
            }            
            // Finally add the language - we always know this is included and can easily ensure no trailing comma
            $jstring .= "\nlanguage: \"".$options['language']."\"";

            // Put it all together           
            $instance['gzstring'] = $gzstring;            
            $instance['name'] = $itemvars['iname'];
            $instance['desc'] = $itemvars['desc'];
            $instance['conftype'] = 0;
            $instance['options'] = serialize($options);
            $instance['jsstring'] = $jstring; //just in case some are missing in custom config
            if ($action == 'create') {
                // Let the API create the new instance
                $instance['active'] = $itemvars['active'];   
                $iconfig = xarModAPIFunc('tinymce', 'admin', 'create', $instance);    
                // If creation is successful redirect to the admin view page, otherwise display an error message
                if (isset($iconfig) && $iconfig > 0) {
                    xarSessionSetVar('statusmsg',xarML('New TinyMCE Config Instance created'));
                    xarResponseRedirect(xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'view', 'id' => $iconfig)));
                    return true;
                } else {
                    xarSessionSetVar('statusmsg',xarML('Problem with creation of new TinyMCE Config Instance'));
                    xarResponseRedirect(xarModURL('tinymce', 'admin', 'manageconfigs'));
                }
            } elseif ($action == 'update') {
                // Add the instance ID to the API parameters and update
                $instance['id'] = $id;
                $instance['active'] = $data['defaultinstance'] ==1 ?TRUE:$itemvars['active']; 

                $iconfig = xarModAPIFunc('tinymce', 'admin', 'update', $instance);
                
                // If the update is successful redirect to the admin view page, otherwise display an error message
                if (!$iconfig) {
                    xarSessionSetVar('statusmsg', xarML('Problem updating the TinyMCE Config #(1)', $instance['name']));
                    $msg = xarML('Problem updating a TinyMCE config instance with Name of #(1)', $instance['name']);
                    xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return false;
                } else {
                    xarSessionSetVar('statusmsg',xarML('TinyMCE Config updated'));
                    xarResponseRedirect(xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'modify', 'id'=>$id)));
                    return true;
                }

            }
        }
        // Confirming a deletion
        elseif ($action == 'confirm') {
    
            $item = xarModAPIFunc('tinymce', 'user', 'getall', array('id'=>$id)); 
            $data['item'] = current($item);

            // Can't delete the default configuration instance
            if ($data['item']['name'] == $data['defaultinstance']) {
                $msg = xarML('You cannot delete the default config. Please change the default first');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return false;
            }

            // If the deletion fails display an error, otherwise redirect to the config management page
            if (!xarModAPIFunc('tinymce', 'admin', 'delete', array('id' => (int)$id))) {
                $msg = xarML('Problem deleting a TinyMCE Config with id #(1)',$id);
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return false;
            } else {
                xarSessionSetVar('statusmsg', xarML('TinyMCE Config Instance deleted'));
                xarResponseRedirect(xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'view')));
                return true;
            }
        }
    }

    // Create Edit/Delete links
    $totalinstances = count($iconfigs);
    $configurls = array();

    if ($totalinstances > 0) {
        foreach ($iconfigs as $name => $iconfig) {
             $iconfigs[$iconfig['name']]['options'] = unserialize($iconfig['options']);
            // Generate an edit URL if the user has the right privileges
            if (xarSecurityCheck('EditTinyMCE',0)) {
                $configurls[$iconfig['id']]['editurl'] = xarModURL('tinymce','admin','manageconfigs', array('id' => (int)$iconfig['id'], 'action' => 'modify'));
            } else {
                $configurls[$iconfig[$id]]['editurl'] = '';
            }

            // Generate a delete URL if... 
            // the user has the right privileges, 
            // it's not the only instance left, 
            // or it's not the default
            if (xarSecurityCheck('DeleteTinyMCE',0) && ($totalinstances > 1) && ($iconfig['name'] != $default['name']) && ($iconfig['name'] !='default')) {
                $configurls[$iconfig['id']]['deleteurl'] = xarModURL('tinymce','admin','manageconfigs', array('id' => (int)$iconfig['id'], 'action' => 'delete'));
            } else {
                $configurls[$iconfig['id']]['deleteurl'] = '';
            }
        }
    }

    $data['configurls'] = $configurls;
    $data['newurl'] = xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'new'));

    // Fill in relevant variables
    if ($action == 'new') {
        xarSessionSetVar('statusmsg','');
        $data['authid'] = xarSecGenAuthKey();
        $data['iname'] = 'nameme';
        $data['idesc'] = '';
        $data['buttonlabel'] = xarML('Create');
        $data['managetype'] = xarML('Create Form');
        $data['currentoptions'] = array();
        $data['link'] = xarModURL('tinymce','admin','manageconfigs', array('action' => 'create'));
    } elseif ($action == 'modify') {
        xarSessionSetVar('statusmsg','');
        $items = xarModAPIFunc('tinymce','user','getall',array('id'=>$id));
        $item= current($items);
        $currentname = $item['name'];
        
        // Prepare for display and make sure we expand $baseurl in case it hasn't already been
        $currentoptions= str_ireplace("\$baseurl","$baseurl",$item['options']);
        $currentoptions= unserialize($currentoptions);
          
        // Prepare the js string for display of current configuration
        $jsstring = str_replace("\$baseurl", "$baseurl", $item['jsstring']);
        $stringstart = 'tinyMCE.init({';
        $stringend = ' });';
        $data['jsstrings'] = "";
        $data['jsstrings'] = $stringstart;
        $data['jsstrings'] .= stripslashes($jsstring);
        $data['jsstrings'] .="\n".$stringend;
        $data['currentoptions'] = $currentoptions;
        $data['id'] = $id;
        $data['returnurl'] = xarServerGetCurrentURL();
        $data['manageconfig'] = xarML('Edit Configuration Instance');
        $data['icactive'] = xarModGetVar('tinymce', 'icactive') ? true : false;
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('tinymce', 'admin', 'manageconfigs', array('action' => 'update'));
        $data['isdefault'] = ($currentname == $data['defaultinstance']) ? TRUE : FALSE;

     } elseif ($action == 'delete') {
        xarSessionSetVar('statusmsg','');

        $items = xarModAPIFunc('tinymce','user','getall', array('id'=> $id));

        if (is_array($items) && count($item) == 1) {
            $item = current($items);
        } else {
            // There is something wrong - the item doesn't exist
            $msg = xarML('There has been an error. Please contact the system administrator and inform them of this error message.');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return false;
        }

        if ($item['name'] == $default) {
            $msg = xarML('You cannot delete the default configuration. Please change the default config first');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
            return false;
        }

        $data['item'] = $item;
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['managetype'] = xarML('Delete Configuration Instance');
        $data['numitems'] = xarModAPIFunc('tinymce','user','countitems');
        $data['link'] = xarModURL('tinymce','admin','manageconfigs', array('action' => 'confirm'));
    }    
    
    $data['action'] = $action;
    $data['iconfigs']=$iconfigs;
    $data['configarray'] = $configarray;
    $data['iconfiglink'] = xarModURL('tinymce','admin','manageconfigs');   

    $hooks = xarModCallHooks('module', 'modifyconfig', 'tinymce', array('module' => 'tinymce'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
