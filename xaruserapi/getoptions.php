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
 */
/**
 * Function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * This function returns options formatted for use by dd dropdown property
 *
 * @return array
 */
function crispbb_userapi_getoptions($args)
{
    extract($args);
    $data = array();
    if (empty($options)) return $data;
    if (is_string($options)) $options = explode(',', $options);
    if (!is_array($options)) return $data;

    foreach ($options as $option) {
        $items = array();
        switch ($option) {
            case 'fprivileges':

            break;

            case 'fsettings':

            break;

            case 'ftype':
                $items[0] = xarML('Normal');
                $items[1] = xarML('Redirected');
                $items[2] = xarML('Newsgroup');
                $items[3] = xarML('Remote');
            break;

            case 'fstatus':
                $items[0] = xarML('Open');
                $items[1] = xarML('Closed');
            break;

            case 'ttype':
                $items[0] = xarML('Normal');
                $items[1] = xarML('Sticky');
                $items[2] = xarML('Announcement');
                $items[3] = xarML('FAQ');
            break;

            case 'tstatus':
                $items[0] = xarML('Open');
                $items[1] = xarML('Closed');
                $items[2] = xarML('Submitted');
                $items[3] = xarML('Locked');
            break;

            case 'topicsort':
                $items['ttitle'] = xarML('Topic Title');
                $items['ptime'] = xarML('Last Post Time');
                $items['ttime'] = xarML('Topic Start Time');
                $items['numreplies'] = xarML('Number of Replies');
                $items['numviews'] = xarML('Number of Views');
                $items['towner'] = xarML('Topic Starter');
                if (xarModIsAvailable('ratings')) {
                    $items['ratings'] = xarML('Rating');
                }
            break;

            case 'sortorder':
                $items['ASC'] = !empty($short) ? xarML('ASC') : xarML('Ascending');
                $items['DESC'] = !empty($short) ? xarML('DESC') : xarML('Descending');
            break;

            case 'pages':
                $items[0] = xarML('on first page only');
                $items[1] = xarML('on all pages');
            break;

            case 'components':
                $items['forum'] = xarML('Forum');
                $items['topics'] = xarML('Topics');
                $items['posts'] = xarML('Posts');
            break;

            case 'displayoptions':
                $items['showuserpanel'] = xarML('Show user panel');
                $items['showsearchbox'] = xarML('Show search box');
                $items['showforumjump'] = xarML('Show forum jump');
                $items['showtopicjump'] = xarML('Show topic jump');
                $items['showquickreply'] = xarML('Show quick reply');
                $items['showpermissions'] = xarML('Show user permissions');
                $items['showsortbox'] = xarML('Show sort options');
                $items['showemptycats'] = xarML('Show empty categories');
            break;
            /*
            case 'privileges':
                if (!isset($level) || !is_numeric($level)) continue;
                //$items['viewforum'] = xarML('View Forum');
                //if ($level == 100) break;
                //$items['readforum'] = xarML('Read Forum');
                //if ($level == 200) break;
                $items['newtopic'] = xarML('Post Topics');
                $items['newreply'] = xarML('Post Replies');
                $items['editowntopic'] = xarML('Edit Own Topics');
                $items['editownreply'] = xarML('Edit Own Replies');
                $items['closeowntopic'] = xarML('Close Own Topics');
                $items['stickies'] = xarML('Post Stickies');
                $items['announcements'] = xarML('Post Announcmements');
                $items['faqs'] = xarML('Post FAQs');
                if (xarModIsAvailable('bbcode')) {
                    $items['bbcode'] = xarML('Post BBCode');
                    $items['bbcodedeny'] = xarML('Deny BBCode');
                }
                if (xarModIsAvailable('smilies')) {
                    $items['smilies'] = xarML('Post Smilies');
                    $items['smiliesdeny'] = xarML('Deny Smilies');
                }
                if (xarModIsAvailable('changelog')) {
                    $items['changelog'] = xarML('Remark Changelog');
                }
                if (xarModIsAvailable('polls')) {
                    $items['polls'] = xarML('Post Polls');
                }
                $items['html'] = xarML('Post HTML');
                $items['htmldeny'] = xarML('Deny HTML');
                if ($level == 300) break;
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
                if ($level == 400) break;
                $items['addforum'] = xarML('Add Forum');
                if ($level == 500) break;
                //$items['editforum'] = xarML('Edit Forum');
                //if ($level == 600) break;
                //$items['deleteforum'] = xarML('Delete Forum');
                //if ($level == 700) break;
                //$items['adminforums'] = xarML('Administrator');
            break;
            */
        }
        $data[$option] = $items;
    }

    if (count($data) == 1) return reset($data);
    return $data;

}
?>