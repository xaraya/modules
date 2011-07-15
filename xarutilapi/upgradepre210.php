<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage contactform
 * @link http://xaraya.com/index.php/release/1049.html
 * @author potion <potion@xaraya.com>
 */ 
function contactform_utilapi_upgradepre210() {

    // First get the settings
    $a = xarModVars::get('contactform','to_email');
    $b = xarModVars::get('contactform','default_subject');
    $c = xarModVars::get('contactform','save_to_db');
    $d = xarModVars::get('contactform','contact_objects');
    $e = xarModVars::get('contactform','enable_short_urls'); 
    
    if (!xarMod::apiFunc('dynamicdata','util','import', array('file' =>  sys::code() . 'modules/contactform/xardata/contactform_module_settings-def.xml', 'overwrite' => true))) return;

    // Set the vars
    xarModVars::set('contactform','to_email',$a);
    xarModVars::set('contactform','default_subject',$b);
    xarModVars::set('contactform','save_to_db',$c); 
    xarModVars::set('contactform','contact_objects',$d);
    xarModVars::set('contactform','enable_short_urls',$e);

    //the new one
    xarModVars::set('contactform', 'strip_tags',true);

    return true;
    
}
?>