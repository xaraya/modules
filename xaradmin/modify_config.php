<?php
/**
 * Reports module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage reports
 * @link http://xaraya.com/index.php/release/4704.html
 * @author Marcel van der Boom <marcel@hsdev.com>
 */
/**
 * Modify config
 * @todo MichelV Check for required php extensions
 */
function reports_admin_modify_config()
{
    // Check whether we can cache reports data
    $cacheDir = xarCoreGetVarDirPath().'/cache/reports';
    if(!file_exists($cacheDir)) {
        // Try to create it
        if(!mkdir($cacheDir)) {
          $msg = xarML("Reports cache directory non existant and unable to create ($cacheDir)");
          xarErrorSet(XAR_USER_EXCEPTION, 'FILE_NOT_FOUND', $msg);
          return;
        }
    }

    if(!is_writable($cacheDir)) {
        // We aren't going to try to make it writable
        $msg = xarML("Reports cache directory is not writable ($cacheDir)");
        xarErrorSet(XAR_USER_EXCEPTION, 'CONFIG_ERROR', $msg);
        return;
    }


    $backends= array( array('id'=>'ezpdf',
                            'name'=> xarML('EzPDF (pure PHP)'),
                            'available' => false),
                      array('id'=>'yaps',
                            'name'=> xarML('YaPS (GS based)'),
                            'available' => false),
                      array('id'=>'pdflib',
                            'name'=> xarML('pdfLib (C-library)'),
                            'available' => false),
                      array('id' => 'fop',
                            'name' => xarML('Formatting Objects Processor (fop)'),
                            'available' => true)
                      );
    $format = xarModGetVar('reports','default_output');
    if(empty($format)) {
        xarModSetVar('reports','default_output','html');
    }
    $itemsperpage = xarModGetVar('reports','itemsperpage');
    if(empty($itemsperpage)) {
        xarModSetVar('reports','itemsperpage', 10);
    }
    $fop_location = xarModGetVar('reports','fop_location');
    if(empty($fop_location)) {
        xarModSetVar('reports','fop_location','c:/apps/fop/');
    }
    // Get the PHP version
    if (function_exists('version_compare')) {
        if (version_compare(PHP_VERSION,'5','>=')) $PHPVersion5 = true;
    }
    // This is called xsl in PHP5.x Should check for that when php version is 5 or higher
    $xsltextension  = extension_loaded ('xslt');
    $xslextension  = extension_loaded ('xsl');
    $data = array('authid' => xarSecGenAuthKey(),
                  'rep_location' => xarModGetVar('reports','reports_location'),
                  'img_location' => xarModGetVar('reports','images_location'),
                  'fop_location' => xarModGetVar('reports','fop_location'),
                  'backends' => $backends,
                  'selectedbackend' => xarModGetVar('reports','pdf_backend'),
                  'format' => $format,
                  'itemsperpage' => xarModGetVar('reports','itemsperpage'),
                  'xsltextension'  => $xsltextension,
                  'xslextension'  => $xslextension,
                  'PHPVersion5' => $PHPVersion5
                  );

    return $data;
}

?>