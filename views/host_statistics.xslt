<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<!--
//
//  Graphs template
//
//-->
<xsl:template match="graphs">
<script type="text/javascript" src="/js/host_statistics.js" />

<div class="timespan"><xsl:value-of select="@begin" /> - <xsl:value-of select="@end" /></div>

<xsl:for-each select="graph">
<fieldset class="statistics">
<legend><xsl:value-of select="@label" /></legend>
<xsl:variable name="position" select="position()" />
<div class="info">
<span class="date" id="date_{$position}" />
<span class="count" id="count_{$position}" />
</div>
<div class="ybar"><xsl:value-of select="@max" /></div>
<div class="graph">
<xsl:for-each select="day">
<a href="/{/output/page}/{../@type}/{@timestamp}">
	<div class="bar" onMouseOver="javascript:show_date({$position}, '{@label}', '{@count}')" onMouseOut="javascript:clear_date({$position})">
		<div class="percentage" style="height:{.}px"></div>
	</div>
</a>
</xsl:for-each>
</div>
<br clear="both" />
</fieldset>
</xsl:for-each>
</xsl:template>

<!--
//
//  Day template
//
//-->
<xsl:template match="day">
<input type="button" value="Back" class="back button" onClick="javascript:document.location='/{/output/page}'" />
<table class="list">
<tr>
<th class="hostname">Hostname</th>
<th class="webserver">Webserver</th>
<th class="count"><xsl:value-of select="@label" /></th>
</tr>
<xsl:for-each select="stat">
<tr>
<td><xsl:value-of select="hostname" /></td>
<td><xsl:value-of select="webserver" /></td>
<td><xsl:value-of select="count" /></td>
</tr>
</xsl:for-each>
</table>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Host statistics</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="graphs" />
<xsl:apply-templates select="day" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
