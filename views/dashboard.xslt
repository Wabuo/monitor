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
<h2>Alerts for today<span>[<a href="javascript:return false" id="opener">?</a>]</span></h2>
<div id="dialog" title="Percentages">
<p>The shown percentages represent the surpluse value for the current day, compared to the median for the previous days.</p>
<p>So, when the median for the previous days is 200 and the value for the current day is 300, the percentage is 50%. Because (300-200)/200 * 100% = 50%.</p>
<p>The current time is taken into account when calculating the median for the previous days. Only count values starting at <xsl:value-of select="threshold_value" /> and percentages starting at <xsl:value-of select="threshold_change" />% are shown. The alerts are refreshed every <xsl:if test="page_refresh>1"><xsl:value-of select="page_refresh" /></xsl:if> minute<xsl:if test="page_refresh>1">s</xsl:if>.</p>
</div>
<div class="alerts" refresh="{page_refresh}"></div>
</div>
</div>

<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
