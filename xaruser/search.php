<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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

    $data['keys'] = xarMod::apiFunc('keywords',
                                'user',
                                'search',
                               array('q' => $q));

    if (empty ($data['keys'])){
        $data['status'] = xarML('No Keywords found matching this search');
    }

   return $data;
}
?>