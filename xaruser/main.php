<?php
function headlines_user_main()
{
    xarVarFetch('startnum', 'id', $startnum, '1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('catid', 'str:0:', $data['catid'], '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                           'user',
                           'getall',
                            array('catid' => $data['catid'],
                                  'startnum' => $startnum,
                                  'numitems' => xarModGetVar('headlines', 'itemsperpage')));

    //if (empty($links)) return
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];

        // Check and see if a feed has been supplied to us.
        if(isset($link['url'])) {
            $feedfile = $link['url'];
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
                                        'extension' => '.xml',
                                        'superrors' => TRUE));
        if (!$feeddata) {
            //return; // throw back
        }

        // Create a need feedParser object
        $p = new feedParser();

        // Tell feedParser to parse the data
        $info = $p->parseFeed($feeddata);

        if (empty($info['warning'])){
            if (!empty($link['title'])){
                $links[$i]['chantitle'] = $link['title'];
            } else {
                $links[$i]['chantitle']  =   $info['channel']['title'];
            }
            if (!empty($link['desc'])){
                $links[$i]['chandesc'] = $link['desc'];

            } else {
                $links[$i]['chandesc']   =   $info['channel']['description'];
            }
            $links[$i]['chanlink']   =   $info['channel']['link'];
            $links[$i]['viewlink'] = xarModURL('headlines',
                                               'user',
                                               'view',
                                               array('hid' => $link['hid']));
            $links[$i]['importlink'] = xarModURL('headlines',
                                                 'admin',
                                                 'import',
                                                 array('hid' => $link['hid']));

        // FIXME Reverse Logic here until I make a config setting.
        if (!empty($settings['showcomments'])) {
            $showcomments = 0;
        } else {
            $showcomments = 1;
        }
        
        if ($showcomments) {
            if (!xarModIsAvailable('comments')) {
                $showcomments = 0;
            }
        }
        
        if ($showcomments) {
            $links[$i]['comments'] = xarModAPIFunc('comments',
                                                   'user',
                                                   'get_count',
                                                   array('modid' => xarModGetIDFromName('headlines'),
                                                         'objectid' => $link['hid']));
            
            if (!$links[$i]['comments']) {
                $links[$i]['comments'] = '';
            } elseif ($links[$i]['comments'] == 1) {
                $links[$i]['comments'] .= ' ' . xarML('comment');
            } else {
                $links[$i]['comments'] .= ' ' . xarML('comments');
            }
        } else {
            $links[$i]['comments'] = '';
        }
        

        } else {
            $links[$i]['chantitle'] = xarVarPrepForDisplay($feedfile);
            $links[$i]['chandesc'] = xarML('There is a problem with this feed : #(1)', xarVarPrepForDisplay($info['warning']));
            $links[$i]['chanlink'] = '';
            $links[$i]['viewlink'] = '';
            $links[$i]['importlink'] = '';
            $links[$i]['comments'] = '';
        }
    }

    $data['indlinks'] = $links;
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'user', 'main', array('startnum' => '%%')),
                                    xarModGetVar('headlines', 'itemsperpage'));

    return $data;
}
?>