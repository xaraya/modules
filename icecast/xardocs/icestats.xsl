<xsl:stylesheet xmlns:xsl = "http://www.w3.org/1999/XSL/Transform" version = "1.0" >
<xsl:output method="html" indent="yes" />
<xsl:template match = "/icestats" >


<!-- Note: that what i do here is kinda hacked.. just something to do 
   cuz it's easier than parsing xml in php4 without any more dependencies
   
   The file format is a seperated into sections seperated by colons
   the first section is the global server stats
   the second section and any sections after that are statistics for each mount
-->

<xsl:value-of select="client_connections" />,
<xsl:value-of select="connections" />,
<xsl:value-of select="source_connections" />,
<xsl:value-of select="sources" />

<xsl:for-each select="source">
:<xsl:value-of select="@mount" />,
<xsl:value-of select="artist" />,
<xsl:value-of select="channels" />,
<xsl:value-of select="listeners" />,
<xsl:value-of select="public" />,
<xsl:value-of select="quality" />,
<xsl:value-of select="samplerate" />,
<xsl:value-of select="title" />,
<xsl:value-of select="type" />,
<xsl:value-of select="description" />,
<xsl:value-of select="url" />
</xsl:for-each>
</xsl:template>
</xsl:stylesheet>

