<?php


/**
 * Searches all -active- comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_search( $args ) 
{

    if(!xarVarFetch('startnum', 'isset', $startnum,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('header',   'isset', $header,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('q',        'isset', $q,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bool',     'isset', $bool,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('author',   'isset', $author,    NULL, XARVAR_DONT_SET)) {return;}


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

        // need to get the user's uid from the name
        // FIXME:  this should be an api function in the roles module
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        // Get user information
        $rolestable = $xartable['roles'];
        $query = "SELECT xar_uid
                  FROM $rolestable
                  WHERE xar_uname = '" . xarVarPrepForStore($author) . "'";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        // if we found the uid add it to the search list,
        // otherwise we won't bother searching for it
        if (!$result->EOF) {
            $uids = $result->fields;
            $search['uid'] = $uids[0];
            $search['author'] = $author;
        }

        $result->Close();
    } else {
        $postinfo['header[author]'] = 0;
        $header['author'] = 0;
    }


    $package['comments'] = xarModAPIFunc('comments', 'user', 'search', $search);

    if (!empty($package['comments'])) {

        $header['modid'] = $package['comments'][0]['xar_modid'];
        $header['itemtype'] = $package['comments'][0]['xar_itemtype'];
        $header['objectid'] = $package['comments'][0]['xar_objectid'];
        $receipt['returnurl']['decoded'] = xarModURL('comments','user','display', $postinfo);
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);

        $receipt['directurl'] = true;

        $data['package'] = $package;
        $data['receipt'] = $receipt;


    }

    if (!isset($data['package'])){
        $data['receipt']['status'] = xarML('No Comments Found Matching Search');
    }

    $data['header'] = $header;
    return $data;
}

?>