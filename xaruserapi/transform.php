<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <potion@xaraya.com>
 */
/**
 *    Call the path_userapi_itemurl for strings like...

			pathURL:somemodule:123

		...where 123 is an itemid.

		If $withscheme is false, the URL scheme (ex. http://) will be removed.

 */
function path_userapi_transform($args){

	$withscheme = false;

	extract($args);

	if (strstr($subj,'pathURL:')) {
		 
		$trans = preg_replace_callback('/pathURL:(\w+):(\d+)/i',
			create_function(
				   '$matches',
					"return xarMod::apiFunc('path','user','itemurl',array('module' => \$matches[1], 'itemid' => \$matches[2], 'withscheme' => '" . $withscheme . "'));"   
			),
		$subj);

		return $trans;

	} else {

		return $subj;

	}

}

?>