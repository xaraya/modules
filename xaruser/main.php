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
function headlines_user_main()
{
    xarVarFetch('startnum', 'id', $startnum, '1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('catid', 'str:0:', $data['catid'], '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;

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
        if (empty($link['url'])) {
            continue;
        }
        $feedfile = $link['url'];

        if (xarModGetVar('headlines', 'magpie')){
            $links[$i] = xarModAPIFunc('magpie',
                                  'user',
                                  'process',
                                  array('feedfile' => $feedfile));
        } else {
            $links[$i] = xarModAPIFunc('headlines',
                                  'user',
                                  'process',
                                  array('feedfile' => $feedfile));
        }

        if (!isset($links[$i])) {
            // Catch any exceptions.
            if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                // 'text' rendering returns the exception as an array.
                $errorstack = xarErrorGet();
                $errorstack = array_shift($errorstack);
                $links[$i] = array('chantitle' => $errorstack['short'],
                                   'chandesc'  => $errorstack['long']);
                // Clear the errors since we are handling it locally.
                xarErrorHandled();
            }
            continue;
        } elseif (!empty($links[$i]['warning'])){
            $msg = xarML('There is a problem with this feed : #(1)', $links[$i]['warning']);
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }

        if (!empty($link['title'])){
            $links[$i]['chantitle'] = $link['title'];
        }
        if (!empty($link['desc'])){
            $links[$i]['chandesc'] = $link['desc'];
        }
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

    }

    $data['indlinks'] = $links;
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'user', 'main', array('startnum' => '%%')),
                                    xarModGetVar('headlines', 'itemsperpage'));

    return $data;
}
?>
