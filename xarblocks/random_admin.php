<?php
/**
 * Top Items Block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 *
 */
/**
 * initialise block
 * @author Jim McDonald
 */
    sys::import('modules.publications.xarblocks.random');

    class Publications_RandomBlockAdmin extends Publications_RandomBlock
    {
        public function modify(Array $data=array())
        {
            $data = parent::modify($data);
            if(!empty($data['catfilter'])) {
                $cidsarray = array($data['catfilter']);
            } else {
                $cidsarray = array();
            }

            $data['locales'] = xarMLSListSiteLocales();
            asort($data['locales']);

            return $data;
        }

        public function update(Array $data=array())
        {
            xarVarFetch('locale', 'str', $data['locale'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle', 'str', $data['alttitle'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('altsummary', 'str', $data['altsummary'], '', XARVAR_NOT_REQUIRED);
            xarVarFetch('showtitle', 'checkbox', $data['showtitle'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsummary', 'checkbox', $data['showsummary'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showpubdate', 'checkbox', $data['showpubdate'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showauthor', 'checkbox', $data['showauthor'], false, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsubmit', 'checkbox', $data['showsubmit'], false, XARVAR_NOT_REQUIRED);

            return parent::update($data);
        }
    }
?>