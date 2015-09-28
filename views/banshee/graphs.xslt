<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="graph.xslt" />

<!--
//
//  Day template
//
//-->
<xsl:template match="day">
<div class="date"><xsl:value-of select="@day" /></div>
<table class="table table-striped table-condensed table-xs">
<thead>
<tr>
<xsl:if test="@hostnames='yes'">
<th class="hostname">Hostname</th>
</xsl:if>
<th class="webserver">Webserver</th>
<th class="count"><xsl:value-of select="@label" /></th>
</tr>
</thead>
<tbody>
<xsl:for-each select="stat">
<tr>
<xsl:if test="../@hostnames='yes'">
<td><xsl:value-of select="hostname" /></td>
</xsl:if>
<td><xsl:value-of select="webserver" /></td>
<td><xsl:value-of select="count" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<a href="/{/output/page}" class="btn btn-default">Back</a>
</xsl:template>

</xsl:stylesheet>
