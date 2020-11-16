<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Searches all -active- comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_search($args)
{
    if (!xarVarFetch('startnum', 'isset', $startnum, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('header', 'isset', $header, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('q', 'isset', $q, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('bool', 'isset', $bool, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('sort', 'isset', $sort, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('author', 'isset', $author, null, XARVAR_DONT_SET)) {
        return;
    }

    $postinfo   = array('q' => $q, 'author' => $author);
    $data       = array();
    $search     = array();

    // TODO:  check 'q' and 'author' for '%' value
    //        and sterilize if found
    if (!isset($q) || strlen(trim($q)) <= 0) {
        if (isset($author) && strlen(trim($author)) > 0) {
            $q = $author;
        } else {
            $data['header']['text']     = 1;
            $data['header']['title']    = 1;
            $data['header']['author']   = 1;
            return $data;
        }
    }

    $q = "%$q%";

    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 20;
    }

    if (isset($header['title'])) {
        $search['title'] = $q;
        $postinfo['header[title]'] = 1;
        $header['title'] = 1;
    } else {
        $header['title'] = 0;
        $postinfo['header[title]'] = 0;
    }

    if (isset($header['text'])) {
        $search['text'] = $q;
        $postinfo['header[text]'] = 1;
        $header['text'] = 1;
    } else {
        $header['text'] = 0;
        $postinfo['header[text]'] = 0;
    }

    if (isset($header['author'])) {
        $postinfo['header[author]'] = 1;
        $header['author'] = 1;
        $user = xarRoles::ufindRole($author);

        $search['role_id'] = $user;
        $search['author'] = $author;
    } else {
        $postinfo['header[author]'] = 0;
        $header['author'] = 0;
    }

    $package['comments'] = xarMod::apiFunc('comments', 'user', 'search', $search);

    if (!empty($package['comments'])) {
        foreach ($package['comments'] as $key => $comment) {
            if ($header['text']) {
                // say which pieces of text (array keys) you want to be transformed
                $comment['transform'] = array('text');
                // call the item transform hooks
                // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
                $comment = xarModCallHooks('item', 'transform', $comment['id'], $comment, 'comments');
                // Index appears to be empty on the transform.  Is this line needed?
                //$package['comments'][$key]['text'] = xarVarPrepHTMLDisplay($comment['text']);
            }
            if ($header['title']) {
                $package['comments'][$key]['title'] = xarVarPrepForDisplay($comment['title']);
            }
        }

        $header['modid'] = $package['comments'][0]['modid'];
        $header['itemtype'] = $package['comments'][0]['itemtype'];
        $header['objectid'] = $package['comments'][0]['objectid'];

        $receipt['directurl'] = true;

        $data['package'] = $package;
        $data['receipt'] = $receipt;
    }

    if (!isset($data['package'])) {
        $data['receipt']['status'] = xarML('No Comments Found Matching Search');
    }

    $data['header'] = $header;
    return $data;
}
