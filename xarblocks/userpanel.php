<?php
/**
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispbb module
 * @link http://www.xaraya.com/index.php/release/970.html
 * @author
 */
/**
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Block init
 */
function crispbb_userpanelblock_init()
{
    return array(
        /* block defaults */
        'showusername' => true,
        'showaccount' => true,
        'showtimenow' => true,
        'showlastvisit' => true,
        'showthisvisit' => true,
        'showtotalvisit' => true,
        'showlogout' => true,
        /* set some defaults for block caching */
        'nocache' => 1, // cache by default, 0 = yes, 1 = no
        'pageshared' => 0, // share across pages? 0 = no, 1 = yes
        'usershared' => 0, // share across group members? 0 no, 1 = yes
        'cacheexpire' => 1 // default refresh
    );
}
/**
 * Block info array
 */
function crispbb_userpanelblock_info()
{
    return array(
        'text_type' => 'userpanel', // block name
        'text_type_long' => 'userpanel', // block display name
        'module' => 'crispbb', // module block belongs to
        // your block may have no config options in which case you can omit 'func_update'
        'func_update' => 'crispbb_userpanelblock_insert', // block update function
        'allow_multiple' => true, // specify if there can be multiple instances of this block
        'form_content' => false, // specify if this block has a form
        'form_refresh' => false, // specify form refresh
        'show_preview' => true // show preview
    );
}
/**
 * Display func.
 * Displays the block based on settings stored in $blockinfo
 * Outputs to userpanel.xd template
 * @param $blockinfo array containing title,content
**/
function crispbb_userpanelblock_display($blockinfo)
{

    if (!xarSecurityCheck('ReadCrispBBBlock', 0, 'Block', $blockinfo['name'])) {return;}

    if (!xarUserIsLoggedIn()) return;

    $defaults = crispbb_userpanelblock_init();

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $blockinfo['content'] = '';

    $data = array();
    $data['showusername'] = isset($vars['showusername']) ? $vars['showusername'] : $defaults['showusername'];
    $data['showaccount'] = isset($vars['showaccount']) ? $vars['showaccount'] : $defaults['showaccount'];
    $data['showtimenow'] = isset($vars['showtimenow']) ? $vars['showtimenow'] : $defaults['showtimenow'];
    $data['showlastvisit'] = isset($vars['showlastvisit']) ? $vars['showlastvisit'] : $defaults['showlastvisit'];
    $data['showthisvisit'] = isset($vars['showthisvisit']) ? $vars['showthisvisit'] : $defaults['showthisvisit'];
    $data['showtotalvisit'] = isset($vars['showtotalvisit']) ? $vars['showtotalvisit'] : $defaults['showtotalvisit'];
    $data['showlogout'] = isset($vars['showlogout']) ? $vars['showlogout'] : $defaults['showlogout'];

    $now = time();
    if (xarVarIsCached('Blocks.crispbb', 'tracking')) {
        $tracking = xarVarGetCached('Blocks.crispbb', 'tracking');
    } else {
        $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
        xarModDelUserVar('crispbb', 'tracking');
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $data['uid'] = xarUserGetVar('uid');
    $data['name'] = xarUserGetVar('name');
    if ($data['showlastvisit']) {
    $data['lastvisit'] = $tracking[0]['lastvisit'];
    }
    if ($data['showthisvisit']) {
    $data['visitstart'] = $tracking[0]['visitstart'];
    }
    if ($data['showtotalvisit']) {
    $data['totalvisit'] = $tracking[0]['totalvisit'];
    }
    if ($data['showtimenow']) {
        $data['timenow'] = $now;
    }
    $blockinfo['content'] = $data;

    return $blockinfo;
}
/**
 * Modify Function to the Blocks Admin
 * This displays the blocks current settings in Modify Block Instance function
 * Outputs to userpanel-modify.xd template
 * @param $blockinfo array containing title,content
 */
function crispbb_userpanelblock_modify($blockinfo)
{

    $defaults = crispbb_userpanelblock_init();

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $data = array();
    $data['showusername'] = isset($vars['showusername']) ? $vars['showusername'] : $defaults['showusername'];
    $data['showaccount'] = isset($vars['showaccount']) ? $vars['showaccount'] : $defaults['showaccount'];
    $data['showtimenow'] = isset($vars['showtimenow']) ? $vars['showtimenow'] : $defaults['showtimenow'];
    $data['showlastvisit'] = isset($vars['showlastvisit']) ? $vars['showlastvisit'] : $defaults['showlastvisit'];
    $data['showthisvisit'] = isset($vars['showthisvisit']) ? $vars['showthisvisit'] : $defaults['showthisvisit'];
    $data['showtotalvisit'] = isset($vars['showtotalvisit']) ? $vars['showtotalvisit'] : $defaults['showtotalvisit'];
    $data['showlogout'] = isset($vars['showlogout']) ? $vars['showlogout'] : $defaults['showlogout'];
    $data['blockid'] = $blockinfo['bid'];
     // Just return the template variables.
    return $data;
}
/**
 * Updates the Block config from the Blocks Admin
 * This function accepts input from the form in userpanel-modify.xd
 * @param $blockinfo array containing title,content
 */
function crispbb_userpanelblock_insert($blockinfo)
{

    $defaults = crispbb_userpanelblock_init();

    $vars = array();
    if (!xarVarFetch('showusername', 'checkbox', $vars['showusername'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showaccount', 'checkbox', $vars['showaccount'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtimenow', 'checkbox', $vars['showtimenow'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showlastvisit', 'checkbox', $vars['showlastvisit'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showthisvisit', 'checkbox', $vars['showthisvisit'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtotalvisit', 'checkbox', $vars['showtotalvisit'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showlogout', 'checkbox', $vars['showlogout'], false, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>