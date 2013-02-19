<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  URIs template
//
//-->
<xsl:template match="uris">
<xsl:for-each select="uri">
<div class="uri"><xsl:value-of select="request_uri" /></div>
<div class="count"><xsl:value-of select="count" /></div>
</xsl:for-each>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Not Founds</h1>
<xsl:apply-templates select="uris" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
