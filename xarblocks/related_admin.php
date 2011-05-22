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
    sys::import('modules.publications.xarblocks.related');

    class Publications_RelatedBlockAdmin extends Publications_RelatedBlock
    {
        public function modify(Array $data=array())
        {
            $data = parent::modify($data);
            if (empty($data['numitems']))          $data['numitems'] = $this->numitems;
            if (empty($data['showvalue']))         $data['showvalue'] = $this->showvalue;
            return $data;
        }

        public function update(Array $data=array())
        {
            xarVarFetch('numitems', 'int', $data['numitems'], $this->numitems, XARVAR_NOT_REQUIRED);
            xarVarFetch('showvalue', 'checkbox', $data['showvalue'], $this->showvalue, XARVAR_NOT_REQUIRED);

            return parent::update($data);
        }
    }
?>