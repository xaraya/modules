<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
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
 * @param    integer    [$args['selected_id']]      optional: the cid of the comment to view (only for displaying single comments)
 * @param    integer    [$args['thread']]           optional: display the entire thread following cid
 * @param    integer    [$args['preview']]          optional: an array containing a single (preview) comment used with adding/editing comments
 * @return   array      returns whatever needs to be parsed by the BlockLayout engine
 */
function comments_user_display($args)
{
    if (!xarSecurityCheck('ReadComments', 0)) return;

    // check if we're coming via a hook call
    if (isset($args['objectid'])) {
        $ishooked = 1;
    } else {
        // if we're not coming via a hook call
        $ishooked = 0;
        // then check for a 'id' parameter
        if (!empty($args['id'])) {
            $id = $args['id'];
        } else {
            xarVarFetch('id', 'int:1:', $id, 0, XARVAR_NOT_REQUIRED);
        }
        // and set the selected id to this one
        if (!empty($id) && !isset($args['selected_id'])) {
            $args['selected_id'] = $id;
        }
    }

    // TODO: now clean up the rest :-)

    $header   = xarRequest::getVar('header');
    $package  = xarRequest::getVar('package');
    $receipt  = xarRequest::getVar('receipt');

    // Fetch the module ID
    if (isset($args['modid'])) {
        $header['modid'] = $args['modid'];
    } elseif (isset($header['modid'])) {
        $args['modid'] = $header['modid'];
    } elseif (!empty($args['extrainfo']) && !empty($args['extrainfo']['module'])) {
        if (is_numeric($args['extrainfo']['module'])) {
            $modid = $args['extrainfo']['module'];
        } else {
            $modid = xarMod::getRegID($args['extrainfo']['module']);
        }
        $args['modid'] = $modid;
        $header['modid'] = $modid;
    } else {
        xarVarFetch('modid', 'isset', $modid, NULL, XARVAR_NOT_REQUIRED);
        if (empty($modid)) {
            $modid = xarMod::getRegID(xarModGetName());
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

	
    $package['settings'] = xarMod::apiFunc('comments','user','getoptions',$header);

    // FIXME: clean up return url handling

    $settings_uri = "&#38;depth={$package['settings']['depth']}"
        . "&#38;order={$package['settings']['order']}"
        . "&#38;sortby={$package['settings']['sortby']}"
        . "&#38;render={$package['settings']['render']}";

    // Fetch the object ID
    if (isset($args['objectid'])) {
        $header['objectid'] = $args['objectid'];
    } elseif (isset($header['objectid'])) {
        $args['objectid'] = $header['objectid'];
    } else {
        xarVarFetch('objectid','int', $objectid, 1, XARVAR_NOT_REQUIRED);
        $args['objectid'] = $objectid;
        $header['objectid'] = $objectid;
    }

    if (isset($args['selected_id'])) {
        $header['selected_id'] = $args['selected_id'];
    } elseif (isset($header['selected_id'])) {
        $args['selected_id'] = $header['selected_id'];
    } else {
        xarVarFetch('selected_id', 'isset', $selected_id, NULL, XARVAR_NOT_REQUIRED);
        $args['selected_id'] = $selected_id;
        $header['selected_id'] = $selected_id;
    }
    if (!isset($args['thread'])) {
        xarVarFetch('thread', 'isset', $thread, NULL, XARVAR_NOT_REQUIRED);
    }
    if (isset($thread) && $thread == 1) {
        $header['cid'] = $cid;
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
        throw new BadParameterException($msg);
    }

	// testing
	//$header['selected_id'] = 3;

    if (!isset($header['selected_id']) || isset($thread)) {
        $package['comments'] = xarMod::apiFunc('comments','user','get_multiple',$header);
        if (count($package['comments']) > 1) {
            $package['comments'] = comments_renderer_array_sort(
                $package['comments'],
                $package['settings']['sortby'],
                $package['settings']['order']
            );
        }
    } else {
        $header['id'] = $header['selected_id'];
        $package['settings']['render'] = _COM_VIEW_FLAT;
        $package['comments'] = xarMod::apiFunc('comments','user','get_one', $header);
        if (!empty($package['comments'][0])) {
            $header['modid'] = $package['comments'][0]['modid'];
            $header['itemtype'] = $package['comments'][0]['itemtype'];
            $header['objectid'] = $package['comments'][0]['objectid'];
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
            $comment['text'] = xarVarPrepHTMLDisplay($comment['text']);
            $comment['title'] = xarVarPrepForDisplay($comment['title']);
            // say which pieces of text (array keys) you want to be transformed
            $comment['transform'] = array('text');
            // call the item transform hooks
            // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
            $package['comments'][$key] = xarModCallHooks('item', 'transform', $comment['id'], $comment, 'comments');
        }
    } 
    $header['input-title']            = xarML('Post a new comment');

    $package['settings']['max_depth'] = _COM_MAX_DEPTH;
    $package['role_id']               = xarUserGetVar('id');
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
        try{
            $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                array('itemtype' => $header['itemtype'], 'itemids' => array($header['objectid'])));
        } catch (Exception $e) {}

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
        } else {
            $receipt['returnurl']['raw'] = '';
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

    $hooks = xarMod::apiFunc('comments','user','formhooks');

	$baseurl = xarServer::getCurrentURL();
	foreach($package['comments'] as $key => $val) {
		$package['comments'][$key]['permalink'] = str_replace($baseurl, '',$package['comments'][$key]['permalink']);
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
