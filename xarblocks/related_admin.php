<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
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
    public function modify(array $data=[])
    {
        $data = $this->getContent();

        return $data;
    }

    public function update(array $data=[])
    {
        $args = [];
        xarVar::fetch('numitems', 'int', $args['numitems'], $this->numitems, xarVar::NOT_REQUIRED);
        xarVar::fetch('showvalue', 'checkbox', $args['showvalue'], 0, xarVar::NOT_REQUIRED);

        xarVar::fetch('showpubtype', 'checkbox', $args['showpubtype'], 0, xarVar::NOT_REQUIRED);
        xarVar::fetch('showcategory', 'checkbox', $args['showcategory'], 0, xarVar::NOT_REQUIRED);
        xarVar::fetch('showauthor', 'checkbox', $args['showauthor'], 0, xarVar::NOT_REQUIRED);
        $this->setContent($args);
        return true;
    }
}
