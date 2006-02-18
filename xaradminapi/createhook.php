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
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['keywords'] from arguments, or 'keywords' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'createhook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    // check if we need to save some keywords here
    if (isset($extrainfo['keywords']) && is_string($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str:1:', $keywords, '', XARVAR_NOT_REQUIRED);
    }
    if (empty($keywords)) {
        return $extrainfo;
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
    // old way of doing it with hardcoded delimiters
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
    if (count($cleanwords) < 1) {
        return $extrainfo;
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
            if (count($acceptedwords) < 1) {
                return $extrainfo;
            }
            $cleanwords = $acceptedwords;
        }
    }
*/

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    foreach ($cleanwords as $word) {
        // Get a new keywords ID
        $nextId = $dbconn->GenId($keywordstable);
        // Create new keyword
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
    $extrainfo['keywords'] = join(' ',$cleanwords);
    return $extrainfo;
}
?>
