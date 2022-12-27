<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
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
 * @param    bool       [$args['noposting']]        optional: a boolean to define whether posting is enabled
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
    if (!xarSecurity::check('ReadComments', 0)) {
        return;
    }

    // Check if an object was passed
    if (isset($args['object'])) {
        $fields['moduleid'] = $args['object']->moduleid;
        $fields['itemtype'] = $args['object']->itemtype;
        $fields['itemid'] = $args['object']->properties['id']->value;
        $fields['parent_url'] = xarServer::getCurrentURL();
    } else {
        // Check for required args
        $ishooked = 0;
        // then check for a 'id' parameter
        if (!empty($args['id'])) {
            $comment_id = $args['id'];
        } else {
            xarVar::fetch('comment_id', 'int:1:', $data['comment_id'], 0, xarVar::NOT_REQUIRED);
        }
        // and set the selected id to this one
        if (!empty($data['comment_id']) && !isset($data['selected_id'])) {
            $data['selected_id'] = $data['comment_id'];
        }
    }

    # --------------------------------------------------------
    # Bail if the proper args were not passed
#
    if (empty($fields)) {
        return xarTpl::module('comments', 'user', 'errors', ['layout' => 'no_direct_access']);
    }

    # --------------------------------------------------------
    # Try and get a selectee ID if we don't have one yet
#
    if (empty($data['selected_id'])) {
        xarVar::fetch('selected_id', 'int', $data['selected_id'], 0, xarVar::NOT_REQUIRED);
    }

    # --------------------------------------------------------
    # Get the current comment
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'comments_comments']);
    if (!empty($data['selected_id'])) {
        $data['object']->getItem(['itemid' => $data['selected_id']]);
    }
    $data['selected_id'] = $data['object']->properties['id']->value;

    # --------------------------------------------------------
    # Add any attributes passed
#
    if (isset($args['tplmodule'])) {
        $data['object']->tplmodule = $args['tplmodule'];
    }

    # --------------------------------------------------------
    # Load the comment object with what we know about the environment
#
    $data['object']->setFieldValues($fields, 1);
    $fields = $data['object']->getFieldValues([], 1);

    # --------------------------------------------------------
    # Create an empty object for display and add any attributes passed
#
    $data['emptyobject'] = DataObjectMaster::getObject(['name' => 'comments_comments']);
    if (isset($args['tplmodule'])) {
        $data['object']->tplmodule = $args['tplmodule'];
    }

    # --------------------------------------------------------
    # Get the viewing options: depth, render style, order, and sortby
#
    $package['settings'] = xarMod::apiFunc('comments', 'user', 'getoptions');

    if (!isset($args['thread'])) {
        xarVar::fetch('thread', 'isset', $thread, null, xarVar::NOT_REQUIRED);
    }

    if (!xarMod::load('comments', 'renderer')) {
        $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
        throw new BadParameterException($msg);
    }

    if (empty($data['selected_id']) || isset($thread)) {
        $data['comments'] = xarMod::apiFunc('comments', 'user', 'get_multiple', $fields);
        if (count($data['comments']) > 1) {
            $data['comments'] = comments_renderer_array_sort(
                $data['comments'],
                $package['settings']['sortby'],
                $package['settings']['order']
            );
        }
    } else {
        $package['settings']['render'] = _COM_VIEW_FLAT;
        $data['comments'] = xarMod::apiFunc('comments', 'user', 'get_one', $fields);
    }

    $data['comments'] = comments_renderer_array_prune_excessdepth(
        [
            'array_list'    => $data['comments'],
            'cutoff'        => $package['settings']['depth'],
            'moduleid'      => $fields['moduleid'],
            'itemtype'      => $fields['itemtype'],
            'itemid'        => $fields['itemid'],
        ]
    );

    if ($package['settings']['render'] == _COM_VIEW_THREADED) {
        $data['comments'] = comments_renderer_array_maptree($data['comments']);
    }

    // run text and title through transform hooks
    if (!empty($data['comments'])) {
        foreach ($data['comments'] as $key => $comment) {
            $comment['text'] = xarVar::prepHTMLDisplay($comment['text']);
            $comment['title'] = xarVar::prepForDisplay($comment['title']);
            // say which pieces of text (array keys) you want to be transformed
            $comment['transform'] = ['text'];
            // call the item transform hooks
            // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
            $data['comments'][$key] = xarModHooks::call('item', 'transform', $comment['id'], $comment, 'comments');
        }
    }

    $package['settings']['max_depth'] = _COM_MAX_DEPTH;
    // Bug 6175: removed xarVar::prepForDisplay() from the title, as articles already
    // does this *but* maybe needs fixing in articles instead?
    $package['new_title']             = xarVar::getCached('Comments.title', 'title');

    if (!xarVar::fetch('comment_action', 'str', $data['comment_action'], 'submit', xarVar::NOT_REQUIRED)) {
        return;
    }

    $hooks = xarMod::apiFunc('comments', 'user', 'formhooks');

    if (!empty($data['comments'])) {
        $baseurl = xarServer::getCurrentURL();
        foreach ($data['comments'] as $key => $val) {
            $data['comments'][$key]['parent_url'] = str_replace($baseurl, '', $data['comments'][$key]['parent_url']);
        }
    }

    $data['hooks']   = $hooks;
    $data['package'] = $package;

    $data['comment_id'] = $data['selected_id'];

    // Pass posting parameter to the template
    if (isset($args['noposting'])) {
        $data['noposting'] = $args['noposting'];
    } else {
        $data['noposting'] = false;
    }

    return $data;
}
