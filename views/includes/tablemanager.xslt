<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../includes/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="list">
<tr>
<xsl:for-each select="labels/label">
	<th class="{@name}"><xsl:value-of select="." /></th>
</xsl:for-each>
</tr>

<xsl:for-each select="items/item">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<xsl:for-each select="value">
	<td><xsl:value-of select="." /></td>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>
<xsl:apply-templates select="pagination" />

<input type="button" value="New {labels/@name}" class="button" onClick="javascript:document.location='/{/output/page}/new'" />
<xsl:if test="../back">
<input type="button" value="Back" class="button" onClick="javascript:document.location='/{../back}'" />
</xsl:if>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="form/@id">
<input type="hidden" name="id" value="{form/@id}" />
</xsl:if>

<xsl:if test="count(form/element[@type='datetime'])&gt;0">
<script type="text/javascript" src="/js/calendar.js" />
<script type="text/javascript" src="/js/calendar-en.js" />
<script type="text/javascript" src="/js/calendar-setup.js" />
</xsl:if>

<table class="tablemanager">
<xsl:for-each select="form/element">
<tr><td><xsl:value-of select="label" />:</td><td>
<xsl:choose>
	<!-- Boolean -->
	<xsl:when test="@type='boolean'">
		<input type="checkbox" name="{@name}"><xsl:if test="value=1"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input>
	</xsl:when>
	<!-- Date and time -->
	<xsl:when test="@type='datetime'">
		<input type="text" id="{@name}" name="{@name}" value="{value}" readonly="readonly" class="text datetime" />
	</xsl:when>
	<!-- Enumerate -->
	<xsl:when test="@type='enum'">
		<select name="{@name}" class="text">
		<xsl:for-each select="options/option">
		<option value="{@value}">
			<xsl:if test="@value=../../value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
			<xsl:value-of select="." />
		</option>
		</xsl:for-each>
		</select>
	</xsl:when>
	<!-- Text -->
	<xsl:when test="@type='text'">
		<textarea name="{@name}" class="text"><xsl:value-of select="value" /></textarea>
	</xsl:when>
	<!-- Other -->
	<xsl:otherwise>
		<input type="text" name="{@name}" value="{value}" class="text other" />
	</xsl:otherwise>
</xsl:choose>
</td></tr>
</xsl:for-each>
</table>

<xsl:for-each select="form/element[@type='datetime']">
<script type="text/javascript">
&lt;!--
	Calendar.setup({
		inputField: "<xsl:value-of select="@name" />",
		button    : "<xsl:value-of select="@name" />",
		ifFormat  : "%Y-%m-%d %H:%M:%S",
		showsTime : true,
		timeFormat: "24",
		firstDay  : 1
	});
//-->
</script>
</xsl:for-each>

<input type="submit" name="submit_button" value="Save {form/@name}" class="button" />
<input type="button" value="Cancel" class="button" onClick="javascript:document.location='/{/output/page}'" />
<xsl:if test="form/@id">
<input type="submit" name="submit_button" value="Delete {form/@name}" class="button" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</form>
</xsl:template>

<!--
//
//  Result template
//
//-->
<xsl:template match="result">
<p><xsl:value-of select="." /></p>
<xsl:choose>
	<xsl:when test="@url">
		<xsl:call-template name="redirect"><xsl:with-param name="url" select="@url" /></xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:call-template name="redirect" />
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<!--
//
//  Tablemanager template
//
//-->
<xsl:template match="tablemanager">
<h1><xsl:if test="icon"><img src="/images/icons/{icon}" class="title_icon" /></xsl:if><xsl:value-of select="name" /> administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
