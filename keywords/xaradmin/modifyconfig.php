<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * Update the configuration parameters of the module based on data from the modification form
 * 
 * @author mikespub
 * @access public 
 * @param $restricted -
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function keywords_admin_modifyconfig()
{ 
    if (!xarVarFetch('restricted', 'int', $restricted, $restricted, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useitemtype', 'int', $useitemtype, $useitemtype, XARVAR_NOT_REQUIRED)) return;
    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();
    
    if (isset($restricted)) {
        $data['restricted'] = $restricted;
    } else {
    $data['restricted'] = xarModGetVar('keywords','restricted');
    }
    
    if (isset($useitemtype)) {
        $data['useitemtype'] = $useitemtype;
    } else {
    $data['useitemtype'] = xarModGetVar('keywords','useitemtype');
    }
    
    
    $data['settings'] = array();
    $keywords = xarModAPIFunc('keywords',
                              'admin',
                              'getwordslimited',
                              array('moduleid' => '0'));


    // $keywords = xarModGetVar('keywords','default');
    if ($data['useitemtype']== 0) {
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'keywords' => $keywords);
    } else {
    $data['settings']['default'][0] = array('label' => xarML('Default configuration'),
                                            'keywords' => $keywords);
    }

    $hookedmodules = xarModAPIFunc('modules',
                                   'admin',
                                   'gethookedmodules',
                                   array('hookModName' => 'keywords'));

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            
            if ($data['useitemtype']== 1) {
                $modules[$modname] = xarModAPIFunc($modname,'user','getitemtypes',array(), 0);
                if (!isset($modules[$modname])) {  
                    $modules[$modname][0]['label']= $modname; 
                 }
                foreach ($modules as $mod => $v1) {
                    foreach ($v1 as $itemtype => $item) {
                        foreach ($item as $k3 => $v3) {
                            $moduleid = xarModGetIDFromName($mod,'module');
                         $keywords = xarModAPIFunc('keywords',
                                                   'admin',
                                                   'getwordslimited',
                                                   array('moduleid' => $moduleid,
						   	 'itemtype' => $itemtype));
                            if ($itemtype == 0) {                          
                                $link = xarModURL($mod,'user','main');
                    } else {
                                $link = xarModURL($mod,'user','view',array('itemtype' => $itemtype));
                    }
                            $label = $item['label'];
                            $data['settings'][$mod][$itemtype] = array('label' => xarML('Keywords for <a href="#(1)">#(2)</a>', $link, $label),
                                                                    'keywords'   => $keywords);
        		
  	   	}
   	   	}
                }
            } else {

                      $moduleid = xarModGetIDFromName($modname,'module');
                      $keywords = xarModAPIFunc('keywords',
                                                'admin',
                                                'getwordslimited',
                                                 array('moduleid' => $moduleid));

                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label' => xarML('Configuration for <a href="#(1)">#(2)</a> module', $link, $modname),
                                                    'keywords'   => $keywords);
            }
        }
    }
    
    $data['isalias'] = xarModGetVar('keywords','SupportShortURLs');

    if (isset($restricted)) {
        $data['restricted'] = $restricted;
    } else {
    $data['restricted'] = xarModGetVar('keywords','restricted');
    }

    $data['delimiters'] = xarModGetVar('keywords','delimiters');

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

?>
