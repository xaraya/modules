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
 *
 * @package comments
 *
 */
sys::import('modules.dynamicdata.class.properties.base');

class CommentsProperty extends DataProperty
{
    public $id         = 103;
    public $name       = 'comments';
    public $desc       = 'Comments';
    public $reqmodules = array('comments');

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'comments';
        $this->filepath   = 'modules/comments/xarproperties';
    }

    public function showInput(array $data = array())
    {
        if (!xarSecurityCheck('ReadComments', 0)) {
            return;
        }

        // Check for a 'id' parameter
        if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            xarVarFetch('id', 'int:1:', $id, 0, XARVAR_NOT_REQUIRED);
        }

        // and set the selected id to this one
        if (!empty($id) && !isset($data['selected_id'])) {
            $data['selected_id'] = $id;
        }

        // TODO: now clean up the rest :-)

        $header   = xarController::getVar('header');
        $package  = xarController::getVar('package');
        $receipt  = xarController::getVar('receipt');

        // Fetch the module ID
        if (isset($data['modid'])) {
            $header['modid'] = $data['modid'];
        } elseif (isset($header['modid'])) {
            $data['modid'] = $header['modid'];
        } else {
            xarVarFetch('modid', 'isset', $modid, null, XARVAR_NOT_REQUIRED);
            if (empty($modid)) {
                $modid = xarMod::getRegID(xarModGetName());
            }
            $data['modid'] = $modid;
            $header['modid'] = $modid;
        }
        $header['modname'] = xarModGetNameFromID($header['modid']);

        // Fetch the itemtype
        if (isset($data['itemtype'])) {
            $header['itemtype'] = $data['itemtype'];
        } elseif (isset($header['itemtype'])) {
            $data['itemtype'] = $header['itemtype'];
        } else {
            xarVarFetch('itemtype', 'isset', $itemtype, null, XARVAR_NOT_REQUIRED);
            $data['itemtype'] = $itemtype;
            $header['itemtype'] = $itemtype;
        }


        $package['settings'] = xarMod::apiFunc('comments', 'user', 'getoptions', $header);

        // FIXME: clean up return url handling

        $settings_uri = "&#38;depth={$package['settings']['depth']}"
            . "&#38;order={$package['settings']['order']}"
            . "&#38;sortby={$package['settings']['sortby']}"
            . "&#38;render={$package['settings']['render']}";

        // Fetch the object ID
        if (isset($data['object'])) {
            $header['objectid'] = xarMod::getID($data['object']);
        } elseif (isset($header['objectid'])) {
            $data['objectid'] = $header['objectid'];
        } else {
            xarVarFetch('objectid', 'isset', $objectid, null, XARVAR_NOT_REQUIRED);
            $data['objectid'] = $objectid;
            $header['objectid'] = $objectid;
        }

        if (isset($data['selected_id'])) {
            $header['selected_id'] = $data['selected_id'];
        } elseif (isset($header['selected_id'])) {
            $data['selected_id'] = $header['selected_id'];
        } else {
            xarVarFetch('selected_id', 'isset', $selected_id, null, XARVAR_NOT_REQUIRED);
            $data['selected_id'] = $selected_id;
            $header['selected_id'] = $selected_id;
        }
        if (!isset($data['thread'])) {
            xarVarFetch('thread', 'isset', $thread, null, XARVAR_NOT_REQUIRED);
        }
        if (isset($thread) && $thread == 1) {
            $header['cid'] = $cid;
        }

        if (!xarModLoad('comments', 'renderer')) {
            $msg = xarML('Unable to load #(1) #(2)', 'comments', 'renderer');
            throw new BadParameterException($msg);
        }


        if (!isset($header['selected_id']) || isset($thread)) {
            $package['comments'] = xarMod::apiFunc('comments', 'user', 'get_multiple', $header);
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
            $package['comments'] = xarMod::apiFunc('comments', 'user', 'get_one', $header);
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
        /*if(isset($data['returnurl'])) {
            $receipt['returnurl']['raw'] = $data['returnurl'];
        }*/

        // get the title and link of the original object
        $modinfo = xarMod::getInfo($header['modid']);
        try {
            $itemlinks = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemlinks',
                array('itemtype' => $header['itemtype'], 'itemids' => array($header['objectid']))
            );
        } catch (Exception $e) {
        }

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

        /*$receipt['returnurl'] = array('encoded' => rawurlencode($url), 'decoded' => $url);*/

        $receipt['post_url']              = xarModURL('comments', 'user', 'reply');
        $receipt['action']                = 'display';

        $hooks = xarMod::apiFunc('comments', 'user', 'formhooks');

        //if (time() - ($package['comments']['xar_date'] - ($package['settings']['edittimelimit'] * 60))) {
        //}
        $data['hooks']   = $hooks;
        $data['header']  = $header;
        $data['package'] = $package;
        $data['receipt'] = $receipt;

        return parent::showInput($data);
    }
}
