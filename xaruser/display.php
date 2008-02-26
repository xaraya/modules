<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Displays a comment or set of comments
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer    $args['modid']              the module id
 * @param    integer    $args['itemtype']           the item type
 * @param    string     $args['objectid']           the item id
 * @param    string     $args['returnurl']          the url to return to
 * @param    integer    $args['depth']              depth of comment thread to display
 * @param    integer    [$args['selected_cid']]     optional: the cid of the comment to view (only for displaying single comments)
 * @param    integer    [$args['thread']]           optional: display the entire thread following cid
 * @param    integer    [$args['preview']]          optional: an array containing a single (preview) comment used with adding/editing comments
 * @return   array      returns whatever needs to be parsed by the BlockLayout engine
 */
function comments_user_display($args)
{
    if (!xarSecurityCheck('Comments-Read', 0)) return;

    // check if we're coming via a hook call
    if (isset($args['objectid'])) {
        $ishooked = 1;
    } else {
        // if we're not coming via a hook call
        $ishooked = 0;
        // then check for a 'cid' parameter
        if (!empty($args['cid'])) {
            $cid = $args['cid'];
        } else {
            xarVarFetch('cid', 'int:1:', $cid, 0, XARVAR_NOT_REQUIRED);
        }
        // and set the selected cid to this one
        if (!empty($cid) && !isset($args['selected_cid'])) {
            $args['selected_cid'] = $cid;
        }
    }

    // TODO: now clean up the rest :-)

    $header   = xarRequestGetVar('header');
    $package  = xarRequestGetVar('package');
    $receipt  = xarRequestGetVar('receipt');

    // Fetch the module ID
    if (isset($args['modid'])) {
        $header['modid'] = $args['modid'];
    } elseif (isset($header['modid'])) {
        $args['modid'] = $header['modid'];
    } elseif (!empty($args['extrainfo']) && !empty($args['extrainfo']['module'])) {
        if (is_numeric($args['extrainfo']['module'])) {
            $modid = $args['extrainfo']['module'];
        } else {
            $modid = xarModGetIDFromName($args['extrainfo']['module']);
        }
        $args['modid'] = $modid;
        $header['modid'] = $modid;
    } else {
        xarVarFetch('modid', 'isset', $modid, NULL, XARVAR_NOT_REQUIRED);
        if (empty($modid)) {
            $modid = xarModGetIDFromName(xarModGetName());
        }
        $args['modid'] = $modid;
        $header['modid'] = $modid;
    }
    $header['modname'] = xarModGetNameFromID($header['modid']);

    // Fetch the itemtype
    if (isset($args['itemtype'])) {
        $header['itemtype'] = $args['itemtype'];
    } elseif (isset($header['itemtype'])) {
        $args['itemtype'] = $header['itemtype'];
    } elseif (!empty($args['extrainfo']) && isset($args['extrainfo']['itemtype'])) {
        $args['itemtype'] = $args['extrainfo']['itemtype'];
        $header['itemtype'] = $args['extrainfo']['itemtype'];
    } else {
        xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED);
        $args['itemtype'] = $itemtype;
        $header['itemtype'] = $itemtype;
    }


    $package['settings'] = xarModAPIFunc('comments','user','getoptions',$header);

    // FIXME: clean up return url handling

    $settings_uri = "&amp;depth={$package['settings']['depth']}"
        . "&amp;order={$package['settings']['order']}"
        . "&amp;sortby={$package['settings']['sortby']}"
        . "&amp;render={$package['settings']['render']}";

    // Fetch the object ID
    if (isset($args['objectid'])) {
        $header['objectid'] = $args['objectid'];
    } elseif (isset($header['objectid'])) {
        $args['objectid'] = $header['objectid'];
    } else {
        xarVarFetch('objectid','isset', $objectid, NULL, XARVAR_NOT_REQUIRED);
        $args['objectid'] = $objectid;
        $header['objectid'] = $objectid;
    }

    if (isset($args['selected_cid'])) {
        $header['selected_cid'] = $args['selected_cid'];
    } elseif (isset($header['selected_cid'])) {
        $args['selected_cid'] = $header['selected_cid'];
    } else {
        xarVarFetch('selected_cid', 'isset', $selected_cid, NULL, XARVAR_NOT_REQUIRED);
        $args['selected_cid'] = $selected_cid;
        $header['selected_cid'] = $selected_cid;
    }
    if (!isset($args['thread'])) {
        xarVarFetch('thread', 'isset', $thread, NULL, XARVAR_NOT_REQUIRED);
    }
    if (isset($thread) && $thread == 1) {
        $header['cid'] = $cid;
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException($msg));
        return;
    }


    if (!isset($header['selected_cid']) || isset($thread)) {
        $package['comments'] = xarModAPIFunc('comments','user','get_multiple',$header);
        if (count($package['comments']) > 1) {
            $package['comments'] = comments_renderer_array_sort(
                $package['comments'],
                $package['settings']['sortby'],
                $package['settings']['order']
            );
        }
    } else {
        $header['cid'] = $header['selected_cid'];
        $package['settings']['render'] = _COM_VIEW_FLAT;
        $package['comments'] = xarModAPIFunc('comments','user','get_one', $header);
        if (!empty($package['comments'][0])) {
            $header['modid'] = $package['comments'][0]['xar_modid'];
            $header['itemtype'] = $package['comments'][0]['xar_itemtype'];
            $header['objectid'] = $package['comments'][0]['xar_objectid'];
        }
    }


    $package['comments'] = comments_renderer_array_prune_excessdepth(
        array(
            'array_list'    => $package['comments'],
            'cutoff'        => $package['settings']['depth'],
            'modid'         => $header['modid'],
            'itemtype'      => $header['itemtype'],
            'objectid'      => $header['objectid'],
        )
    );

    if ($package['settings']['render'] == _COM_VIEW_THREADED) {
        $package['comments'] = comments_renderer_array_maptree($package['comments']);
    }

    // run text and title through transform hooks
    if (!empty($package['comments'])) {
        foreach ($package['comments'] as $key => $comment) {
            $comment['xar_text'] = xarVarPrepHTMLDisplay($comment['xar_text']);
            $comment['xar_title'] = xarVarPrepForDisplay($comment['xar_title']);
            // say which pieces of text (array keys) you want to be transformed
            $comment['transform'] = array('xar_text');
            // call the item transform hooks
            // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
            $package['comments'][$key] = xarModCallHooks('item', 'transform', $comment['xar_cid'], $comment, 'comments');
        }
    }

    $header['input-title']            = xarML('Post a new comment');

    $package['settings']['max_depth'] = _COM_MAX_DEPTH;
    $package['uid']                   = xarUserGetVar('uid');
    $package['uname']                 = xarUserGetVar('uname');
    $package['name']                  = xarUserGetVar('name');
    // Bug 6175: removed xarVarPrepForDisplay() from the title, as articles already
    // does this *but* maybe needs fixing in articles instead?
    $package['new_title']             = xarVarGetCached('Comments.title', 'title');

    // Let's honour the phpdoc entry at the top :-)
    if(isset($args['returnurl'])) {
        $receipt['returnurl']['raw'] = $args['returnurl'];
    }
    if (empty($ishooked) && empty($receipt['returnurl'])) {
        // get the title and link of the original object
        $modinfo = xarModGetInfo($header['modid']);
        $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
            array('itemtype' => $header['itemtype'], 'itemids' => array($header['objectid'])),
            // don't throw an exception if this function doesn't exist
            0
        );

        if (!empty($itemlinks) && !empty($itemlinks[$header['objectid']])) {
            $url = $itemlinks[$header['objectid']]['url'];
            if (!strstr($url, '?')) {
                $url .= '?';
            }
            $header['objectlink'] = $itemlinks[$header['objectid']]['url'];
            $header['objecttitle'] = $itemlinks[$header['objectid']]['label'];
        } else {
            $url = xarModURL($modinfo['name'], 'user', 'main');
        }

        $receipt['returnurl'] = array('encoded' => rawurlencode($url), 'decoded' => $url);
    } elseif (!isset($receipt['returnurl']['raw'])) {
        if (empty($args['extrainfo'])) {
            $modinfo = xarModGetInfo($args['modid']);
            $receipt['returnurl']['raw'] = xarModURL($modinfo['name'],'user','main');
        } elseif (is_array($args['extrainfo']) && isset($args['extrainfo']['returnurl'])) {
            $receipt['returnurl']['raw'] = $args['extrainfo']['returnurl'];
        } elseif (is_string($args['extrainfo'])) {
            $receipt['returnurl']['raw'] = $args['extrainfo'];
        }
        if (!stristr($receipt['returnurl']['raw'], '?')) {
            $receipt['returnurl']['raw'] .= '?';
        }
        $receipt['returnurl']['decoded'] = $receipt['returnurl']['raw'] . $settings_uri;
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);
    } else {
        if (!stristr($receipt['returnurl']['raw'],'?')) {
            $receipt['returnurl']['raw'] .= '?';
        }
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['raw']);
        $receipt['returnurl']['decoded'] = $receipt['returnurl']['raw'] . $settings_uri;
    }

    $receipt['post_url']              = xarModURL('comments', 'user', 'reply');
    $receipt['action']                = 'display';

    $hooks = xarModAPIFunc('comments','user','formhooks');
    $item['returnurl'] = xarModURL('comments',
				   'user',
				   'display',
				   array('cid' => $comment['xar_cid']));
    $item['module'] = 'comments';
    $hookoutputs = xarModCallHooks('item',
			     'display',
			     $comment['xar_cid'],
			     $item);
    if (empty($hookoutputs)) {
      $output['hookoutput'] = '';
    } else {
      /* You can use the output from individual hooks in your template too, e.g. with
       * $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
       */
      $output['hookoutput'] = $hookoutputs;
    }

    //if (time() - ($package['comments']['xar_date'] - ($package['settings']['edittimelimit'] * 60))) {
    //}
    $output['hooks']   = $hooks;
    $output['header']  = $header;
    $output['package'] = $package;
    $output['receipt'] = $receipt;

    return $output;
}
?>
