<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Hostnames template
//
//-->
<xsl:template match="hostnames">
<form action="/{/output/page}" method="post">
<table class="table table-striped table-hover table-condensed table-xs">
<thead>
<tr><th>Hostname</th><th>Visible</th><th>Delete</th></tr>
</thead>
<tbody>
<xsl:for-each select="hostname">
<tr>
	<td><xsl:value-of select="." /></td>
	<td>
		<input type="checkbox" name="hostname[]" value="{@id}">
			<xsl:if test="@visible='yes'">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:if>
		</input>
	</td>
	<td><input type="checkbox" name="delete[]" value="{@id}" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<input type="submit" name="submit_button" value="Update hostnames" class="btn btn-default" />
<a href="/cms" class="btn btn-default">Back</a>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Hostname administration</h1>
<xsl:apply-templates select="hostnames" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
