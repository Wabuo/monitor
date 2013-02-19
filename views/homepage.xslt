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
<legend><xsl:value-of select="name" /></legend>
<table>
<tr><td>IP address:</td><td><xsl:value-of select="ip_address" /></td></tr>
<tr><td>Port:</td><td><xsl:value-of select="port" /></td></tr>
<tr><td>SSL:</td><td><xsl:value-of select="ssl" /></td></tr>
<tr><td>Active:</td><td><xsl:value-of select="active" /></td></tr>
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
<h1>Homepage</h1>
<div class="left">
<h2>Monitored webservers</h2>
<xsl:apply-templates select="webserver" />
</div>
<div class="right">
<h2>Alerts for today</h2>
<xsl:apply-templates select="list" />
</div>
<br clear="both" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
