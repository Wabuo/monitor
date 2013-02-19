<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../includes/banshee.xslt" />

<!--
//
//  Hostnames template
//
//-->
<xsl:template match="hostnames">
<form action="/admin/hostname" method="post">
<table class="list">
<tr><th>Hostname</th><th>Visible in dropdown menu</th></tr>
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
</tr>
</xsl:for-each>
</table>

<input type="submit" name="submit_button" value="Update hostnames" class="button" />
<input type="button" value="Back" class="button" onClick="javascript:document.location='/admin'" />
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Hostnames</h1>
<xsl:apply-templates select="hostnames" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
