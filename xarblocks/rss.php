<?php
/**
 * Displays an RSS Display.
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
 */
/**
 * Block init - holds security.
 */
function headlines_rssblock_init()
{
    return array(
        'rssurl' => '',
        'maxitems' => 5,
        'showdescriptions' => false,
        'show_chantitle' => 1,
        'show_chandesc' => 1,
        'refresh' => 3600,
        'nocache' => 0, // cache by default
        'pageshared' => 1, // don't share across pages here
        'usershared' => 1, // share across group members
        'cacheexpire' => 3601 // right after the refresh rate :-)
    );
}

/**
 * Block info array
 */
function headlines_rssblock_info()
{
    return array(
        'text_type' => 'RSS',
        'text_type_long' => 'RSS Newsfeed',
        'module' => 'headlines',
        'func_update' => 'headlines_rssblock_insert',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_display($blockinfo)
{

    // Break out options from our content field.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $blockinfo['content'] = '';

    // Check and see if a feed has been supplied to us.
    if (empty($vars['rssurl'])) {
        $blockinfo['title'] = xarML('Headlines');
        $blockinfo['content'] = xarML('No Feed URL Specified');
        return $blockinfo;
    } else {
        $feedfile = $vars['rssurl'];
    }

    if (!isset($vars['maxitems'])) {
        $vars['maxitems'] = 5;
    }
    if (!isset($vars['show_chantitle'])) {
        $vars['show_chantitle'] = 1;
    }
    if (!isset($vars['show_chandesc'])) {
            $vars['show_chandesc'] = 1;
    }

    if (empty($vars['refresh'])) {
        $vars['refresh'] = 3600;
    }


    if (xarModGetVar('headlines', 'magpie')){
        // Set some globals to bring Magpie into line with
        // site and headlines settngs.
        if (!defined('MAGPIE_OUTPUT_ENCODING')) {
            define('MAGPIE_OUTPUT_ENCODING', xarMLSGetCharsetFromLocale(xarMLSGetCurrentLocale()));
        }
        // Set the Magpie cache lower than the block cache, so we always fetch something.
        if (!defined('MAGPIE_CACHE_AGE')) {
            define('MAGPIE_CACHE_AGE', round($vars['refresh'] / 2));
        }
        $data = xarModAPIFunc('magpie',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    } else {
        $data = xarModAPIFunc('headlines',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    }

    if (!empty($data['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $info['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $data['feedcontent'] = array_slice($data['feedcontent'], 0, $vars['maxitems']);

    $blockinfo['content'] = array(
        'feedcontent'  => $data['feedcontent'],
        'blockid'      => $blockinfo['bid'],
        'chantitle'    => $data['chantitle'],
        'chanlink'     => $data['chanlink'],
        'chandesc'     => $data['chandesc'],
        'show_desc'     => $vars['showdescriptions'],
        'show_chantitle' => $vars['show_chantitle'],
        'show_chandesc'  => $vars['show_chandesc']
    );

    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_modify($blockinfo)
{
    // Break out options from our content field.
    // Prepare for when content is passed in as an array.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Migrate $row['rssurl'] to content if present
    if (!empty($vars['url'])) {
        $vars['rssurl'] = $vars['url'];
        unset($vars['url']);
    }

    // Get parameters from whatever input we need
    $vars['items'] = array();

    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'getall');
    $vars['items'] = $links;

    // Defaults
    if (!isset($vars['rssurl'])) {
        $vars['rssurl'] = '';
    }
    if (!ereg("^http://|https://|ftp://", $vars['rssurl'])) {
        $vars['rssurl'] = '';
    }
    if (!isset($vars['show_chantitle'])) {
            $vars['show_chantitle'] = 1;
    }
    if (!isset($vars['show_chandesc'])) {
            $vars['show_chandesc'] = 1;
    }
    if (!isset($vars['showdescriptions'])) {
        $vars['showdescriptions'] = 0;
    }
    if (!isset($vars['maxitems'])) {
        $vars['maxitems'] = 5;
    }
    if (!isset($vars['refresh'])) {
        $vars['refresh'] = 3600;
    }

    $vars['blockid'] = $blockinfo['bid'];

    // Just return the template variables.
    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_insert($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('rssurl', 'str:1:', $vars['rssurl'], '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('show_chantitle', 'checkbox', $vars['show_chantitle'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('show_chandesc', 'checkbox', $vars['show_chandesc'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('refresh', 'int:0', $vars['refresh'], 3600, XARVAR_NOT_REQUIRED)) {return;}

    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>
