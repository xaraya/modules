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
var_dump($args['tplmodule']);
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
            xarVarFetch('comment_id', 'int:1:', $data['comment_id'], 0, XARVAR_NOT_REQUIRED);
        }
        // and set the selected id to this one
        if (!empty($data['comment_id']) && !isset($data['selected_id'])) {
            $data['selected_id'] = $data['comment_id'];
        }
    }
    
# --------------------------------------------------------
# Bail if the proper args were not passed
#
    if (empty($fields))    
        return xarTpl::module('comments','user','errors',array('layout' => 'no_direct_access'));
        
# --------------------------------------------------------
# Try and get a selectee ID if we don't have one yet
#
    if (empty($data['selected_id'])) {
        xarVarFetch('selected_id', 'int', $data['selected_id'], 0, XARVAR_NOT_REQUIRED);
    }

# --------------------------------------------------------
# Get the current comment
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
    if (!empty($data['selected_id'])) $data['object']->getItem(array('itemid' => $data['selected_id']));
    $data['selected_id'] = $data['object']->properties['id']->value;

# --------------------------------------------------------
# Add any attributes passed
#
    if (isset($args['tplmodule'])) $data['object']->tplmodule = $args['tplmodule'];

# --------------------------------------------------------
# Load the comment object with what we know about the environment
#
    $data['object']->setFieldValues($fields);
    $fields = $data['object']->getFieldValues();


# --------------------------------------------------------
# Create an empty object for display and add any attributes passed
#
    $data['emptyobject'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
    if (isset($args['tplmodule'])) $data['object']->tplmodule = $args['tplmodule'];

# --------------------------------------------------------
# Get the viewing options: depth, render style, order, and sortby
#
    $data['object']->setFieldValues($fields);
    $fields = $data['object']->getFieldValues();

    $package['settings'] = xarMod::apiFunc('comments','user','getoptions');

    if (!isset($args['thread'])) {
        xarVarFetch('thread', 'isset', $thread, NULL, XARVAR_NOT_REQUIRED);
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
        throw new BadParameterException($msg);
    }

    if (empty($data['selected_id']) || isset($thread)) {
        $data['comments'] = xarMod::apiFunc('comments','user','get_multiple',$fields);
        if (count($data['comments']) > 1) {
            $data['comments'] = comments_renderer_array_sort(
                $data['comments'],
                $package['settings']['sortby'],
                $package['settings']['order']
            );
        }
    } else {
        $package['settings']['render'] = _COM_VIEW_FLAT;
        $data['comments'] = xarMod::apiFunc('comments','user','get_one', $fields);
    }

    $data['comments'] = comments_renderer_array_prune_excessdepth(
        array(
            'array_list'    => $data['comments'],
            'cutoff'        => $package['settings']['depth'],
            'moduleid'      => $fields['moduleid'],
            'itemtype'      => $fields['itemtype'],
            'itemid'        => $fields['itemid'],
        )
    );

    if ($package['settings']['render'] == _COM_VIEW_THREADED) {
        $data['comments'] = comments_renderer_array_maptree($data['comments']);
    }

    // run text and title through transform hooks
    if (!empty($data['comments'])) {
        foreach ($data['comments'] as $key => $comment) {
            $comment['text'] = xarVarPrepHTMLDisplay($comment['text']);
            $comment['title'] = xarVarPrepForDisplay($comment['title']);
            // say which pieces of text (array keys) you want to be transformed
            $comment['transform'] = array('text');
            // call the item transform hooks
            // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
            $data['comments'][$key] = xarModCallHooks('item', 'transform', $comment['id'], $comment, 'comments');
        }
    }

    $package['settings']['max_depth'] = _COM_MAX_DEPTH;
    // Bug 6175: removed xarVarPrepForDisplay() from the title, as articles already
    // does this *but* maybe needs fixing in articles instead?
    $package['new_title']             = xarVarGetCached('Comments.title', 'title');

    if (!xarVarFetch('comment_action', 'str', $data['comment_action'], 'submit', XARVAR_NOT_REQUIRED)) return;

    $hooks = xarMod::apiFunc('comments','user','formhooks');

    if (!empty($data['comments'])) {
        $baseurl = xarServer::getCurrentURL();
        foreach($data['comments'] as $key => $val) {
            $data['comments'][$key]['parent_url'] = str_replace($baseurl, '',$data['comments'][$key]['parent_url']);
        }
    }

    $data['hooks']   = $hooks;
    $data['package'] = $package;

    $data['comment_id'] = $data['selected_id'];
    return $data;
}
?>
