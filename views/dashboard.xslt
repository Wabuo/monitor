<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
// Webserver template
//
//-->
<xsl:template match="webserver">
<div class="panel panel-default webserver">
<div class="panel-heading {status}"><xsl:value-of select="name" /></div>
<div class="panel-body"><table>
<xsl:if test="version!=''">
<tr><td>Version:</td><td><xsl:value-of select="version" /></td></tr>
<tr><td>Up to date:</td><td><xsl:if test="uptodate='no'"><xsl:attribute name="class">warning</xsl:attribute></xsl:if><xsl:value-of select="uptodate" /></td></tr>
</xsl:if>
<tr><td>Active sync:</td><td><xsl:value-of select="active" /></td></tr>
<tr><td>Sync address:</td><td><xsl:value-of select="address" /></td></tr>
<tr><td>Latest sync fails:</td><td><xsl:if test="errors>0"><xsl:attribute name="class">warning</xsl:attribute></xsl:if><xsl:value-of select="errors" /></td></tr>
</table></div>
</div>
</xsl:template>

<!--
//
//  List template
//
//-->
<xsl:template match="list">
<xsl:if test="item">
	<div class="panel panel-default top-alert">
	<div class="panel-heading"><xsl:value-of select="@title" /></div>
	<div class="panel-body"><table>
	<xsl:for-each select="item">
		<tr>
		<td><xsl:value-of select="." /></td>
		<td><xsl:value-of select="@count" /></td>
		</tr>
	</xsl:for-each>
	</table></div>
	</div>
</xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Dashboard</h1>
<div class="row">
<div class="col-sm-6">
<h2>Monitored webservers</h2>
<xsl:apply-templates select="webserver" />
</div>

<div class="col-sm-6">
<h2>Alerts for today</h2>
<xsl:apply-templates select="list" />
</div>
</div>

<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
