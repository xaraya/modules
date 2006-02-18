<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */


/*
 * Support for short URLs (user functions)
 *
 * The following two functions encode module parameters into some
 * virtual path that will be added to index.php, and decode a virtual
 * path back to the original module parameters.
 *
 * The result is that people (and search engines) can use URLs like :
 *
 * - http://mysite.com/index.php/newsletter/ (main function)
 * - http://mysite.com/index.php/newsletter/list.html (view function)
 * - http://mysite.com/index.php/newsletter/123.html (display function)
 *
 * in addition to the 'normal' Xaraya URLs that look like :
 *
 * - http://mysite.com/index.php?module=newsletter&func=display&exid=123
 *
 * You can also combine the two, e.g. for less frequently-used parameters :
 *
 * - http://mysite.com/index.php/newsletter/list.html?startnum=21
 *
 *
 * Module developers who wish to support this feature are strongly
 * recommended to create virtual paths that are 'semantically meaningful',
 * so that people navigating in your module can understand at a glance what
 * the short URLs mean, and how they could e.g. display item 234 simply
 * by changing the 123.html into 234.html.
 *
 * For newer modules with many different optional parameters and functions,
 * this generally implies re-thinking which parameters could easily be set
 * to some default to cover the most frequently-used cases, and rethinking
 * how each function could be represented inside some "virtual directory
 * structure". E.g. .../archive/2002/05/, .../forums/12/345.html, ../recent.html
 * or .../<categoryname>/123.html
 *
 * The same kind of encoding/decoding can be done for admin functions as well,
 * except that by default, the URLs will start with index.php/admin/newsletter.
 * The encode/decode functions for admin functions are in xaradminapi.php.
 *
 */


/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author Richard Cave
 * @param $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function newsletter_userapi_decode_shorturl($params)
{
    // Initialise the argument list we will return
    $args = array();

    $module = 'newsletter';

    // Analyse the different parts of the virtual path
    // $params[1] contains the first part after index.php/newsletter

    // In general, you should be strict in encoding URLs, but as liberal
    // as possible in trying to decode them...
    if (empty($params[1])) {
        // nothing specified -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^index/i',$params[1])) {
        // some search engine/someone tried using index.html (or similar)
        // -> we'll go to the main function
        return array('main', $args);

    } elseif (preg_match('/^subscribe/i',$params[1])) {
        // something that starts with 'subscribe' is probably for the
        // newsubscription function
        // Note : make sure your encoding/decoding is consistent ! :-)
        return array('newsubscription', $args);

    } elseif (preg_match('/^modify/i',$params[1])) {
        // something that starts with 'subscribe' is probably for the
        // newsubscription function
        // Note : make sure your encoding/decoding is consistent ! :-)
        return array('modifysubscription', $args);


    //} elseif (preg_match('/^(\d+)/',$params[1],$matches)) {
    } elseif (preg_match('/^archives/i',$params[1])) {
        // something that starts with 'archive' is probably for the
        // viewarchives function
        // Note : make sure your encoding/decoding is consistent ! :-)
        if (isset($params[2])) {
            $publicationId = $params[2];
            $args['publicationId'] = $publicationId;
        }
        return array('viewarchives', $args);

    } elseif (preg_match('/^preview/i',$params[1])) {
        // something that starts with 'archive' is probably for the
        // viewarchives function
        // Note : make sure your encoding/decoding is consistent ! :-)
        if (isset($params[2])) {
            $issueId = $params[2];
            $args['issueId'] = $issueId;
        }
        return array('previewissue', $args);

    } else {
        // the first part might be something variable like a category name
        // In order to match that, you'll have to retrieve all relevant
        // categories for this module, and compare against them...
        // $cid = xarModGetVar('newsletter','mastercids');
        // if (xarModAPILoad('categories','user')) {
        //     $cats = xarModAPIFunc('categories',
        //                          'user',
        //                          'getcat',
        //                          array('cid' => $cid,
        //                                'return_itself' => true,
        //                                'getchildren' => true));
        //     // lower-case for fanciful search engines/people
        //     $params[1] = strtolower($params[1]);
        //     $foundcid = 0;
        //     foreach ($cats as $cat) {
        //         if ($params[1] == strtolower($cat['name'])) {
        //             $foundcid = $cat['cid'];
        //             break;
        //         }
        //     }
        //     // check if we found a matching category
        //     if (!empty($foundcid)) {
        //         $args['cid'] = $foundcid;
        //         // TODO: now analyse $params[2] for index, list, \d+ etc.
        //         // and return array('whatever', $args);
        //     }
        // }

        // we have no idea what this virtual path could be, so we'll just
        // forget about trying to decode this thing

        // you *could* return the main function here if you want to
        // return array('main', $args);
    }

    // default : return nothing -> no short URL decoded
}

?>
