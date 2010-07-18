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
	$sort = 'dir';

	extract($args); 

	asort($directories);
 
	foreach ($directories as $dir) {

		$loc = $basepath . $dir;

		if (empty($locfilter) || stristr($dir, $locfilter)) {

			if (is_dir($loc) && $handle = opendir($loc)) {
				$num = 1;
				while (false !== ($file = readdir($handle))) {
					if (empty($filefilter) || stristr($file, $filefilter)) {
						if ($file != '.' && $file != '..') {
							$key = $dir . ';' . $num++;
							$files[$key] = $file;
						}
					}
				}
			}

		}
	}

	if (isset($files) && $sort == 'dir') {
		xarMod::apiFunc('downloads','admin','natksort',array('arr2sort' => $files));
		return $files;
	} elseif (isset($files)) { 
		natcasesort($files);
		return $files;
	} else {
		return false;
	}

}

?>