<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  Origins template
//
//-->
<xsl:template match="origins">
<div class="origins">
<div class="max"><xsl:value-of select="@max" /> &#8595;</div>
<xsl:for-each select="origin">
	<div class="origin">
	<div class="country"><xsl:value-of select="." /></div>
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
<h1>Origins</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="origins" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
