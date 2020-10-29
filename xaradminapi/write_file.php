<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function publications_adminapi_write_file($args)
{
    if (empty($args['file'])) {
        return false;
    }
    try {
        $dir = dirname($args['file']);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $fp = fopen($args['file'], "wb");
    
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($args['data']);
        }
        fwrite($fp, $args['data']);
        fclose($fp);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
