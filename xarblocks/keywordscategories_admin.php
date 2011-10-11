<?php
/**
 * Keywords Module Categories Block
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
sys::import('modules.keywords.xarblocks.keywordscategories');

class Keywords_KeywordscategoriesBlockAdmin extends Keywords_KeywordscategoriesBlock implements iBlock
{
    public function modify()
    {
        $vars = $this->getContent();
        $vars['blockid'] = $this->block_id;
        return $vars;
    }
    
    public function update()
    {
        if (!xarVarFetch('refreshtime', 'int:1:', $vars['refreshtime'],1,XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;
    }

}
?>