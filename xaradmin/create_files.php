<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    sys::import('modules.dynamicdata.class.objects.master');

    function sitemapper_admin_create_files()
    {
        // Security Check
        if (!xarSecurity::check('ManageSitemapper')) {
            return;
        }
        
        // Get the link data
        $object = DataObjectMaster::getObjectList(array('name' => 'sitemapper_links'));
        $where = 'state = 3';
        $data['items'] = $object->getItems(array('where' => $where));

        if (empty($items)) {

            // Assemble the string to be compiled for the input template
            sys::import('xaraya.templating.compiler');
            $blCompiler = XarayaCompiler::instance();
            $tplInputString  = '<?xml version="1.0" encoding="utf-8"?>';
            $tplInputString .= '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
            $tplInputString .= xarModVars::get('sitemapper', 'template');
            $tplInputString .= '</xar:template>';
            
            // Run the template and its data through the compiler
            try {
                $tplString = $blCompiler->compilestring($tplInputString);
                $tplString = xarTpl::string($tplString, $data);
            } catch (Exception $e) {
                var_dump($e->getMessage());
                exit;
            }
        }

        // Now create files
        if (xarModVars::get('sitemapper', 'file_create_xml')) {
            $filename = xarModVars::get('sitemapper', 'xml_filename');
            if (empty($filename)) {
                throw new Exception(xarML("Missing file name"));
            }
            try {
                file_put_contents($filename . ".xml", $tplString);
            } catch (Exception $e) {
                return xarTpl::module('sitemapper', 'user', 'errors', array('layout' => 'no_permission'));
            }
        }
        if (xarModVars::get('sitemapper', 'file_create_zip')) {
            $filename = xarModVars::get('sitemapper', 'zip_filename');
            if (empty($filename)) {
                throw new Exception(xarML("Missing file name"));
            }
            try {
                file_put_contents($filename . ".gz", gzencode($tplString));
            } catch (Exception $e) {
                return xarTpl::module('sitemapper', 'user', 'errors', array('layout' => 'no_permission'));
            }
        }
        
        return $data;
    }
