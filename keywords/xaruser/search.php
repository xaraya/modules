<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author Alberto Cazzaniga (Janez)
*/

/**
 * 
 */
 
function keywords_user_search($args) 
{
if (!xarSecurityCheck('ReadKeywords')) return;
	
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