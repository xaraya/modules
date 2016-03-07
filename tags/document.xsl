<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet [
<!ENTITY nl "&#xd;&#xa;">
]>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xar="http://xaraya.com/2004/blocklayout"
    xmlns:php="http://php.net/xsl"
    xmlns="http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd"
    exclude-result-prefixes="php xar">

  <xsl:template match="xar:ISO20022Document">
    <xsl:element name="Document" xmlns="http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.six-interbank-clearing.com/de/pain.001.001.03.ch.02.xsd  pain.001.001.03.ch.02.xsd">
      <xsl:apply-templates/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="xar:isoelement">
    <!--
      This tag only allows a name attribute that is a string (for now)
      Need to look at preprocess and postprocess in xsltransformer.php when trying to enhance further
    -->
    <xsl:element name="{@name}">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
</xsl:stylesheet>