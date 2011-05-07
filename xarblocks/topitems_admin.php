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
    sys::import('modules.publications.xarblocks.topitems');

    class Publications_TopitemsBlockAdmin extends Publications_TopitemsBlock
    {
        function modify(Array $data=array())
        {
            $data = parent::modify($data);
            if (!isset($data['numitems']))        $data['numitems'] = $this->numitems;
            if (!isset($data['pubtype_id']))      $data['pubtype_id'] = $this->pubtype_id;
            if (!isset($data['linkpubtype']))     $data['linkpubtype'] = $this->linkpubtype;
            if (!isset($data['nopublimit']))      $data['nopublimit'] = $this->nopublimit;
            if (!isset($data['catfilter']))       $data['catfilter'] = $this->catfilter;
            if (!isset($data['includechildren'])) $data['includechildren'] = $this->includechildren;
            if (!isset($data['nocatlimit']))      $data['nocatlimit'] = $this->nocatlimit;
            if (!isset($data['linkcat']))         $data['linkcat'] = $this->linkcat;
            if (!isset($data['dynamictitle']))    $data['dynamictitle'] = $this->dynamictitle;
            if (!isset($data['showsummary']))     $data['showsummary'] = $this->showsummary;
            if (!isset($data['showdynamic']))     $data['showdynamic'] = $this->showdynamic;
            if (!isset($data['showvalue']))       $data['showvalue'] = $this->showvalue;
            if (!isset($data['state']))           $data['state'] = $this->state;
            if (!isset($data['toptype']))         $data['toptype'] = $this->toptype;
            return $data;
        }

        public function update(Array $data=array())
        {
            $data = parent::update($data);
            $args = array();
            
            if (!xarVarFetch('numitems',        'int:1:200', $args['numitems'],        $this->numitems, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('pubtype_id',      'id',        $args['pubtype_id'],      $this->pubtype_id, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('linkpubtype',     'checkbox',  $args['linkpubtype'],     $this->linkpubtype, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('nopublimit',      'checkbox',  $args['nopublimit'],      $this->nopublimit, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('catfilter',       'id',        $args['catfilter'],       $this->catfilter, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('includechildren', 'checkbox',  $args['includechildren'], $this->includechildren, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('nocatlimit',      'checkbox',  $args['nocatlimit'],      $this->nocatlimit, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('linkcat',         'checkbox',  $args['linkcat'],         $this->linkcat, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('dynamictitle',    'checkbox',  $args['dynamictitle'],    $this->dynamictitle, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showsummary',     'checkbox',  $args['showsummary'],     $this->showsummary, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showdynamic',     'checkbox',  $args['showdynamic'],     $this->showdynamic, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showvalue',       'checkbox',  $args['showvalue'],       $this->showvalue, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('state',           'strlist:,:int:1:4', $args['state'],   $this->state, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('toptype',         'enum:author:date:hits:rating:title', $args['toptype'], $this->toptype, XARVAR_NOT_REQUIRED)) {return;}

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
            $data['content'] = $args;
            return $data;
        }
    }
?>