<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
// Webserver template
//
//-->
<xsl:template match="webserver">
<fieldset class="webserver">
<legend class="{status}"><xsl:value-of select="name" /></legend>
<table>
<xsl:if test="version!=''">
<tr><td>Version:</td><td><xsl:value-of select="version" /></td></tr>
<tr><td>Up to date:</td><td><xsl:if test="uptodate='no'"><xsl:attribute name="class">warning</xsl:attribute></xsl:if><xsl:value-of select="uptodate" /></td></tr>
</xsl:if>
<tr><td>Active sync:</td><td><xsl:value-of select="active" /></td></tr>
<tr><td>Sync address:</td><td><xsl:value-of select="address" /></td></tr>
<tr><td>Latest sync fails:</td><td><xsl:if test="errors>0"><xsl:attribute name="class">warning</xsl:attribute></xsl:if><xsl:value-of select="errors" /></td></tr>
</table>
</fieldset>
</xsl:template>

<!--
//
//  List template
//
//-->
<xsl:template match="list">
<xsl:if test="item">
	<fieldset class="list">
	<legend><xsl:value-of select="@title" /></legend>
	<xsl:for-each select="item">
		<div class="item">
		<div class="label"><xsl:value-of select="." /></div>
		<div class="count"><xsl:value-of select="@count" /></div>
		</div>
	</xsl:for-each>
	</fieldset>
</xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Dashboard</h1>
<div class="left">
<h2>Monitored webservers</h2>
<xsl:apply-templates select="webserver" />
</div>

<div class="right">
<h2>Alerts for today</h2>
<xsl:apply-templates select="list" />
</div>

<div style="clear:both"></div>
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
