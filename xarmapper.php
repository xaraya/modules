<?php
/**
 * Request mapper + default cache settings for the dyn_example module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage dyn_example
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub
 */
/**
 * Request mapper + default cache settings for the dyn_example module - warning: this is still subject to change !
 *
 * @return array with the request mapper for dyn_example
 */
function dyn_example_xarmapper()
{
    $module = 'dyn_example';

    // Initialize array
    $xarmapper = array();

/* Cfr. xarcachemanager module caching config (and future request mapper ?)

    // Hint: generate this dynamically first, and save the output in your xarmapper.php

// CHECKME: do we want/need the api functions too someday ?
    //$functiontypes = array('user','admin','userapi','adminapi');
// CHECKME: do we want/need the admin gui functions too someday ?
    $functiontypes = array('user','admin');

    foreach ($functiontypes as $type) {
        // find the $type functions
        $typedir = realpath(sys::code() . 'modules/' . $module . '/xar' . $type . '/');
        if ($typedir && $dh = @opendir($typedir)) {
            while(($filename = @readdir($dh)) !== false) {
                // Got a file or directory.
                $thisfile = $typedir . '/' . $filename;
                if (is_file($thisfile) && preg_match('/^(.+)\.php$/', $filename, $matches)) {
                    if ($type == 'user' || $type == 'admin') {
                        // try to find all xarVarFetch() parameters
                        $params = array();
                        $content = implode('',file($thisfile));
                        if (preg_match_all("/xarVarFetch\('([^']+)'/", $content, $params)) {
                            $paramslist = implode(',', $params[1]);
                        } else {
                            $paramslist = '';
                        }
                        // admin functions are not cacheable by default
                        if ($type == 'admin') {
                            $nocache = 1;
                            $usershared = 0;
                        } else {
                            $nocache = 0;
                            $usershared = 1;
                        }
                    } else {
                        // api and other functions are NOT cacheable by default (currently unused)
                        $paramslist = '';
                        $nocache = 1;
                        $usershared = 0;
                    }
                    $xarmapper[$type][$matches[1]] = array('params' => $paramslist, 'nocache' => $nocache, 'usershared' => $usershared, 'cacheexpire' => '');
                }
            }
            closedir($dh);
        }
    }
    // Output the generated xarmapper for now (to be copied to your xarmapper.php)
    $output = str_replace("\n","\n    ", str_replace('  ', '    ', var_export($xarmapper, true)));
    echo '<textarea rows="20" cols="80">
    // Please verify those function parameters
    $xarmapper = ' . $output . ';</textarea>';
*/

    // Please verify those function parameters
    $xarmapper = array (
        'user' => 
        array (
            'display' => 
            array (
                'params' => 'itemid',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'main' => 
            array (
                'params' => 'startnum',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'view' => 
            array (
                'params' => 'tab,startnum',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
        ),
        'admin' => 
        array (
            'delete' => 
            array (
                'params' => 'name,itemid,confirm',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'main' => 
            array (
                'params' => '',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'modify' => 
            array (
                'params' => 'itemid,name,confirm,preview,confirm',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'modifyconfig' => 
            array (
                'params' => 'phase,bold',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'new' => 
            array (
                'params' => 'preview,confirm',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'overview' => 
            array (
                'params' => '',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'view' => 
            array (
                'params' => 'startnum',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
        ),
    );

    // Return mapper information
    return $xarmapper;
}

?>
