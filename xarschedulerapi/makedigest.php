<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * make a digest of new publications (executed by the scheduler module)
 * e.g. for sending out to users, for use by the pubsub module, ...
 *
 * @author mikespub
 * @access public
 */
function publications_schedulerapi_makedigest($args)
{

// TODO: get some configuration info about which pubtypes, categories, statees, ... are
//       concerned, if there are any limits to the number of publications to put in the digest,
//       etc. Then retrieve the relevant publications, create the digest and do whatever :-)

/* Simple example for HTML digests :

    $ptid = 1; // News Publications
    $catid = null; // all categories

    $now = time();
    // see when we last created a digest
    $lastdigest = xarModVars::get('publications','lastdigest');
    if (empty($lastdigest)) {
        // if we haven't created a digest yet, let's skip all past publications
        $lastdigest = $now;
    }

    // count the number of new items since the last digest
    $count = xarMod::apiFunc('publications','user','countitems',
                           array('ptid' => $ptid, // some publication type(s)
                                 'catid' => $catid, // some categories
                                 'state' => array(2,3), // approved or frontpage
                                 'startdate' => $lastdigest)); // since the last digest
    // specify some minimum number of items before we create a digest
    if ($count < 1) {
        // Note: don't update the last digest time here - we're waiting for enough publications
        // we're done here
        return true;
    }

    // create some HTML digest
    $htmldigest = xarMod::guiFunc('publications','user','view',
                             array('ptid' => $ptid,
                                   'catid' => $catid,
                                   'startdate' => $lastdigest,
                                   // override some defaults here if you want
                                   'numitems' => 20,
                                   'show_categories' => 1,
                                   'show_prevnext' => 0,
                                   'show_comments' => 0,
                                   'show_hitcount' => 0,
                                   'show_ratings' => 0,
                                   'show_archives' => 0,
                                   'show_map' => 0,
                                   'show_publinks' => 0));

    // update the last digest time
    xarModVars::set('publications','lastdigest',$now);

    // ... do something with this digest ...

    $lastdate = xarLocale::formatDate("%a, %d %b %Y %H:%M:%S %Z", $lastdigest);
    $subject = xarML('New publications since #(1)', $lastdate);

    $textdigest = strip_tags($htmldigest);
    $textdigest = preg_replace('/[ \t]+/',' ',$textdigest);
    $textdigest = preg_replace("/\s*\r?\n(\s*\r?\n)+/","\n\n",$textdigest);

    // get a list of users from somewhere
    $userlist = array(3); // typical Admin account here

    // send the digest to all of them
    foreach ($userlist as $uid) {
        $name = xarUser::getVar('name',$uid);
        $email = xarUser::getVar('email',$uid);
        if (!xarMod::apiFunc('mail', 'admin', 'sendhtmlmail',
                           array('info' => $email,
                                 'name' => $name,
                                 'subject' => $subject,
                                 'message' => $textdigest,
                                 'htmlmessage' => $htmldigest))) {
            // oops
        }
    }
*/

    return true;
}

?>
