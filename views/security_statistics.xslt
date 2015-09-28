<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />
<xsl:include href="banshee/filter.xslt" />
<xsl:include href="banshee/graphs.xslt" />

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Security statistics</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="graph" />
<xsl:apply-templates select="day" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
