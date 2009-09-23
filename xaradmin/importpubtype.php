<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Import an object definition or an object item from XML
 */
function articles_admin_importpubtype($args)
{
    if (!xarSecurityCheck('AdminArticles')) return;

    if(!xarVarFetch('import', 'isset', $import,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('xml', 'isset', $xml,  NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    if (!empty($xml)) {
        // remove &nbsp; resp. &#160;
        $xml = trim($xml, "\xc2\xa0");
    }

    $data = array();
    $data['menutitle'] = xarML('Dynamic Data Utilities');

    $data['warning'] = '';
    $data['options'] = array();

    $basedir = sys::code() . 'modules/articles';
    $filetype = 'xml';
    $files = xarModAPIFunc('dynamicdata','admin','browse',
                           array('basedir' => $basedir,
                                 'filetype' => $filetype));
    if (!isset($files) || count($files) < 1) {
        $files = array();
        $data['warning'] = xarML('There are currently no XML files available for import in "#(1)"',$basedir);
    }

    if (!empty($import) || !empty($xml)) {
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        if (!empty($import)) {
            $found = '';
            foreach ($files as $file) {
                if ($file == $import) {
                    $found = $file;
                    break;
                }
            }
            if (empty($found) || !file_exists($basedir . '/' . $file)) {
                $msg = xarML('File not found');
                throw new BadParameterException(null,$msg);
            }
            $ptid = xarModAPIFunc('articles','admin','importpubtype',
                                  array('file' => $basedir . '/' . $file));
        } else {
            $ptid = xarModAPIFunc('articles','admin','importpubtype',
                                  array('xml' => $xml));
        }
        if (empty($ptid)) return;

        $data['warning'] = xarML('Publication type #(1) was successfully imported',$ptid);
    }

    natsort($files);
    array_unshift($files,'');
    foreach ($files as $file) {
         $data['options'][] = array('id' => $file,
                                    'name' => $file);
    }

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>