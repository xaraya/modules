<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['keywords'] from arguments, or 'keywords' from input
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @return mixed true on success, false on failure. string keywords list
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object id', 'admin', 'updatehook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'updatehook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // We can exit immediately if the status flag is set because we are just updating
    // the status in the articles or other content module that works on that principle
    // Bug 1960 and 3161
    if (xarVarIsCached('Hooks.all','noupdate') || !empty($extrainfo['statusflag'])){
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'updatehook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'updatehook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // check if we need to save some keywords here
    if (isset($extrainfo['keywords']) && is_string($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str:1:', $keywords, '', XARVAR_NOT_REQUIRED);
    }
    if (empty($keywords)) {
        $keywords = '';
    }

 $words = xarModAPIFunc('keywords',
                         'admin',
                         'separekeywords',
                          array('keywords' => $keywords));

/*
    // get the list of delimiters to work with
    $delimiters = xarModGetVar('keywords','delimiters');
    $dellength = strlen($delimiters);

    // extract individual keywords from the input string (comma, semi-column or space separated)
    for ($i=0; $i<$dellength; $i++) {
        $delimiter = substr($delimiters,$i,1);
        if (strstr($keywords,$delimiter)) {
            $words = explode($delimiter,$keywords);
        }
    }
    //if nothing has been separated, just plop the whole string (possibly only one keyword) into words.
    if (!isset($words)) {
        $words = array();
        $words[] = $keywords;
    }
   */

    // old way with hardcoded separators
    /*if (strstr($keywords,',')) {
        $words = explode(',',$keywords);
    } elseif (strstr($keywords,';')) {
        $words = explode(';',$keywords);
    } else {
        $words = explode(' ',$keywords);
    }*/
    $cleanwords = array();
    foreach ($words as $word) {
        $word = trim($word);
        if (empty($word)) continue;
        $cleanwords[] = $word;
    }

/* TODO: restrict to predefined keyword list
    $restricted = xarModGetVar('keywords','restricted');
    if (!empty($restricted)) {
        $wordlist = array();
        if (!empty($itemtype)) {
            $getlist = xarModGetVar('keywords',$modname.'.'.$itemtype);
        } else {
            $getlist = xarModGetVar('keywords',$modname);
        }
        if (!isset($getlist)) {
            $getlist = xarModGetVar('keywords','default');
        }
        if (!empty($getlist)) {
            $wordlist = split(',',$getlist);
        }
        if (count($wordlist) > 0) {
            $acceptedwords = array();
            foreach ($cleanwords as $word) {
                if (!in_array($word, $wordlist)) continue;
                $acceptedwords[] = $word;
            }
            $cleanwords = $acceptedwords;
        }
    }
*/

    // get the current keywords for this item
    $oldwords = xarModAPIFunc('keywords','user','getwords',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $delete = array();
    $keep = array();
    $new = array();
    // check what we need to delete, what we can keep, and what's new
    if (isset($oldwords) && count($oldwords) > 0) {
        foreach ($oldwords as $id => $word) {
            if (!in_array($word,$cleanwords)) {
                $delete[$id] = $word;
            } else {
                $keep[] = $word;
            }
        }
        foreach ($cleanwords as $word) {
            if (!in_array($word,$keep)) {
                $new[] = $word;
            }
        }
        if (count($delete) == 0 && count($new) == 0) {
            $extrainfo['keywords'] = join(' ',$cleanwords);

            return $extrainfo;
        }
    } else {
        $new = $cleanwords;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    if (count($delete) > 0) {
        // Delete old words for this module item
        $idlist = array_keys($delete);
        $query = "DELETE FROM $keywordstable
                  WHERE xar_id IN (" . join(', ',$idlist) . ")";

        $result =& $dbconn->Execute($query);
        if (!$result) {
            // we *must* return $extrainfo for now, or the next hook will fail
            //return false;
            return $extrainfo;
        }
    }

    if (count($new) > 0) {
        foreach ($new as $word) {
            // Get a new keywords ID
            $nextId = $dbconn->GenId($keywordstable);
            // Create new keywords
            $query = "INSERT INTO $keywordstable (xar_id,
                                               xar_keyword,
                                               xar_moduleid,
                                               xar_itemtype,
                                               xar_itemid)
                    VALUES (?,
                            ?,
                            ?,
                            ?,
                            ?)";

            $result =& $dbconn->Execute($query,array($nextId, $word, $modid, $itemtype, $objectid));
            if (!$result) {
                // we *must* return $extrainfo for now, or the next hook will fail
                //return false;
                return $extrainfo;
            }

            //$keywordsid = $dbconn->PO_Insert_ID($keywordstable, 'xar_id');
        }
    }
    $extrainfo['keywords'] = join(' ',$cleanwords);
    // Return the extra info
    return $extrainfo;
}
?>
