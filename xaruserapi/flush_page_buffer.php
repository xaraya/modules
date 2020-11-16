 <?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

 function uploads_userapi_flush_page_buffer(/*VOID*/)
 {
     if (ini_get('output_handler') == 'ob_gzhandler' || ini_get('zlib.output_compression') == true) {
         do {
             $contents = ob_get_contents();
             if (!strlen($contents)) {
                 // Assume we have nothing to store
                 $pageBuffer[] = '';
                 break;
             } else {
                 $pageBuffer[] = $contents;
             }
         } while (@ob_end_clean());
     } else {
         do {
             $pageBuffer[] = ob_get_contents();
         } while (@ob_end_clean());
     }

     $buffer = array_reverse($pageBuffer);

     return $buffer;
 }
?>