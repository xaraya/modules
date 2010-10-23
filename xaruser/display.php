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
 * @param    integer    $args['depth']              depth of comment thread to display
 * @param    integer    [$args['selected_id']]      optional: the cid of the comment to view (only for displaying single comments)
 * @param    integer    [$args['thread']]           optional: display the entire thread following cid
 * @param    integer    [$args['preview']]          optional: an array containing a single (preview) comment used with adding/editing comments
 * @return   array      returns whatever needs to be parsed by the BlockLayout engine
 */

 /*
	generally speaking...
	$package = the comment data
	$header = info describing the item that we're commenting on 
	$receipt = particulars of the form submission
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

    $header   = xarRequest::getVar('header');
    $package  = xarRequest::getVar('package');
    $receipt  = xarRequest::getVar('receipt');

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

    $receipt['post_url']              = xarModURL('comments', 'user', 'reply');
    $receipt['action']                = 'display';

    $hooks = xarMod::apiFunc('comments','user','formhooks');

	if (!empty($package['comments'])) {
		$baseurl = xarServer::getCurrentURL();
		foreach($package['comments'] as $key => $val) {
			$package['comments'][$key]['objecturl'] = str_replace($baseurl, '',$package['comments'][$key]['objecturl']);
		}
	}

    $data['hooks']   = $hooks;
    $data['header']  = $header;
    $data['package'] = $package;
    $data['receipt'] = $receipt;

    return $data;
}
?>
