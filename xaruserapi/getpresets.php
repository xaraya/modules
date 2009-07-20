<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Get a specific item
 *
 * Standard function to return factory default settings
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  string type component to return defaults for (forums|privileges - default forums)
 * @return array
 * @throws none
 */
function crispbb_userapi_getpresets($args)
{
    extract ($args);
    $presets = array();

    if (!empty($preset) && is_string($preset)) {
        $preset = explode(',', $preset);
    }

    if (!empty($preset) && is_array($preset)) {
        foreach ($preset as $key) {
            $items = array();
            switch ($key) {
                case 'topicsortoptions':
                    // admin functions : forumconfig, new, modify
                    $items['ptime'] = array('id' => 'ptime', 'name' => xarML('Last Post Time'));
                break;

                case 'tstatusoptions':
                    $items[0] = array('id' => '0', 'name' => xarML('Open'));
                    $items[1] = array('id' => '1', 'name' => xarML('Closed'));
                    $items[2] = array('id' => '2', 'name' => xarML('Submitted'));
                    $items[3] = array('id' => '3', 'name' => xarML('Moved'));
                    $items[4] = array('id' => '4', 'name' => xarML('Locked'));
                    $items[5] = array('id' => '5', 'name' => xarML('Deleted'));
                break;

                case 'ttypeoptions':
                    $items[0] = array('id' => '0', 'name' => xarML('Normal Topic'));
                    $items[1] = array('id' => '1', 'name' => xarML('Sticky Topic'));
                    $items[2] = array('id' => '2', 'name' => xarML('Announcement'));
                    $items[3] = array('id' => '3', 'name' => xarML('FAQ'));
                break;

                case 'forumstatusoptions':
                    $items[0] = array('id' => '0', 'name' => xarML('Open'));
                    $items[1] = array('id' => '1', 'name' => xarML('Closed'));
                    //$items[2] = array('id' => '2', 'name' => xarML('Redirected'));
                    //$items[3] = array('id' => '3', 'name' => xarML('Members'));
                    //$items[4] = array('id' => '4', 'name' => xarML('Private'));
                    //$items[6] = array('id' => '2', 'name' => xarML('Locked'));
                break;

                case 'ftypeoptions':
                    $items[0] = array('id' => '0', 'name' => xarML('Default'));
                    $items[1] = array('id' => '1', 'name' => xarML('Redirected'));
                    $items[2] = array('id' => '2', 'name' => xarML('Members Only'));
                    $items[3] = array('id' => '3', 'name' => xarML('Newsgroup'));
                    $items[4] = array('id' => '4', 'name' => xarML('Imported'));
                break;

                case 'pstatusoptions':
                    $items[0] = array('id' => '0', 'name' => xarML('Open'));
                    $items[1] = array('id' => '1', 'name' => xarML('Reported'));
                    $items[2] = array('id' => '2', 'name' => xarML('Submitted'));
                    $items[5] = array('id' => '5', 'name' => xarML('Deleted'));
                break;

                case 'privleveloptions':
                    // used in forumconfig
                    $items[100] = array('id' => 100, 'name' => xarML('View'));
                    $items[200] = array('id' => 200, 'name' => xarML('Read'));
                    $items[300] = array('id' => 300, 'name' => xarML('Post'));
                    $items[400] = array('id' => 400, 'name' => xarML('Moderate'));
                    $items[500] = array('id' => 500, 'name' => xarML('Add'));
                    $items[600] = array('id' => 600, 'name' => xarML('Edit'));
                    $items[700] = array('id' => 700, 'name' => xarML('Delete'));
                    $items[800] = array('id' => 800, 'name' => xarML('Admin'));
                break;

                case 'privactionlabels':
                    $items['viewforum'] = xarML('View Forum');
                    $items['readforum'] = xarML('Read Topics');
                    $items['newtopic'] = xarML('Post New Topics');
                    $items['newreply'] = xarML('Post Replies');
                    $items['editowntopic'] = xarML('Edit Own Topics');
                    $items['editownreply'] = xarML('Edit Own Replies');
                    $items['closeowntopic'] = xarML('Close Own Topics');
                    $items['stickies'] = xarML('Post Stickies');
                    $items['announcements'] = xarML('Post Announcements');
                    $items['faqs'] = xarML('Post FAQs');
                    //$items['topicdescription'] = xarML('Add Topic Description');
                    //$items['topicicons'] = xarML('Add Topic Icons');
                    $items['bbcode'] = xarML('Use BBCode');
                    $items['bbcodedeny'] = xarML('Disable BBCode');
                    $items['smilies'] = xarML('Use Smilies');
                    $items['smiliesdeny'] = xarML('Disable Smilies');
                    //$items['changelog'] = xarML('Add Changelog Remark');
                    //$items['polls'] = xarML('Add Polls');
                    $items['html'] = xarML('Use HTML');
                    $items['htmldeny'] = xarML('Disable HTML');
                    //unset($items['editowntopic']); // not necessary for moderators
                    //unset($items['closeowntopic']); // not necessary for moderators
                    $items['edittopics'] = xarML('Edit Topics');
                    $items['editreplies'] = xarML('Edit Replies');
                    $items['closetopics'] = xarML('Close Topics');
                    $items['locktopics'] = xarML('Lock Topics');
                    $items['movetopics'] = xarML('Move Topics');
                    $items['splittopics'] = xarML('Split Topics');
                    $items['deletetopics'] = xarML('Delete Topics');
                    $items['deletereplies'] = xarML('Delete Replies');
                    //$items['approvetopics'] = xarML('Approve Topics');
                    //$items['approvereplies'] = xarML('Approve Replies');
                    $items['addforum'] = xarML('Add Forum');
                    $items['editforum'] = xarML('Edit Forum');
                    $items['deleteforum'] = xarML('Delete Forum');
                break;

                case 'sortorderoptions':
                    // used in forumconfig
                    $items['ASC'] = array('id' => 'ASC', 'name' => xarML('ASC'));
                    $items['DESC'] = array('id' => 'DESC', 'name' => xarML('DESC'));
                break;

                case 'pagedisplayoptions':
                    // used in forumconfig
                    $items[0] = array('id' => 0, 'name' => xarML('on first page only'));
                    $items[1] = array('id' => 1, 'name' => xarML('on all pages'));
                break;

                case 'fsettings':
                    // all the forum params you ever wanted to know about, but were afraid to ask :@)
                    $items['topicsperpage'] = 20;
                    $items['topicsortorder'] = 'DESC';
                    $items['topicsortfield'] = 'ptime';
                    $items['postsperpage'] = 20;
                    $items['postsortorder'] = 'ASC';
                    $items['hottopicposts'] = 20;
                    $items['hottopichits'] = 0;
                    $items['hottopicratings'] = 0;
                    $items['topictitlemin'] = 5;
                    $items['topictitlemax'] = 254;
                    //$items['topicdesc'] = 1;
                    $items['topicdescmin'] = 0;
                    $items['topicdescmax'] = 100;
                    $items['topicpostmin'] = 5;
                    $items['topicpostmax'] = 65535;
                    $items['showstickies'] = 0;
                    $items['showannouncements'] = 0;
                    $items['showfaqs'] = 0;
                    $items['iconfolder'] = 'topicicons';
                    $items['floodcontrol'] = 0;
                    $items['postbuffer'] = 0;
                    $items['topicview'] = 'flat';
                    $items['showimages'] = true;
                    $items['ftransforms'] = array();
                    $items['ttransforms'] = array();
                    $items['ptransforms'] = array();
                    $items['topicapproval'] = 0;
                    $items['replyapproval'] = 0;
                break;

                case 'fprivileges':
                    // View options
                    $view = array();
                    $view['viewforum'] = 2;
                    $items[100] = $view;

                    // Read options
                    $read = $view;
                    $read['readforum'] = 2;
                    $items[200] = $read;

                    // Poster options
                    $post = $read;
                    $post['newtopic'] = 1;
                    $post['newreply'] = 1;
                    $post['editowntopic'] = 1;
                    $post['editownreply'] = 1;
                    $post['closeowntopic'] = 0;
                    $post['stickies'] = 0;
                    $post['announcements'] = 0;
                    $post['faqs'] = 0;
                    //$post['topicdescription'] = 1;
                    //$post['topicicons'] = 1;
                    $post['bbcode'] = 0;
                    $post['bbcodedeny'] = 0;
                    $post['smilies'] = 0;
                    $post['smiliesdeny'] = 0;
                    $post['changelog'] = 0;
                    $post['polls'] = 0;
                    $post['html'] = 0;
                    $post['htmldeny'] = 0;
                    $items[300] = $post; // Post

                    // Moderator options
                    $moderator = $post; // inherit from post
                    //unset($moderator['editowntopic']); // not necessary for moderators
                    //unset($moderator['closeowntopic']); // not necessary for moderators
                    $moderator['edittopics'] = 1;
                    $moderator['editreplies'] = 1;
                    $moderator['closetopics'] = 1;
                    $moderator['locktopics'] = 1;
                    $moderator['movetopics'] = 1;
                    $moderator['splittopics'] = 1;
                    $moderator['deletetopics'] = 1;
                    $moderator['deletereplies'] = 1;
                    //$moderator['approvetopics'] = 1;
                    //$moderator['approvereplies'] = 1;
                    $items[400] = $moderator;

                    // Add options
                    $add = $moderator;
                    $add['addforum'] = 1;
                    $items[500] = $add;

                    // Edit options
                    $edit = array();
                    // if you can edit a forum, you automatically have privs for all previous levels
                    foreach ($add as $k => $v) {
                        $edit[$k] = 2;
                    }
                    $edit['editforum'] = 1;
                    $items[600] = $edit;

                    // Delete options
                    $delete = $edit;
                    $delete['deleteforum'] = 1;
                    $items[700] = $delete;

                    // Admin doesn't need any options
                    $items[800] = $delete;
                break;

                case 'ftransfields':
                    $items['fname'] = array('id' => 'fname', 'name' => xarML('Forum Name'));
                    $items['fdesc'] = array('id' => 'fdesc', 'name' => xarML('Forum Description'));
                break;
                case 'ttransfields':
                    $items['ttitle'] = array('id' => 'ttitle', 'name' => xarML('Topic Title'));
                    $items['tdesc'] = array('id' => 'tdesc', 'name' => xarML('Topic Description'));
                    $items['ttext'] = array('id' => 'ttext', 'name' => xarML('Topic Text'));
                break;
                case 'ptransfields':
                    $items['pdesc'] = array('id' => 'pdesc', 'name' => xarML('Post Description'));
                    $items['ptext'] = array('id' => 'ptext', 'name' => xarML('Post Text'));
                break;
            }
            $presets[$key] = $items;
        }
    }

    return $presets;

}
?>