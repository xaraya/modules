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

function publications_adminapi_read_file($args)
{
    if (empty($args['file'])) return false;
    try {
        $data = "";
        if (file_exists($args['file'])) {
            $fp = fopen($args['file'], "rb");
            while (!feof($fp)) {
                $filestring = fread($fp, 4096);
                $data .=  $filestring;
            }
            fclose ($fp);
        }
        return $data ;
    } catch (Exception $e) {
        return '';
    }
}

?>