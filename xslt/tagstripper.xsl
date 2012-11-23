<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:transform [
<!ENTITY nl "&#xd;&#xa;">
]>
<xsl:transform version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xar="http://xaraya.com/2004/blocklayout"
  xmlns:php="http://php.net/xsl"
  exclude-result-prefixes="php xar">
  <!-- rvm@marisdev.com 2012-11-23 -->

  <xsl:strip-space elements="*"/>
  <xsl:output encoding="UTF-8" />

  <xsl:param name="stripTag"/>
  <xsl:param name="stripAttrs"/>

  <xsl:variable name="tagstostrip" select="normalize-space($stripTag)"/>
  <xsl:variable name="attrstostrip" select="normalize-space($stripAttrs)"/>

  <xsl:template match="node()|@*" name="identity">
    <xsl:copy>
      <xsl:apply-templates select="node()|@*"/>
    </xsl:copy>
  </xsl:template>

  <xsl:template name="striptag">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template name="stripattrs">
    <xsl:copy><xsl:apply-templates/></xsl:copy>
  </xsl:template>

  <xsl:template match="*">
    <xsl:choose>
      <xsl:when test="contains( $tagstostrip, concat('| ', local-name(), ' |'))">
        <xsl:call-template name="striptag"/>
      </xsl:when>
      <xsl:when test="contains( $attrstostrip, concat('| ', local-name(), ' |'))">
        <xsl:call-template name="stripattrs"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:call-template name="identity"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:transform>