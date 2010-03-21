<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet [
<!ENTITY nl "&#xd;&#xa;">
]>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xar="http://xaraya.com/2004/blocklayout"
    xmlns:php="http://php.net/xsl"
    exclude-result-prefixes="php xar">

  <xsl:template match="xar:workflow-activity">
    <xsl:processing-instruction name="php">
      <xsl:text>echo xarMod::apiFunc('workflow','user','showactivity',</xsl:text>
        <xsl:call-template name="atts2args">
          <xsl:with-param name="nodeset" select="@*"/>
        </xsl:call-template>
      <xsl:text>);</xsl:text>
    </xsl:processing-instruction>
  </xsl:template>

  <xsl:template match="xar:workflow-status">
    <xsl:processing-instruction name="php">
      <xsl:text>echo xarMod::apiFunc('workflow','user','showstatus',</xsl:text>
        <xsl:call-template name="atts2args">
          <xsl:with-param name="nodeset" select="@*"/>
        </xsl:call-template>
      <xsl:text>);</xsl:text>
    </xsl:processing-instruction>
  </xsl:template>

  <xsl:template match="xar:workflow-instances">
    <xsl:processing-instruction name="php">
      <xsl:text>echo xarMod::apiFunc('workflow','user','showinstances',</xsl:text>
        <xsl:call-template name="atts2args">
          <xsl:with-param name="nodeset" select="@*"/>
        </xsl:call-template>
      <xsl:text>);</xsl:text>
    </xsl:processing-instruction>
  </xsl:template>

</xsl:stylesheet>
