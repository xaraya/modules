<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * initialise block
 * @author Jim McDonald
 */
    sys::import('modules.publications.xarblocks.random');

    class Publications_RandomBlockAdmin extends Publications_RandomBlock
    {
        public function modify(array $data=array())
        {
            $data = $this->getContent();
            if (!empty($data['catfilter'])) {
                $cidsarray = array($data['catfilter']);
            } else {
                $cidsarray = array();
            }

            $data['locales'] = xarMLS::listSiteLocales();
            asort($data['locales']);

            return $data;
        }

        public function update(array $data=array())
        {
            xarVar::fetch('locale', 'str', $data['locale'], '', XARVAR_NOT_REQUIRED);
            xarVar::fetch('alttitle', 'str', $data['alttitle'], '', XARVAR_NOT_REQUIRED);
            xarVar::fetch('altsummary', 'str', $data['altsummary'], '', XARVAR_NOT_REQUIRED);
            xarVar::fetch('showtitle', 'checkbox', $data['showtitle'], false, XARVAR_NOT_REQUIRED);
            xarVar::fetch('showsummary', 'checkbox', $data['showsummary'], false, XARVAR_NOT_REQUIRED);
            xarVar::fetch('showpubdate', 'checkbox', $data['showpubdate'], false, XARVAR_NOT_REQUIRED);
            xarVar::fetch('showauthor', 'checkbox', $data['showauthor'], false, XARVAR_NOT_REQUIRED);
            xarVar::fetch('showsubmit', 'checkbox', $data['showsubmit'], false, XARVAR_NOT_REQUIRED);
            $this->setContent($data);
            return true;
        }
    }
