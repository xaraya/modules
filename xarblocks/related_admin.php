<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * initialise block
 * @author Jim McDonald
 */
    sys::import('modules.publications.xarblocks.related');

    class Publications_RelatedBlockAdmin extends Publications_RelatedBlock
    {
        public function modify(Array $data=array())
        {
            $data = $this->getContent();

            return $data;
        }

        public function update(Array $data=array())
        {
            $args = array();
            xarVarFetch('numitems', 'int', $args['numitems'], $this->numitems, XARVAR_NOT_REQUIRED);
            xarVarFetch('showvalue', 'checkbox', $args['showvalue'], 0, XARVAR_NOT_REQUIRED);

            xarVarFetch('showpubtype',  'checkbox', $args['showpubtype'],  0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showcategory', 'checkbox', $args['showcategory'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showauthor',   'checkbox', $args['showauthor'],   0, XARVAR_NOT_REQUIRED);
            $this->setContent($args);
            return true;
        }
    }
?>