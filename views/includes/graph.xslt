<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
//
//  Graphs template
//
//-->
<xsl:template match="graphs">
<script type="text/javascript" src="/js/graph.js" />

<div class="timespan"><xsl:value-of select="@date_begin" /> - <xsl:value-of select="@date_end" /></div>

<xsl:for-each select="graph">
<fieldset class="statistics">
<legend><xsl:value-of select="@label" /></legend>
<xsl:variable name="position" select="position()" />
<div class="info">
<span class="count" id="count_{$position}" />
<span class="date" id="date_{$position}" />
</div>
<div class="ybar" style="height:{../@graph_height + 22}px"><xsl:value-of select="@max" /></div>
<div class="graph" style="height:{../@graph_height}px">
<xsl:for-each select="day">
<a href="/{/output/page}/{../@type}/{@timestamp}">
	<div class="bar" style="height:{../../@graph_height}px ; width:{../../@bar_width}px" onMouseOver="javascript:show_date({$position}, '{@label}', '{@count}')" onMouseOut="javascript:clear_date({$position})">
		<div class="percentage weekend_{@weekend}" style="height:{.}px ; width:{../../@bar_width - 2}px"></div>
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
<table class="list">
<tr>
<xsl:if test="@hostnames='yes'">
<th class="hostname">Hostname</th>
</xsl:if>
<th class="webserver">Webserver</th>
<th class="count"><xsl:value-of select="@label" /></th>
</tr>
<xsl:for-each select="stat">
<tr>
<xsl:if test="../@hostnames='yes'">
<td><xsl:value-of select="hostname" /></td>
</xsl:if>
<td><xsl:value-of select="webserver" /></td>
<td><xsl:value-of select="count" /></td>
</tr>
</xsl:for-each>
</table>
<input type="button" value="Back" class="button" onClick="javascript:document.location='/{/output/page}'" />
</xsl:template>

</xsl:stylesheet>
