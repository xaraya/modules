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
    extract($args);
    $presets = [];

    if (!empty($preset) && is_string($preset)) {
        $preset = explode(',', $preset);
    }

    if (!empty($preset) && is_array($preset)) {
        foreach ($preset as $key) {
            $items = [];
            switch ($key) {
                case 'topicsortoptions':
                    // admin functions : forumconfig, new, modify
                    $items['ttitle'] = ['id' => 'ttitle', 'name' => xarML('Topic Title')];
                    $items['ptime'] = ['id' => 'ptime', 'name' => xarML('Last Post Time')];
                    $items['ttime'] = ['id' => 'ttitle', 'name' => xarML('Topic Start Time')];
                    $items['numreplies'] = ['id' => 'numreplies', 'name' => xarML('Number of Replies')];
                    $items['numhits'] = ['id' => 'numhits', 'name' => xarML('Number of Views')];
                    $items['towner'] = ['id' => 'towner', 'name' => xarML('Topic Starter')];
                    if (xarMod::isAvailable('ratings')) {
                        $items['numratings'] = ['id' => 'numratings', 'name' => xarML('Topic Rating')];
                    }
                break;

                case 'tstatusoptions':
                    $items[0] = ['id' => '0', 'name' => xarML('Open')];
                    $items[1] = ['id' => '1', 'name' => xarML('Closed')];
                    $items[2] = ['id' => '2', 'name' => xarML('Submitted')];
                    $items[3] = ['id' => '3', 'name' => xarML('Moved')];
                    $items[4] = ['id' => '4', 'name' => xarML('Locked')];
                    $items[5] = ['id' => '5', 'name' => xarML('Deleted')];
                break;

                case 'ttypeoptions':
                    $items[0] = ['id' => '0', 'name' => xarML('Normal')];
                    $items[1] = ['id' => '1', 'name' => xarML('Sticky')];
                    $items[2] = ['id' => '2', 'name' => xarML('Announcement')];
                    $items[3] = ['id' => '3', 'name' => xarML('FAQ')];
                break;

                case 'forumstatusoptions':
                    $items[0] = ['id' => '0', 'name' => xarML('Open')];
                    $items[1] = ['id' => '1', 'name' => xarML('Closed')];
                break;

                case 'ftypeoptions':
                    $items[0] = ['id' => '0', 'name' => xarML('Default')];
                    $items[1] = ['id' => '1', 'name' => xarML('Redirected')];
                break;

                case 'pstatusoptions':
                    $items[0] = ['id' => '0', 'name' => xarML('Open')];
                    $items[2] = ['id' => '2', 'name' => xarML('Submitted')];
                    $items[5] = ['id' => '5', 'name' => xarML('Deleted')];
                break;

                case 'privleveloptions':
                    // used in forumconfig
                    $items[100] = ['id' => 100, 'name' => xarML('View')];
                    $items[200] = ['id' => 200, 'name' => xarML('Read')];
                    $items[300] = ['id' => 300, 'name' => xarML('Post')];
                    $items[400] = ['id' => 400, 'name' => xarML('Moderate')];
                    $items[500] = ['id' => 500, 'name' => xarML('Add')];
                    $items[600] = ['id' => 600, 'name' => xarML('Edit')];
                    $items[700] = ['id' => 700, 'name' => xarML('Delete')];
                    $items[800] = ['id' => 800, 'name' => xarML('Admin')];
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
                    $items['bbcode'] = xarML('Use BBCode');
                    $items['bbcodedeny'] = xarML('Disable BBCode');
                    $items['smilies'] = xarML('Use Smilies');
                    $items['smiliesdeny'] = xarML('Disable Smilies');
                    $items['html'] = xarML('Use HTML');
                    $items['htmldeny'] = xarML('Disable HTML');
                    $items['edittopics'] = xarML('Edit Topics');
                    $items['editreplies'] = xarML('Edit Replies');
                    $items['closetopics'] = xarML('Close Topics');
                    $items['locktopics'] = xarML('Lock Topics');
                    $items['movetopics'] = xarML('Move Topics');
                    $items['splittopics'] = xarML('Split Topics');
                    $items['deletetopics'] = xarML('Delete Topics');
                    $items['deletereplies'] = xarML('Delete Replies');
                    $items['approvetopics'] = xarML('Approve Topics');
                    $items['approvereplies'] = xarML('Approve Replies');
                    $items['addforum'] = xarML('Add Forum');
                    $items['editforum'] = xarML('Edit Forum');
                    $items['deleteforum'] = xarML('Delete Forum');
                break;

                case 'sortorderoptions':
                    // used in forumconfig
                    $items['ASC'] = ['id' => 'ASC', 'name' => xarML('ASC')];
                    $items['DESC'] = ['id' => 'DESC', 'name' => xarML('DESC')];
                break;

                case 'pagedisplayoptions':
                    // used in forumconfig
                    $items[0] = ['id' => 0, 'name' => xarML('on first page only')];
                    $items[1] = ['id' => 1, 'name' => xarML('on all pages')];
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
                    $items['topictitlemax'] = 100;
                    $items['topicdescmin'] = 0;
                    $items['topicdescmax'] = 254;
                    $items['topicpostmin'] = 5;
                    $items['topicpostmax'] = 65535;
                    $items['showstickies'] = 0;
                    $items['showannouncements'] = 0;
                    $items['showfaqs'] = 0;
                    $items['iconfolder'] = 'topicicons-crispbb';
                    $items['icondefault'] = 'none';
                    $items['floodcontrol'] = 0;
                    $items['postbuffer'] = 0;
                    $items['topicview'] = 'flat';
                    $items['showimages'] = true;
                    $items['ftransforms'] = [];
                    $items['ttransforms'] = [];
                    $items['ptransforms'] = [];
                    $items['topicapproval'] = 0;
                    $items['replyapproval'] = 0;
                    $items['redirected'] = [];
                    $items['newsgroup'] = [];
                    $items['members'] = [];
                break;

                case 'fprivileges':
                    // View options
                    $view = [];
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
                    $moderator['edittopics'] = 1;
                    $moderator['editreplies'] = 1;
                    $moderator['closetopics'] = 1;
                    $moderator['locktopics'] = 1;
                    $moderator['movetopics'] = 1;
                    $moderator['splittopics'] = 1;
                    $moderator['deletetopics'] = 1;
                    $moderator['deletereplies'] = 1;
                    $moderator['approvetopics'] = 1;
                    $moderator['approvereplies'] = 1;
                    $items[400] = $moderator;

                    // Add options
                    $add = $moderator;
                    $add['addforum'] = 1;
                    $items[500] = $add;

                    // Edit options
                    $edit = [];
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
                    $items['fname'] = ['id' => 'fname', 'name' => xarML('Forum Name')];
                    $items['fdesc'] = ['id' => 'fdesc', 'name' => xarML('Forum Description')];
                break;
                case 'ttransfields':
                    $items['ttitle'] = ['id' => 'ttitle', 'name' => xarML('Topic Title')];
                    $items['tdesc'] = ['id' => 'tdesc', 'name' => xarML('Topic Description')];
                    $items['ttext'] = ['id' => 'ttext', 'name' => xarML('Topic Text')];
                break;
                case 'ptransfields':
                    $items['pdesc'] = ['id' => 'pdesc', 'name' => xarML('Post Description')];
                    $items['ptext'] = ['id' => 'ptext', 'name' => xarML('Post Text')];
                break;
            }
            $presets[$key] = $items;
        }
    }

    return $presets;
}
