<?php
/**
 * SiteContact Form Block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Modify sitecontact block settings
 */
function sitecontact_sitecontactblock_modify($blockinfo)
{

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if (!isset($vars['formchoice'])) {
        $vars['formchoice'] = xarModVars::get('sitecontact','defaultform');
    }

    if (!isset($vars['showdd'])) {
        $vars['showdd'] = false;
    }

    $vars['formtypes'] = xarModAPIFunc('sitecontact', 'user', 'getcontacttypes');

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * Update sitecontact block settings
 */
function sitecontact_sitecontactblock_update($blockinfo)
{
    $defaultformid = xarModVars::get('sitecontact','defaultform');
    if (!xarVarFetch('showdd', 'checkbox', $vars['showdd'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('formchoice', 'id', $vars['formchoice'],$defaultformid, XARVAR_NOT_REQUIRED)) {return;}

   $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>