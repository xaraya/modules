<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
 * @param array $args['extrainfo']
 * @param string $args['extrainfo']['keywords'] or 'keywords' from input (optional)
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

    // When called via hooks, the module name may be empty. Get it from current module.
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

    if (!xarSecurityCheck('AddKeywords',0,'Item', "$modid:$itemtype:$itemid")) return '';

    // Retrieve the list of allowed delimiters.  Use the first one as the default.
    $delimiters = xarModGetVar('keywords','delimiters');
    $delimiter = substr($delimiters,0,1);

    // Provide a $delimiter separated string of keywords for the form
    // Get old keywords from database and join them
    $oldwords = xarModAPIFunc('keywords', 'user', 'getwords',
                      array('modid'    => $modid,
                            'itemtype' => $itemtype,
                            'itemid'   => $itemid)
    );
    if (isset($oldwords) && count($oldwords) > 0) {
        $keywords = join($delimiter, $oldwords);
    }
    // Check if we have some keywords from a Preview or so and use them
    if (isset($extrainfo['keywords'])) {
        $keywords = $extrainfo['keywords'];
    } else {
        xarVarFetch('keywords', 'str:1:', $newkeywords, NULL, XARVAR_NOT_REQUIRED);
        if (isset($newkeywords)) {
            // We had a 'keywords' field in the form
            $keywords = $newkeywords;
        }
    }
    if (empty($keywords)) {
        $keywords = '';
    }

    $restricted = xarModGetVar('keywords','restricted');
    if ($restricted == '0') {
        // $keywords is delivered as string
        $wordlist = array();
    } else {
        // $keywords needs to be an array for restriced input
        $keywords = xarModAPIFunc('keywords','admin','separekeywords'
                                 ,array('keywords' => $keywords)
        );
        // Get array of predefined words
        $keywords1 = xarModAPIFunc('keywords', 'user', 'getwordslimited',
                                   array('moduleid' => $modid,
                                         'itemtype' => $itemtype)
        );print_r($keywords1);
        $wordlist=array_diff($keywords1, $keywords);
    }

    return xarTplModule('keywords', 'admin', 'modifyhook',
                        array('keywords' => $keywords,
                              'wordlist' => $wordlist,
                              'delimiters' => $delimiters,
                              'delimiter' => $delimiter,
                              'restricted' => $restricted));
}

?>
