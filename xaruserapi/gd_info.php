<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */

function images_userapi_gd_info()
{
    if (function_exists('gd_info')) {
        $gd_info = gd_info();

    } else {
        $gd_info = array(
            'GD Version'          => 'not supported',
            'FreeType Support'    => FALSE,
            'T1Lib Support'       => FALSE,
            'GIF Read Support'    => FALSE,
            'GIF Create Support'  => FALSE,
            'JPG Support'         => FALSE,
            'PNG Support'         => FALSE,
            'WBMP Support'        => FALSE,
            'XBM Support'         => FALSE);

        ob_start();
        phpinfo(INFO_MODULES);
        $string = ob_get_contents();
        ob_end_clean();

        $pieces = explode('<h2>', $string);
        foreach ($pieces as $key => $piece) {

            if (!stristr($piece, 'module_gd')) {
                unset($pieces[$key]);
            } else {
                $gd_pre = $piece;
                unset($pieces);
                break;
            }
        }

        if (isset($gd_pre)) {
            $gd_multi = explode("\n", $gd_pre);

            foreach($gd_multi as $key => $line) {
                // skip the first & second key key cuz they're just garbage
                if ($key <= 1)  {
                    continue;
                }

                eregi('\<tr\>\<td class="e"\>([^<]*)\<\/td\>\<td class="v"\>([^<]*)\<\/td\>\<\/tr\>', $line, $matches);

                $key   = trim($matches[1]);
                $value = trim($matches[2]);

                switch($value) {
                    case 'enabled':
                        $value = TRUE;
                        break;
                    case 'disabled':
                        $value = FALSE;
                        break;
                }
                $gd_info[$key] = $value;
            }
        }
    }

    if (function_exists('imagetypes')) {
        $gd_info['typesBitmask'] = imagetypes();
    } else {
        $gd_info['typesBitmask'] = 0;
    }

    return $gd_info;
}


?>
