<?php
function headlines_user_view()
{
    // Security Check
    if(!xarSecurityCheck('ReadHeadlines')) return;
    xarVarFetch('hid', 'id', $hid, XARVAR_PREP_FOR_DISPLAY);

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }
    $data['hid'] = $hid;

    // Check and see if a feed has been supplied to us.
    if(isset($links['url'])) {
        $feedfile = $links['url'];
    } else {
        $feedfile = "";
    }
    // Sanitize the URL provided to us since
    // some people can be very mean.
    $feedfile = preg_replace("/\.\./","donthackthis",$feedfile);
    $feedfile = preg_replace("/^\//","ummmmno",$feedfile);

    // Get the feed file (from cache or from the remote site)
    $feeddata = xarModAPIFunc('base', 'user', 'getfile',
                              array('url' => $feedfile,
                                    'cached' => true,
                                    'cachedir' => 'cache/rss',
                                    'refresh' => 3600,
                                    'extension' => '.xml'));
    if (!$feeddata) {
        return; // throw back
    }

    // Create a need feedParser object
    $p = new feedParser();

    // Tell feedParser to parse the data
    $info = $p->parseFeed($feeddata);

    if (empty($info['warning'])){
        foreach ($info as $content){
             foreach ($content as $newline){
                    if(is_array($newline)) {
                        if (isset($newline['description'])){
                            $description = $newline['description'];
                        } else {
                            $description = '';
                        }
                        if (isset($newline['title'])){
                            $title = $newline['title'];
                        } else {
                            $title = '';
                        }
                        if (isset($newline['link'])){
                            $link = $newline['link'];
                        } else {
                            $link = '';
                        }

                        $feedcontent[] = array('title' => $title, 'link' => $link, 'description' => $description);
                }
            }
        }

        if (!empty($links['title'])){
            $data['chantitle'] = $links['title'];
        } else {
            $data['chantitle']  =   $info['channel']['title'];
        }
        if (!empty($links['desc'])){
            $data['chandesc'] = $links['desc'];
        } else {
            $data['chandesc']   =   $info['channel']['description'];
        }
        $data['chanlink']   =   $info['channel']['link'];

        xarTplSetPageTitle(xarVarPrepForDisplay($data['chantitle']));

    } else {
        $msg = xarML('There is a problem with a feed.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $data['feedcontent'] = $feedcontent;

    $data['module'] = 'headlines';
    $data['itemtype'] = 0;
    $data['itemid'] = $hid;
    $data['returnurl'] = xarModURL('headlines',
                                   'user',
                                   'view',
                                   array('hid' => $hid));
    $hooks = xarModCallHooks('item', 'display', $hid, $data);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}
?>