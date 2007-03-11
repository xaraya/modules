<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Initialize shoutblock
 *
 * @return array
 */
function shouter_shoutblockblock_init()
{
    return array(
        'numitems'          => 5,
//        'blockwidth'        => 180,
//        'blockwrap'         => 19,
        'allowsmilies'      => true,
//        'lightrow'          => 'FFFFFF',
//        'darkrow'           => 'E0E0E0',
        'shoutblockrefresh' => 0,
        'anonymouspost'     => false,
    );
}

/**
 * Get information about the shoutblock
 *
 * @return array
 */
function shouter_shoutblockblock_info()
{
    return array('text_type'      => 'Shoutblock',
                 'module'         => 'shouter',
                 'text_type_long' => 'Shoutblock',
                 'allow_multiple' => false,
                 'form_content'    => true,
                 'form_refresh'   => true,
                 'show_preview'   => false);
}

/**
 * Display shoutblock
 *
 * @param array $blockinfo
 * @return array
 */
function shouter_shoutblockblock_display($blockinfo)
{
    if (!xarSecurityCheck('ReadShouterBlock', 0, 'Block', $blockinfo['title'])) {return;}

    /* Get variables from content block.
     * Content is a serialized array for legacy support, but will be
     * an array (not serialized) once all blocks have been converted.
     */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    $items = xarModAPIFunc('shouter', 'user', 'getall',
                     array('numitems' => $vars['numitems'])
             );

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;}

    $totitems = count($items);
    for ($i = 0; $i < $totitems; $i++) {
        $item = $items[$i];
        $items[$i]['shout'] = xarVarPrepForDisplay($item['shout']);
//        $items[$i]['shout'] = wordwrap(xarVarPrepForDisplay($item['shout']), $vars['blockwrap'], "\n", 1);
    }

    $data['shouturl'] = xarModURL('shouter', 'admin', 'create',array());
    $data['rawshouturl'] = xarModURL('shouter', 'admin', 'create',array(),false);
    $data['anonymouspost'] = $vars['anonymouspost'];

//    $lightrow = xarModGetVar('shouter','lightrow');
//    $data['lightrow'] = "background:#".$vars['lightrow'].";";

//    $darkrow = xarModGetVar('shouter','darkrow');
//    $data['darkrow'] = "background:#".$vars['darkrow'].";";


//    $blockwidth = xarModGetVar('shouter','blockwidth');
//    $data['blockwidth'] = "width:".$vars['blockwidth']."px;";

    // JS won't load in the head directly from a block template
    // unless the shouter block or a blockgroup containing it
    // is called before <xar:base-render-javascript />
    // so, we stick xmlhttprequest.js in the body instead
    xarModAPIFunc('base','javascript','modulefile', array('module' => 'base', 'filename' => 'xmlhttprequest.js', 'position' => 'body'));



    $data['refresh'] = true;

    if ($vars['shoutblockrefresh'] == 0) {
        $data['refresh'] = false;
    }
    $data['shoutblockrefresh'] = $vars['shoutblockrefresh'] . '000';

    // Transform Hook for smilies
    $data['items'] = array();

    foreach ($items as $item) {
        $item['module'] = 'shouter';
        $item['itemtype'] = 0;
        $item['itemid'] = $item['shoutid'];
        $item['transform'] = array('shout');

        $item = xarModCallHooks('item', 'transform', $item['shoutid'], $item);
        // Display the content
        $data['items'][] = $item;
    }

    $data['blockurl'] = xarModURL('blocks', 'user', 'display',array('name' => $blockinfo['name']),false);
    $data['bid'] = $blockinfo['bid'];

    $requestinfo = xarRequestGetInfo();


    /**
     * Don't refresh inside of blocks admin
     * @todo: need a better way to handle whether to load the onLoad event for the timer
     */
    if (xarRequestGetVar('module') == 'blocks' && xarRequestGetVar('type') == 'admin') {
        $data['refresh'] = false;
    }
    $blockinfo['content'] = $data;

    return $blockinfo;
}
?>
