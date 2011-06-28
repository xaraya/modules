<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */
/**
 * 
 */
function content_adminapi_getdisplaytemplates($args)
{
    $ctype = '';

    extract($args);

    if(!empty($ctype)) $ctype = '-' . $ctype;
    
    $paths1 = xarMod::apiFunc('dynamicdata','admin','browse', array(
        'basedir' =>  xarTplGetThemeDir() . '/modules/content/', 
        'filetype' => 'xt'
    )); 
 
    $paths2 = xarMod::apiFunc('dynamicdata','admin','browse', array(
        'basedir' =>  sys::code() . 'modules/content/xartemplates/', 
        'filetype' => 'xt'
    ));
 
    $paths = array_merge($paths1,$paths2);

    $arr = array();

    if (!empty($paths)) {
        foreach ($paths as $path) {
            if (strstr($path, 'user-display'.$ctype)) {
                $p = str_replace('.xt','',$path);
                $p = str_replace('user-display-','',$p);
                $arr[$p] = $p;
            }
        }
    }

    ksort($arr);

    $first = array('-inherit-' => '-inherit-', 'user-display' => 'user-display');

    $arr = array_merge($first, $arr);
 
    return $arr;

}