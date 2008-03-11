<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * process feed
 * @return array of links, or false on failure
 */

function headlines_userapi_process($args)
{
     extract($args);

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');
    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // Sanitize the URL provided to us since
    // some people can be very mean.
    $feedfile = preg_replace("/\.\./", "donthackthis", $feedfile);
    $feedfile = preg_replace("/^\//", "ummmmno", $feedfile);

    // Get the feed file (from cache or from the remote site)
    $feeddata = xarModAPIFunc('base', 'user', 'getfile',
        array(
            'url' => $feedfile,
            'cached' => true,
            'cachedir' => 'cache/rss',
            'refresh' => (isset($refresh) ? $refresh : 3600),
            'extension' => '.xml'
        )
    );

    if (!$feeddata) {
        $msg = xarML('There is a problem with this feed.');
		return array('warning' => $msg);
    }

    // Check what makes a headline unique
    $uniqueid = xarModGetVar('headlines', 'uniqueid');
    if (!empty($uniqueid)) {
        $uniqueid = split(';', $uniqueid);
    } else {
        $uniqueid = array();
    }

    // Create a need feedParser object
    $p = new feedParser();

    // Tell feedParser to parse the data
    $info = $p->parseFeed($feeddata);

    if (empty($info['warning'])){
        foreach ($info as $content){
            foreach ($content as $newline) {
                if (is_array($newline)) {
                    if (isset($newline['description'])) {
                        $description = $newline['description'];
                    } else {
                        $description = '';
                    }

                    if (isset($newline['title'])) {
                        $title = $newline['title'];
                    } else {
                        $title = '';
                    }

                    if (isset($newline['link'])) {
                        $link = $newline['link'];
                    } else {
                        $link = '';
                    }

                    if (isset($newline['date'])) {
                        $date = $newline['date'];
                    } else {
                        $date = '';
                    }

                    if (!empty($uniqueid)) {
                        $params = array();
                        foreach ($uniqueid as $part) {
                            switch ($part) {
                                case 'feed':
                                    $params[$part] = $feedfile;
                                    break;
                                case 'link':
                                    $params[$part] = $link;
                                    break;
                                case 'title':
                                    $params[$part] = $title;
                                    break;
                                case 'description':
                                    $params[$part] = $description;
                                    break;
                                default:
                                    break;
                            }
                        }
                        $id = md5(serialize($params));
                        unset($params);
                    } else {
                        $id = md5(serialize($newline));
                    }

                    $feedcontent[] = array(
                        'id' => $id,
                        'title' => $title,
                        'link' => $link,
                        'description' => $description,
                        'date' => $date,
                    );
                }
            }
        }

        if (!empty($links['title'])) {
            $data['chantitle'] = $links['title'];
        } else {
            $data['chantitle'] = $info['channel']['title'];
        }

        if (!empty($links['desc'])){
            $data['chandesc'] = $links['desc'];
        } else {
            $data['chandesc'] = $info['channel']['description'];
        }

        $data['chanlink'] = $info['channel']['link'];

    } else {
        $msg = xarML('There is a problem with this feed.');
		return array('warning' => $msg);
    }

    $data['feedcontent'] = $feedcontent;

    return $data;
}


?>