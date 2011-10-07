<?php
/**
 * Filler item
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 *
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

    sys::import('modules.publications.xarblocks.filler');

    class Publications_FillerBlockAdmin extends Publications_FillerBlock
    {
        function modify(Array $data=array())
        {
            $data = parent::modify($data);

            if (!isset($data['pubtype_id']))        $data['pubtype_id'] = $this->pubtype_id;
            if (!isset($data['state']))             $data['state'] = $this->state;
            if (!isset($data['fillerid']))          $data['fillerid'] = $this->fillerid;
            if (!isset($data['displaytype']))       $data['displaytype'] = $this->displaytype;
            if (!isset($data['alttitle']))          $data['alttitle'] = $this->alttitle;
            if (!isset($data['alttext']))        $data['alttext'] = $this->alttext;

            if (!is_array($data['state'])) {
                $statearray = array($data['state']);
            } else {
                $statearray = $data['state'];
            }
        
            // Only include pubtype if a specific pubtype is selected
            if (!empty($data['pubtype_id'])) {
                $article_args['ptid'] = $data['pubtype_id'];
            }
        
            // Add the rest of the arguments
            $article_args['state'] = $statearray;

            $data['filtereditems'] = xarModAPIFunc(
                'publications', 'user', 'getall', $article_args );

            $data['pubtypes'] = xarModAPIFunc('publications', 'user', 'get_pubtypes');
            $data['stateoptions'] = array(
                array('id' => '', 'name' => xarML('All Published')),
                array('id' => '3', 'name' => xarML('Frontpage')),
                array('id' => '2', 'name' => xarML('Approved'))
            );

            $data['blockid'] = $data['bid'];
            return $data;
        }

        function update(Array $data=array())
        {
            $data = parent::update($data);
            $args = array();
            xarVarFetch('pubtype_id',       'int',       $args['pubtype_id'],      $this->pubtype_id, XARVAR_NOT_REQUIRED);
            xarVarFetch('state',            'str',       $args['state'],           $this->state, XARVAR_NOT_REQUIRED);
            xarVarFetch('displaytype',      'str',       $args['displaytype'],     $this->displaytype, XARVAR_NOT_REQUIRED);
            xarVarFetch('fillerid',         'id',        $args['fillerid'],        $this->fillerid, XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle',         'str',       $args['alttitle'],        $this->alttitle, XARVAR_NOT_REQUIRED);
            xarVarFetch('alttext',          'str',       $args['alttext'],         $this->alttext, XARVAR_NOT_REQUIRED);
        
            $data['content'] = $args;
            return $data;
        }
}
?>