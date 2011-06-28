<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 */
function content_user_main()
{

    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;} 

    $data['seccheck'] = FALSE;

    if (!xarSecurityCheck('ViewContent')) {
        return;
    } else {
        $data['seccheck'] = TRUE;
    }

    // first see if the $pagetpl is set explicitly for this call
    if (isset($page_template) && !empty($page_template)) {   
        xarTplSetPageTemplateName($page_template);
    } else { 
        $pagetpl = xarModVars::get('content','default_main_page_tpl');  
        if (empty($pagetpl)) $pagetpl = 'default';
        xarTplSetPageTemplateName($pagetpl);
    }

    // Return the template variables defined in this function
    return $data;

}

?>