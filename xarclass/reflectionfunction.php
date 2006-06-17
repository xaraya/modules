<?php
/**
 * ReflectionFunction extension
 * 
 * Extension of the ReflectionFunction class
 *
 * @package autodoc
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
class ad_ReflectionFunction extends ReflectionFunction
{
    function toArray()
    {
        $frParams = $this->getParameters();
        $params = array();
        foreach($frParams as $frParam) {
            $params[] = $frParam->toArray();
        }
        
        $info = array (
                       'id'         => ReflectionInfo::GetId($this->getName(),ReflectionInfo::FNC),
                       'type'       => 'function',
                       'name'       => $this->getName(),
                       'file'       => $this->getFileName(),
                       'start'      => $this->getStartLine(),
                       'end'        => $this->getEndLine(),
                       'returnsref' => $this->returnsReference(),
                       'params'     => $params,
                       'statics'    => $this->getStaticVariables(),
                       'doc'        => $this->getDocComment(),
                       'raw'        => $this->export($this->getName(),true)
                       );
        
        return $info;
    }

    function &getParameters()
    {
        // This gets an array of ReflectionParameter objects
        $params = parent::getParameters();
        $pars = array();
        foreach($params as $key => $param) {
            if($param->getName()) {
                $pars[$key] = new ad_ReflectionParameter($this->getName(),$param->getName());
            }
        }
        return $pars;
    }
}
?>