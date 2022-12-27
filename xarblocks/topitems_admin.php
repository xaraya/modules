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
sys::import('modules.publications.xarblocks.topitems');

class Publications_TopitemsBlockAdmin extends Publications_TopitemsBlock
{
    public function modify(array $data=[])
    {
        $data = $this->getContent();
        return $data;
    }

    public function update(array $data=[])
    {
        $args = [];

        if (!xarVar::fetch('numitems', 'int:1:200', $args['numitems'], $this->numitems, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('pubtype_id', 'id', $args['pubtype_id'], $this->pubtype_id, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('linkpubtype', 'checkbox', $args['linkpubtype'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('nopublimit', 'checkbox', $args['nopublimit'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('catfilter', 'id', $args['catfilter'], $this->catfilter, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('includechildren', 'checkbox', $args['includechildren'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('nocatlimit', 'checkbox', $args['nocatlimit'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('linkcat', 'checkbox', $args['linkcat'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('dynamictitle', 'checkbox', $args['dynamictitle'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showsummary', 'checkbox', $args['showsummary'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showdynamic', 'checkbox', $args['showdynamic'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showvalue', 'checkbox', $args['showvalue'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('pubstate', 'strlist:,:int:1:4', $args['pubstate'], $this->pubstate, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('toptype', 'enum:author:date:hits:rating:title', $args['toptype'], $this->toptype, xarVar::NOT_REQUIRED)) {
            return;
        }

        if ($args['nopublimit'] == true) {
            $args['pubtype_id'] = 0;
        }
        if ($args['nocatlimit']) {
            $args['catfilter'] = 1;
            $args['includechildren'] = 0;
        }
        if ($args['includechildren']) {
            $args['linkcat'] = 0;
        }
        $this->setContent($args);
        return true;
    }
}
