<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  Statistics template
//
//-->
<xsl:template match="statistics">
<xsl:for-each select="connections">
<h2><xsl:value-of select="@webserver" /></h2>
<table class="list">
<tr>
	<th class="begin">Timestamp begin</th>
	<th class="end">Timestamp end</th>
	<th class="connections">Simultaneous connections</th>
</tr>
<xsl:for-each select="connection">
<tr>
	<td><xsl:value-of select="timestamp_begin" /></td>
	<td><xsl:value-of select="timestamp_end" /></td>
	<td><xsl:value-of select="simult_conns" /></td>
</tr>
</xsl:for-each>
</table>
</xsl:for-each>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Server statistics</h1>
<xsl:apply-templates select="statistics" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
