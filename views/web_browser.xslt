<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  Clients template
//
//-->
<xsl:template match="info">
<div class="info">
<div class="max"><xsl:value-of select="../max" /> &#8595;</div>
<xsl:for-each select="item">
<div class="item">
	<div class="name"><xsl:value-of select="." /></div>
	<div class="bar" style="width:{@count}px"></div>
</div>
</xsl:for-each>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Web browsers</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="info" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
