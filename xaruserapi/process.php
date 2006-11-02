<?php
function magpie_userapi_process($args)
{
    extract($args);

    // Little trick for headlines.
    if (empty($url)){
        if (!empty($feedfile)){
            $url = $feedfile;
        } else {
            $msg = xarML('Invalid Parameter.');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
    }

    // Check what makes a headline unique
    $uniqueid = xarModGetVar('headlines','uniqueid');
    if (!empty($uniqueid)) {
        $uniqueid = split(';',$uniqueid);
    } else {
        $uniqueid = array();
    }

    //define('MAGPIE_CACHE_ON',1) ;
    //define('MAGPIE_CACHE_FRESH_ONLY', true) ;
    //define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
    if (!defined('MAGPIE_DIR')) {
        define('MAGPIE_CACHE_DIR', 'var/cache/rss/');
        define('MAGPIE_USER_AGENT',"Xaraya Magpie Parser [ http://www.xaraya.com ]");
        define('MAGPIE_DIR', 'modules/magpie/xarclass/');
    }
	require_once(MAGPIE_DIR.'rss_fetch.inc');
    $rss = fetch_rss($url);

    $data = array();
    $data['chantitle'] = $rss->channel['title'];
    $data['chandesc'] = isset($rss->channel['description']);
    $data['chanlink'] = $rss->channel['link'];

    if (isset($rss)){
        foreach ($rss->items as $item){
            if (isset($item['description'])){
                $description = $item['description'];
            } else {
                $description = '';
            }
            if (isset($item['title'])){
                $title = $item['title'];
            } else {
                $title = '';
            }
            if (isset($item['link'])){
                $link = $item['link'];
            } else {
                $link = '';
            }
            if (isset($item['date_timestamp'])){
                $date = $item['date_timestamp'];
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
            $feedcontent[] = array('title' => $title, 'link' => $link, 'description' => $description, 'date' => $date);
        }
    } else {
        $data['warning'] = true;
    }
    $data['feedcontent'] = $feedcontent;    
    return $data;
}
?>