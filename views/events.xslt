<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />
<xsl:include href="includes/filter.xslt" />
<xsl:include href="includes/pagination.xslt" />

<!--
//
//  Events template
//
//-->
<xsl:template match="events">
<table class="list">
<tr><th class="timestamp">Timestamp</th><th class="webserver">Webserver</th><th class="action">Action</th></tr>
<xsl:for-each select="event">
<tr>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="event" /></td>
</tr>
</xsl:for-each>
</table>
<xsl:apply-templates select="pagination" />
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Events</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="events" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
