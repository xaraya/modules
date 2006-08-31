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
 * modify an entry for a module item - hook for ('item','modify','GUI')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @returns string
 * @return hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_admin_modifyhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'modifyhook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'modifyhook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
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
                    'module name', 'admin', 'modifyhook', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    //if (!xarSecurityCheck('AdminKeywords',0,'Item', "$modid:$itemtype:All")) return '';
    if (!xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:All")) return '';

//retrieve the list of allowed delimiters.  use the first one as the default.
    $delimiters = xarModGetVar('keywords','delimiters');
    $delimiter = substr($delimiters,0,1);

        $restricted = xarModGetVar('keywords','restricted');
        if ($restricted == '0') {
           $oldwords = xarModAPIFunc('keywords',
                                     'user',
                                     'getwords',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    if (isset($oldwords) && count($oldwords) > 0) {
        $keywords = join($delimiter,$oldwords);
    }

    if (isset($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str:1:', $newkeywords, NULL, XARVAR_NOT_REQUIRED);
        if (isset($newkeywords)) {
            $keywords = $newkeywords;
        }
    }
    if (empty($keywords)) {
        $keywords = '';
    }



/*
    // extract individual keywords from the input string (comma, semi-column or space separated)
    if (strstr($keywords,',')) {
        $words = explode(',',$keywords);
    } elseif (strstr($keywords,';')) {
        $words = explode(';',$keywords);
    } else {
        $words = explode(' ',$keywords);
    }
    $cleanwords = array();
    foreach ($words as $word) {
        $word = trim($word);
        if (empty($word)) continue;
        $cleanwords[] = $word;
    }
*/

    $wordlist = array();
/* TODO: restrict to predefined keyword list
    $restricted = xarModGetVar('keywords','restricted');
    if (!empty($restricted)) {
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

        } else {

                        $keywords = xarModAPIFunc('keywords','user','getwords',
                           array('modid' => $modid,
                                 'itemtype' => $itemtype,
                                 'itemid' => $itemid));

                $keywords1 = xarModAPIFunc('keywords',
                                'user',
                                'getwordslimited',
                                 array('moduleid' => $modid,
                 'itemtype' => $itemtype));


                        $wordlist=array_diff($keywords1,$keywords);


                }

    return xarTplModule('keywords',
                        'admin',
                        'modifyhook',
                        array('keywords' => $keywords,
                              'wordlist' => $wordlist,
                              'delimiters' => $delimiters,
                              'delimiter' => $delimiter,
                              'restricted' => $restricted));
}

?>
