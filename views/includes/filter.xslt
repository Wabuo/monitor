<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
//
//  Filter template
//
//-->
<xsl:template match="filter">
<div class="filter">
<form action="/{/output/page}" method="post">
<xsl:if test="hostnames">
<span>Host: <select class="text" name="hostname" onChange="javascript:submit()">
<xsl:for-each select="hostnames/hostname">
	<option value="{@id}">
		<xsl:if test="@selected='yes'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
		<xsl:value-of select="." />
	</option>
</xsl:for-each>
</select></span>
</xsl:if>

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

</xsl:stylesheet>
