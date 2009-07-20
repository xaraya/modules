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
 * @author xardev@invalid.tld
 * @todo make me useful :@)
 */
/**
 * Block init - holds defaults.
 * these are the settings your block will use when a new instance of it is being added.
 * they are also the settings each function uses as defaults when no setting was found.
 * this prevents blocks breaking and throwing ugly errors because of missing settings
 * not required but good practice, because you only have to change the defaults once
 * in this function and they change for all the other functions
 */
function crispbb_topitemsblock_init()
{
    return array(
        /* block defaults */
        'numitems' => 5,
        'fids' => array(),
        'sort' => 'ptime',
        'order' => 'DESC',
        /* set some defaults for block caching */
        'nocache' => 0, // cache by default, 0 = yes, 1 = no
        'pageshared' => 1, // share across pages? 0 = no, 1 = yes
        'usershared' => 1, // share across group members? 0 no, 1 = yes
        'cacheexpire' => 3600 // default refresh
    );
}
/**
 * Block info array
 */
function crispbb_topitemsblock_info()
{
    return array(
        'text_type' => 'topitems', // block name
        'text_type_long' => 'topitems', // block display name
        'module' => 'crispbb', // module block belongs to
        // your block may have no config options in which case you can omit 'func_update'
        'func_update' => 'crispbb_topitemsblock_insert', // block update function
        'allow_multiple' => true, // specify if there can be multiple instances of this block
        'form_content' => false, // specify if this block has a form
        'form_refresh' => false, // specify form refresh
        'show_preview' => true // show preview
    );
}
/**
 * Display func.
 * Displays the block based on settings stored in $blockinfo
 * Outputs to topitems.xd template
 * @param $blockinfo array containing title,content
**/
function crispbb_topitemsblock_display($blockinfo)
{

    if (!xarSecurityCheck('ReadCrispBBBlock', 0, 'Block', $blockinfo['name'])) {return;}

    $defaults = crispbb_topitemsblock_init();
    $forums = xarModAPIFunc('crispbb', 'user', 'getitemlinks');
    $defaults['fids'] = !empty($forums) && is_array($forums) ? array_keys($forums) : array();
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $blockinfo['content'] = '';

    $sorts = array();
    $sorts['ptime'] = array('id' => 'ptime', 'name' => xarML('Last post time'));
    $sorts['numhits'] = array('id' => 'numhits', 'name' => xarML('Number of hits'));
    if (xarModIsAvailable('ratings')) {
        //$sorts['numratings'] = array('id' => 'numratings', 'name' => xarML('Rating'));
    }

    $fids = empty($vars['fids']) || !is_array($vars['fids']) ? $defaults['fids'] : $vars['fids'];
    $sort = empty($vars['sort']) || empty($sorts[$vars['sort']]) ? $defaults['sort'] : $vars['sort'];
    $order = empty($vars['order']) ? $defaults['order'] : $vars['order'];
    $numitems = empty($vars['numitems']) || !is_numeric($vars['numitems']) ? $defaults['numitems'] : $vars['numitems'];
    $topics = xarModAPIFunc('crispbb', 'user', 'gettopics',
        array(
            'fid' => $fids,
            'sort' => $sort,
            'order' => $order,
            'tstatus' => array(0,1,2,4),
            'numitems' => $numitems
        ));



    $blockinfo['content'] = array(
        'topics'  => $topics,
    );

    return $blockinfo;
}
/**
 * Modify Function to the Blocks Admin
 * This displays the blocks current settings in Modify Block Instance function
 * Outputs to topitems-modify.xd template
 * @param $blockinfo array containing title,content
 */
function crispbb_topitemsblock_modify($blockinfo)
{

    $defaults = crispbb_topitemsblock_init();
    $forums = xarModAPIFunc('crispbb', 'user', 'getitemlinks');
    $defaults['fids'] = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $sorts = array();
    $sorts['ptime'] = array('id' => 'ptime', 'name' => xarML('Last post time'));
    $sorts['numhits'] = array('id' => 'numhits', 'name' => xarML('Number of hits'));
    if (xarModIsAvailable('ratings')) {
        //sorts['numratings'] = array('id' => 'numratings', 'name' => xarML('Rating'));
    }

    $vars['forumoptions'] = $forums;
    $vars['fids'] = empty($vars['fids']) || !is_array($vars['fids']) ? $defaults['fids'] : $vars['fids'];
    //$cids = empty($vars['cids']) || !is_array($vars['cids']) ? $defaults['cids'] : $vars['cids'];
    $vars['sort'] = empty($vars['sort']) || empty($sorts[$vars['sort']]) ? $defaults['sort'] : $vars['sort'];
    $vars['order'] = empty($vars['order']) ? $defaults['order'] : $vars['order'];
    $vars['numitems'] = empty($vars['numitems']) || !is_numeric($vars['numitems']) ? $defaults['numitems'] : $vars['numitems'];
    $presets = xarModAPIFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'sortorderoptions'));
    $vars['sortoptions'] = $sorts;
    $vars['orderoptions'] = $presets['sortorderoptions'];
    $vars['blockid'] = $blockinfo['bid'];
     // Just return the template variables.
    return $vars;
}
/**
 * Updates the Block config from the Blocks Admin
 * This function accepts input from the form in topitems-modify.xd
 * @param $blockinfo array containing title,content
 */
function crispbb_topitemsblock_insert($blockinfo)
{

    $defaults = crispbb_topitemsblock_init();
    $forums = xarModAPIFunc('crispbb', 'user', 'getitemlinks');
    $defaults['fids'] = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

    $vars = array();

    if (!xarVarFetch('numitems', 'int:1', $vars['numitems'], $defaults['numitems'], XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('fids', 'list', $vars['fids'], $defaults['fids'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sort', 'enum:ptime:numhits:numratings', $vars['sort'], $defaults['sort'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'enum:ASC:DESC:asc:desc', $vars['order'], $defaults['order'], XARVAR_NOT_REQUIRED)) return;


    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>