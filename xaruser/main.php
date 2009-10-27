<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
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
    xarVarFetch('sort', 'enum:default:date:title', $sort, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;

    $numitems = xarModVars::get('headlines', 'itemsperpage');
    // TODO: admin configurable sort
    // $showsort = xarModVars::get('headlines', 'showsort'); // show sort options to users
    $sort = !empty($sort) ? $sort : xarModVars::get('headlines', 'sortorder'); // default sort order
    // The user API function is called
    $links = xarMod::apiFunc('headlines', 'user', 'getall',
        array(
            'catid' => $data['catid'],
            'startnum' => $startnum,
            'numitems' => $numitems,
            'sort' => $sort
        )
    );
    //if (empty($links)) return
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        // Check and see if a feed has been supplied to us.
        if (empty($link['url'])) {
            continue;
        }
        $feedfile = $link['url'];
        // TODO: make refresh configurable
        $links[$i] = xarMod::apiFunc(
            'headlines', 'user', 'getparsed',
            array('feedfile' => $feedfile, 'refresh' => 7200)
        );


        if (!isset($links[$i])) {
            // CHECKME: What exactly does this catch?
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
            // TODO: option to hide broken feeds for all/admin/user
            $links[$i]['chantitle'] = xarML('Feed unavailable');
            $links[$i]['chandesc'] = $links[$i]['warning'];
        }
        if (!empty($link['title'])){
            $links[$i]['chantitle'] = $link['title'];
        }
        if (!empty($link['desc'])){
            $links[$i]['chandesc'] = $link['desc'];
        }
        if (empty($link['date'])) {
            $link['date'] = time();
        }
        if (empty($links[$i]['lastitem'])) {
            $links[$i]['lastitem'] = $link['date'];
        }

        // TODO: Check individual permissions for View / Import / Edit / Delete
        $links[$i]['viewlink'] = xarModURL('headlines',
                                           'user',
                                           'view',
                                           array('hid' => $link['hid']));
        $links[$i]['importlink'] = xarModURL('headlines',
                                             'admin',
                                             'import',
                                             array('hid' => $link['hid']));
        /* TODO: use the correct api funcs (getall etc) to grab lists of comments, hits, ratings, keywords */

        $showcomments = xarModVars::get('headlines', 'showcomments');

        if ($showcomments) {
            if (!xarMod::isAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
                $showcomments = 0;
            }
        }

        if ($showcomments) {
            $links[$i]['comments'] = xarMod::apiFunc('comments',
                                                   'user',
                                                   'get_count',
                                                   array('modid' => xarMod::getRegID('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['comments']) {
                $links[$i]['comments'] = xarML('No comments');
            } elseif ($links[$i]['comments'] == 1) {
                $links[$i]['comments'] .= ' ' . xarML('comment');
            } else {
                $links[$i]['comments'] .= ' ' . xarML('comments');
            }
        } else {
            $links[$i]['comments'] = '';
        }

        $showratings = xarModVars::get('headlines', 'showratings');

        if ($showratings) {
            if (!xarMod::isAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
                $showratings = 0;
            }
        }

        if ($showratings) {
            $links[$i]['ratings'] = xarMod::apiFunc('ratings',
                                                   'user',
                                                   'get',
                                                   array('modid' => xarMod::getRegID('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['ratings']) {
                $links[$i]['ratings'] = xarML('Unrated');
            } else {
                $links[$i]['ratings'] = xarML('Rated ') . $links[$i]['ratings'];
            }
        } else {
            $links[$i]['ratings'] = '';
        }

        $showhitcount = xarModVars::get('headlines', 'showhitcount');

        if ($showhitcount) {
            if (!xarMod::isAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
                $showhitcount = 0;
            }
        }

        if ($showhitcount) {
            $links[$i]['hitcount'] = xarMod::apiFunc('hitcount',
                                                   'user',
                                                   'get',
                                                   array('modid' => xarMod::getRegID('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['hitcount']) {
                $links[$i]['hitcount'] = xarML('No reads');
            } elseif ($links[$i]['hitcount'] == 1) {
                $links[$i]['hitcount'] .= ' ' . xarML('read');
            } else {
                $links[$i]['hitcount'] .= ' ' . xarML('reads');
            }
        } else {
            $links[$i]['hitcount'] = '';
        }

        $showkeywords = xarModVars::get('headlines', 'showkeywords');

        if ($showkeywords) {
            if (!xarMod::isAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
                $showkeywords = 0;
            }
        }
        if ($showkeywords) {
            $links[$i]['keywords'] = xarMod::apiFunc('keywords', 'user', 'getwords',
                                                    array('modid' => xarMod::getRegID('headlines'),
                                                            'itemid' => $link['hid']));
        }
    }

    $data['indlinks'] = $links;
    sys::import('xaraya.pager');
    $data['pager'] = xarTplGetPager($startnum,
                                    xarMod::apiFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'user', 'main', array('startnum' => '%%')),
                                    $numitems);

    xarTPLSetPageTitle(xarML('Syndicated Headlines'));

    return $data;
}
?>
