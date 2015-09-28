<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />
<xsl:include href="banshee/filter.xslt" />
<xsl:include href="banshee/pagination.xslt" />

<!--
//
//  Events template
//
//-->
<xsl:template match="events">
<form action="/{/output/page}" method="post">
<div class="hidess">Hide start/stop: <input type="checkbox" name="hide_ss" onChange="javascript:submit()"><xsl:if test="@hide_ss='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></div>
<input type="hidden" name="submit_button" value="hidess" />
</form>
<table class="table table-striped table-condensed table-xs">
<thead>
<tr><th class="timestamp">Timestamp</th><th class="webserver">Webserver</th><th class="action">Action</th></tr>
</thead>
<tbody>
<xsl:for-each select="event">
<tr>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="event" /></td>
</tr>
</xsl:for-each>
</tbody>
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
