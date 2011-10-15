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
 * modify block settings
 * @author Jonn Beames et al
 */

    sys::import('modules.publications.xarblocks.featureditems');

    class Publications_FeatureditemsBlockAdmin extends Publications_FeatureditemsBlock
    {
        function modify()
        {
            $data = $this->getContent();
        
            $data['fields'] = array('id', 'name');

            if (!is_array($data['pubstate'])) {
                $statearray = array($data['pubstate']);
            } else {
                $statearray = $data['pubstate'];
            }
        
            if(!empty($data['catfilter'])) {
                $cidsarray = array($data['catfilter']);
            } else {
                $cidsarray = array();
            }

            // Create array based on modifications
            $article_args = array();

            // Only include pubtype if a specific pubtype is selected
            if (!empty($data['pubtype_id'])) {
                $article_args['ptid'] = $data['pubtype_id'];
            }

            // If itemlimit is set to 0, then don't pass to getall
            if ($data['itemlimit'] != 0 ) {
                $article_args['numitems'] = $data['itemlimit'];
            }
        
            // Add the rest of the arguments
            $article_args['cids'] = $cidsarray;
            $article_args['enddate'] = time();
            $article_args['state'] = $statearray;
            $article_args['fields'] = $data['fields'];
            $article_args['sort'] = $data['toptype'];

            $data['filtereditems'] = xarModAPIFunc(
                'publications', 'user', 'getall', $article_args );

    // Check for exceptions
//    if (!isset($vars['filtereditems']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
//        return; // throw back

            // Try to keep the additional headlines select list width less than 50 characters
            for ($idx = 0; $idx < count($data['filtereditems']); $idx++) {
                if (strlen($data['filtereditems'][$idx]['title']) > 50) {
                    $data['filtereditems'][$idx]['title'] = substr($data['filtereditems'][$idx]['title'], 0, 47) . '...';
                }
            }

            $data['pubtypes'] = xarModAPIFunc('publications', 'user', 'get_pubtypes');
            $data['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');
            $data['stateoptions'] = array(
                array('id' => '', 'name' => xarML('All Published')),
                array('id' => '3', 'name' => xarML('Frontpage')),
                array('id' => '2', 'name' => xarML('Approved'))
            );

            $data['sortoptions'] = array(
                array('id' => 'author', 'name' => xarML('Author')),
                array('id' => 'date', 'name' => xarML('Date')),
                array('id' => 'hits', 'name' => xarML('Hit Count')),
                array('id' => 'rating', 'name' => xarML('Rating')),
                array('id' => 'title', 'name' => xarML('Title'))
            );
        
            //Put together the additional featured publications list
            for($idx=0; $idx < count($data['filtereditems']); ++$idx) {
                $data['filtereditems'][$idx]['selected'] = '';
                for($mx=0; $mx < count($data['moreitems']); ++$mx) {
                    if (($data['moreitems'][$mx]) == ($data['filtereditems'][$idx]['id'])) {
                        $data['filtereditems'][$idx]['selected'] = 'selected';
                    }
                }
            }
            $data['morepublications'] = $data['filtereditems'];

            return $data;
        }

        function update(Array $data=array())
        {
            $args = array();
            xarVarFetch('pubtype_id',       'int',       $args['pubtype_id'],      $this->pubtype_id, XARVAR_NOT_REQUIRED);
            xarVarFetch('catfilter',        'id',        $args['catfilter'],       $this->catfilter, XARVAR_NOT_REQUIRED);
            xarVarFetch('nocatlimit',       'checkbox',  $args['nocatlimit'],      $this->nocatlimit, XARVAR_NOT_REQUIRED);
            xarVarFetch('pubstate',         'str',       $args['pubstate'],        $this->pubstate, XARVAR_NOT_REQUIRED);
            xarVarFetch('itemlimit',        'int:1',     $args['itemlimit'],       $this->itemlimit, XARVAR_NOT_REQUIRED);
            xarVarFetch('toptype',  'enum:author:date:hits:rating:title', $args['toptype'], $this->toptype, XARVAR_NOT_REQUIRED);
            xarVarFetch('featuredid',       'id',        $args['featuredid'],      $this->featuredid, XARVAR_NOT_REQUIRED);
            xarVarFetch('alttitle',         'str',       $args['alttitle'],        $this->alttitle, XARVAR_NOT_REQUIRED);
            xarVarFetch('altsummary',       'str',       $args['altsummary'],      $this->altsummary, XARVAR_NOT_REQUIRED);
            xarVarFetch('moreitems',        'list:id',   $args['moreitems'],       $this->moreitems, XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedbod',  'checkbox',  $args['showfeaturedbod'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showfeaturedsum',  'checkbox',  $args['showfeaturedsum'], 0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showsummary',      'checkbox',  $args['showsummary'],     0, XARVAR_NOT_REQUIRED);
            xarVarFetch('showvalue',        'checkbox',  $args['showvalue'],       0, XARVAR_NOT_REQUIRED);
            xarVarFetch('linkpubtype',      'checkbox',  $args['linkpubtype'],     0, XARVAR_NOT_REQUIRED);
            xarVarFetch('linkcat',          'checkbox',  $args['linkcat'],         0, XARVAR_NOT_REQUIRED);
            
            $this->setContent($args);
            return true;        
        }
}
?>