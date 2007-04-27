<?php
/**
 * Create a new forum, including additional setups (calls create first)
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2007 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum (optional)
 * @param $args['fposter'] user id (optional)
 * @param $args['cids'] cats array (optional)
 * @param $args['allowbbcode'] 1 or 0 (optional)
 * @param $args['allowhtml'] 1 or 0 (optional)

 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_adminapi_new($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check, and setup the rest to pass along to create
    if (!isset($fname) || !is_string($fname)) {
        $msg = xarML('Invalid fname');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } 
    if (!isset($fdesc) || !is_string($fdesc)) {
        $args['fdesc'] = $args['fname'];
    }
    if (!isset($fposter)) {
        $tposter = xarUserGetVar('uid');
        $args['fposter'] = $tposter;
    }
    $args['ftopics'] = 1;
    $args['fposts'] = 1;

    $newfid = xarModApiFunc('xarbb', 'admin', 'create', $args );

    if (!$newfid) return; 

    // Get New Forum ID
    $forum = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $newfid));

    // Recovery procedure in case the forum is no longer assigned to any category
    if (empty($forum['fid'])) {
        $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
        foreach ($forums as $info) {
            if ($info['fid'] == $newfid) {
                $forum = $info;
                break;
            }
        }
        if (empty($forum['fid'])) {
            $msg = xarML('Invalid Parameter Count');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    // Need to create a topic so we don't get the nasty empty error when viewing the forum.
    $ttitle = xarML('Welcome to #(1)', $forum['fname']);
    $tpost = xarML('This is the first topic for #(1)', $forum['fname']);

    $tid = xarModAPIFunc('xarbb', 'user', 'createtopic',
        array(
            'fid'      => $forum['fid'],
            'ttitle'   => $ttitle,
            'tpost'    => $tpost,
            'tposter'  => $tposter
        )
    );
    if (!$tid) return;
    
    // Default forum settings
    $xarsettings= xarModGetVar('xarbb', 'settings');
    if (!empty($xarsettings)) {
        $settings = unserialize($xarsettings);
    }

    // TODO: define these defaults in ONE place only.
    $settings['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
    $settings['postsortorder']   = !isset($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
    $settings['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
    $settings['topicsortby']     = !isset($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
    $settings['topicsortorder']  = !isset($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
    $settings['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
    $settings['editstamp']       = 0; // default is zero !isset($settings['editstamp']) ? 0 :$settings['editstamp'];
    $settings['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
    $settings['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
    $settings['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
    $settings['usenntp']         = !isset($settings['usenntp']) ? false :$settings['usenntp'];
    
    // override defaults with args
    if (isset($allowhtml))        $settings['allowhtml'] = $allowhtml;
    else                         $allowhtml = $settings['allowhtml'];
    
    if (isset($allowbbcode))    $settings['allowbbcode'] = $allowbbcode;
    else                         $allowbbcode = $settings['allowbbcode'];
    
    // Enable bbcode hooks for new xarbb forum
    if (xarModIsAvailable('bbcode')) {
        if ($allowbbcode) {
            xarModAPIFunc('modules', 'admin', 'enablehooks',
                    array('callerModName'    => 'xarbb',
                          'callerItemType'   => $forum['fid'],
                          'hookModName'      => 'bbcode'));
        } else {
            xarModAPIFunc('modules', 'admin', 'disablehooks',
                    array('callerModName'    => 'xarbb',
                          'callerItemType'   => $forum['fid'],
                          'hookModName'      => 'bbcode'));
        }
    }
    
    // FIXME: *allowing* HTML and *transforming* text to HTML are two different things;
    // remove this hook dependancy here. This has already been done for modified forums.
    // Enable html hooks for xarbb forum
    if (xarModIsAvailable('html')) {
        if ($allowhtml) {
            xarModAPIFunc('modules','admin','enablehooks',
                    array('callerModName'    => 'xarbb',
                          'callerItemType'   => $forum['fid'],
                          'hookModName'      => 'html'));
        } else {
            xarModAPIFunc('modules','admin','disablehooks',
                    array('callerModName'    => 'xarbb',
                          'callerItemType'   => $forum['fid'],
                          'hookModName'      => 'html'));
        }
    }
    
    xarModSetVar('xarbb', 'settings.' . $forum['fid'], serialize($settings));

    // Return the id as returned by create
    return $newfid;
}

?>