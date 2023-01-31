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
        public function modify(Array $data=array())
        {
            $data = $this->getContent();
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
            xarVar::fetch('locale', 'str', $data['locale'], '', xarVar::NOT_REQUIRED);
            xarVar::fetch('alttitle', 'str', $data['alttitle'], '', xarVar::NOT_REQUIRED);
            xarVar::fetch('altsummary', 'str', $data['altsummary'], '', xarVar::NOT_REQUIRED);
            xarVar::fetch('showtitle', 'checkbox', $data['showtitle'], false, xarVar::NOT_REQUIRED);
            xarVar::fetch('showsummary', 'checkbox', $data['showsummary'], false, xarVar::NOT_REQUIRED);
            xarVar::fetch('showpubdate', 'checkbox', $data['showpubdate'], false, xarVar::NOT_REQUIRED);
            xarVar::fetch('showauthor', 'checkbox', $data['showauthor'], false, xarVar::NOT_REQUIRED);
            xarVar::fetch('showsubmit', 'checkbox', $data['showsubmit'], false, xarVar::NOT_REQUIRED);
            $this->setContent($data);
            return true;
        }
    }
?>