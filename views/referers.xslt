<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  Referers template
//
//-->
<xsl:template match="referers">
<xsl:for-each select="hostname">
	<div class="block">
	<xsl:variable name="pos" select="position()" />
	<div class="host" onClick="javascript:$('.ref{$pos}').slideToggle('normal')">
	<xsl:value-of select="@name" />
	<div class="total">Total: <xsl:value-of select="@count" /></div>
	</div>
	<div class="referers ref{$pos}">
	<xsl:for-each select="referer">
		<div class="referer">
		<div class="count"><xsl:value-of select="@count" /></div>
		<div class="link"><a href="{.}" target="_blank"><xsl:value-of select="." /></a></div>
		</div>
	</xsl:for-each>
	</div>
	</div>
</xsl:for-each>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Referers</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="referers" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
