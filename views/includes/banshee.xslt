<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="functions.xslt" />
<xsl:include href="layout_site.xslt" />
<xsl:include href="layout_cms.xslt" />

<xsl:output method="html" encoding="utf-8" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd" />

<!--
//
//  Filter template
//
//-->
<xsl:template match="filter">
<div class="filter">
<form action="/{/output/page}" method="post">
<xsl:if test="browser_version">
Browser version: <input type="checkbox" name="browser_version" onChange="javascript:submit()">
	<xsl:if test="browser_version='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
</input>
</xsl:if>

<span>Host: <select class="text" name="hostname" onChange="javascript:submit()">
<xsl:for-each select="hostnames/hostname">
	<option value="{@id}">
		<xsl:if test="@selected='yes'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
		<xsl:value-of select="." />
	</option>
</xsl:for-each>
</select></span>

<span>Webserver: <select class="text" name="webserver" onChange="javascript:submit()">
<xsl:for-each select="webservers/webserver">
	<option value="{@id}">
		<xsl:if test="@selected='yes'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
		<xsl:value-of select="." />
	</option>
</xsl:for-each>
</select></span>
<input type="hidden" name="submit_button" value="filter" />
</form>
</div>
</xsl:template>

<!--
//
//  Output template
//
//-->
<xsl:template match="/output">
<xsl:apply-templates select="layout_site" />
<xsl:apply-templates select="layout_cms" />
</xsl:template>

</xsl:stylesheet>
