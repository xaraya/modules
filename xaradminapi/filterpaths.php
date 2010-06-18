<?php
/**
 * get all paths or get paths by actionmodule and/or pathstart
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * @param $args['paths'] required array path
 * @param $args['filters'] optional array parts
 * @param $args['returnpart'] optional integer returnpart
 * @return array
 *
	Examples:

	If $filters == array(1=>'horse',2=>'saddle') you'll get back paths whose first	part is 'horse' and second part is 'saddle'.
	
	If $returnpart == 3, the array will contain only the third part of each path.	
 
 */
function path_adminapi_filterpaths($args) {

	extract($args);

	foreach($paths as $path) {

		// Make sure the path starts with a forward slash
		if($path[0] != '/') {
			$path = '/' . $path; 
		}
		$parts = explode('/',$path);
		unset($parts[0]); // Because of that forward slash

		if (isset($filters) && !empty($filters)) {
			foreach($filters as $key=>$val){
				if (isset($returnpart)) { // Return only the first part, or only the second part, etc., of each path
					if ($val[$key] == $parts[$key]) { 
						$result[] = $parts[$returnpart];
					}
				} else {  // Return the full paths
					if ($val[$key] == $parts[$key]) {
						$result[] = $path;
					}
				}
			}
		}

	}

	return $result;

}
?>
