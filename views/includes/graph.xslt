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

</xsl:stylesheet>
