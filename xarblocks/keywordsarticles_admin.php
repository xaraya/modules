<?php
/**
 * Keywords Module Articles Block
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Original Author of file: Camille Perinel
 * Mostly taken from the topitems.php block of the articles module.(See credits)
 */
sys::import('modules.keywords.xarblocks.keywordsarticles');

class Keywords_KeywordsarticlesBlockAdmin extends Keywords_KeywordsarticlesBlock implements iBlock
{
    public function modify()
    {
        $vars = $this->getContent();
        $vars['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
        $vars['categorylist'] = xarModAPIFunc('categories','user','getcat');
        $vars['statusoptions'] = array(array('id' => '3,2',
                                         'name' => xarML('All Published')),
                                   array('id' => '3',
                                         'name' => xarML('Frontpage')),
                                   array('id' => '2',
                                         'name' => xarML('Approved'))
                                  );

        $vars['blockid'] = $this->block_id;
        // Return output
        return $vars;        
    }
    
    public function update()
    {
        if (!xarVarFetch('ptid', 'id', $vars['ptid'],$this->ptid, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('cid', 'int:1:', $vars['cid'],$this->cid, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('status', 'str:1:', $vars['status'], $this->status, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('refreshtime', 'int:1:', $vars['refreshtime'],1,XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true; 
    }
}
?>