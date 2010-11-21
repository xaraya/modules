<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Timeline Block  - standard Initialization function
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array
 */
function twitter_timelineblock_init()
{
    return array(
        'screen_name'     => '',
        'numitems'        => 3,
        'truncate'        => 0,
        'showimages'      => false,
        'showmyimage'     => false,
        'showsource'      => true,
        'showmodule'      => false,
        'showfollow'      => false,
        'nocache'         => 0, /* cache by default (if block caching is enabled) */
        'pageshared'      => 1, /* share across pages */
        'usershared'      => 1, /* share across group members */
        'cacheexpire'     => null
    );
}

/**
 * Get information on block
 * @return array
 */
function twitter_timelineblock_info()
{
    /* Values */
    return array(
        'text_type' => 'Timeline',
        'module' => 'twitter',
        'text_type_long' => 'Show twitter timeline',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function twitter_timelineblock_display($blockinfo)
{

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $data = array();
    $defaults = twitter_timelineblock_init();
    
    if (empty($vars['screen_name'])) {
        $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
            array(
                'method' => 'public_timeline',
            ));
    } else {
        $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
            array(
                'method' => 'user_timeline',
                'screen_name' => $vars['screen_name'],
            ));
    }
    
    foreach ($defaults as $key => $defval) {
        if (!isset($vars[$key])) $vars[$key] = $defval;
    }    
    $data += $vars;
        
    if (count($items) > $vars['numitems']) $items = array_slice($items, 0, $vars['numitems']);
    $data['status_elements'] = !$items ? array() : $items;
       
   /* Now we need to send our output to the template.
     * Just return the template data.
     */
    $blockinfo['content'] = $data;

    return $blockinfo;
}
/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function twitter_timelineblock_modify($blockinfo)
{
    // Break out options from our content field.
    // Prepare for when content is passed in as an array.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    // Keep all the default values in one place.
    $defaults = twitter_timelineblock_init();
    foreach ($defaults as $key => $defval) {
        if (!isset($vars[$key])) $vars[$key] = $defval;
    } 
    $vars['blockid'] = $blockinfo['bid'];

    // Just return the template variables.
    return $vars;
}

/**
 * Update block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function twitter_timelineblock_update($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = twitter_timelineblock_init();
    $vars = array();
    if (!xarVarFetch('screen_name', 'str:1:20', $vars['screen_name'], $defaults['screen_name'], XARVAR_NOT_REQUIRED)) return;
    
    if (!xarVarFetch('numitems', 'int', $vars['numitems'], $defaults['numitems'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('truncate', 'int', $vars['truncate'], $defaults['truncate'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showimages', 'checkbox', $vars['showimages'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showmyimage', 'checkbox', $vars['showmyimage'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showsource', 'checkbox', $vars['showsource'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showmodule', 'checkbox', $vars['showmodule'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showfollow', 'checkbox', $vars['showfollow'], false, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;
    return $blockinfo;
}


?>