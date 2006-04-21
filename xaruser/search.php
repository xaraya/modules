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
 * @author Alberto Cazzaniga (Janez)
 */
/**
 * Search for keywords
 * @return array Retreived keywords
 */
function keywords_user_search($args)
{
if (!xarSecurityCheck('ReadKeywords',0)) return '';

    if(!xarVarFetch('q',        'isset', $q,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bool',     'isset', $bool,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}


    $data = array();
        if($q == ''){
           return $data;
        }

    $data['keys'] = xarModAPIFunc('keywords',
                                'user',
                                'search',
                               array('q' => $q));

    if (empty ($data['keys'])){
        $data['status'] = xarML('No Keywords found matching this search');
    }

   return $data;
}
?>