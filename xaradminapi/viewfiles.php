<?php
/**
 * View Files
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * View Files
 *
 * @author potion <ryan@webcommunicate.net>
 */
function downloads_adminapi_viewfiles($args)
{

	$locfilter = '';
	$filefilter = '';
	$sort = 'loc';

	extract($args); 
 
	foreach ($locations as $key => $loc) {

		if (empty($locfilter) || stristr($loc, $locfilter)) {

			if (is_dir($loc) && $handle = opendir($loc)) {
				/* This is the correct way to loop over the directory. */
				$num = 1;
				while (false !== ($file = readdir($handle))) {
					if (empty($filefilter) || stristr($file, $filefilter)) {
						if ($file != '.' && $file != '..') {
							$key = $loc . ';' . $num++;
							$files[$key] = $file;
						}
					}
				}
			}

		}
	}
	
	if (isset($files) && $sort == 'loc') {
		ksort($files);
		return $files;
	} elseif (isset($files)) { 
		asort($files);
		return $files;
	} else {
		return false;
	}

}

?>