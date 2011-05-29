<?php
/**
 * Related Items Block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 * @author Marc Lutolf (mfl@netspan.ch)
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
            if (!isset($data['showvalue']))        $data['showvalue'] = $this->showvalue;

            if (!isset($data['showpubtype']))      $data['showpubtype'] = $this->show_pubtype;
            if (!isset($data['showcategory']))     $data['showcategory'] = $this->show_category;
            if (!isset($data['showauthor']))       $data['showauthor'] = $this->show_author;
            return $data;
        }

        public function update(Array $data=array())
        {
            $data = parent::update($data);
            $args = array();
            xarVarFetch('numitems', 'int', $args['numitems'], $this->numitems, XARVAR_NOT_REQUIRED);
            xarVarFetch('showvalue', 'checkbox', $args['showvalue'], 0, XARVAR_NOT_REQUIRED);

            xarVarFetch('showpubtype',  'checkbox', $args['showpubtype'],  0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showcategory', 'checkbox', $args['showcategory'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showauthor',   'checkbox', $args['showauthor'],   0, XARVAR_NOT_REQUIRED);

            $data['content'] = $args;
            return $data;
        }
    }
?>