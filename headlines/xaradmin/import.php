<?php
function headlines_admin_import()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;

    // Get parameters from whatever input we need
    $hid = xarVarCleanFromInput('hid');

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));


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

                        $imports[] = array('title' => $title, 'link' => $link, 'description' => $description);
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

    } else {
        $msg = xarML('There is a problem with this feed : #(1)', $info['warning']);
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (empty($importpubtype)) {
        $importpubtype = xarModGetVar('articles','defaultpubtype');
        if (empty($importpubtype)) {
            $importpubtype = 1;
        }
        xarModSetVar('headlines','importpubtype',1);
    }

    foreach ($imports as $import){

        $article['title'] = $import['title'];
        $article['summary'] = $import['description'];
        $article['summary'] .= '<br /><br />';
        $article['summary'] .= xarML('Source');
        $article['summary'] .= ': <a href="';
        $article['summary'] .= $import['link'];
        $article['summary'] .= '">';
        $article['summary'] .= $info['channel']['title'];
        $article['summary'] .= '</a>';
        $article['aid'] = 0;
        $article['ptid'] = $importpubtype;
        xarModAPIFunc('articles', 'admin', 'create', $article);
    }
    
    xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
}
?>