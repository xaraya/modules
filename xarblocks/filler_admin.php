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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.publications.xarblocks.filler');

class Publications_FillerBlockAdmin extends Publications_FillerBlock
{
    function modify()
    {
        $data = $this->getContent();

        if (!is_array($data['pubstate'])) {
            $statearray = array($data['pubstate']);
        } else {
            $statearray = $data['pubstate'];
        }
        
        // Only include pubtype if a specific pubtype is selected
        if (!empty($data['pubtype_id'])) {
            $article_args['ptid'] = $data['pubtype_id'];
        }
        
        // Add the rest of the arguments
        $article_args['state'] = $statearray;

        $data['filtereditems'] = xarMod::apiFunc(
            'publications', 'user', 'getall', $article_args );

        $data['pubtypes'] = xarMod::apiFunc('publications', 'user', 'get_pubtypes');
        $data['stateoptions'] = array(
            array('id' => '', 'name' => xarML('All Published')),
            array('id' => '3', 'name' => xarML('Frontpage')),
            array('id' => '2', 'name' => xarML('Approved'))
        );

        return $data;
    }

        function update(Array $data=array())
        {
            $args = array();
            xarVar::fetch('pubtype_id',       'int',       $args['pubtype_id'],      $this->pubtype_id, XARVAR_NOT_REQUIRED);
            xarVar::fetch('pubstate',         'str',       $args['pubstate'],        $this->pubstate, XARVAR_NOT_REQUIRED);
            xarVar::fetch('displaytype',      'str',       $args['displaytype'],     $this->displaytype, XARVAR_NOT_REQUIRED);
            xarVar::fetch('fillerid',         'id',        $args['fillerid'],        $this->fillerid, XARVAR_NOT_REQUIRED);
            xarVar::fetch('alttitle',         'str',       $args['alttitle'],        $this->alttitle, XARVAR_NOT_REQUIRED);
            xarVar::fetch('alttext',          'str',       $args['alttext'],         $this->alttext, XARVAR_NOT_REQUIRED);
            $this->setContent($args);
            return true;
        }
}
?>