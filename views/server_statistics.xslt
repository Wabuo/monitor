<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />
<xsl:include href="includes/filter.xslt" />
<xsl:include href="includes/graph.xslt" />

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Server statistics</h1>
<xsl:apply-templates select="filter" />
<xsl:apply-templates select="graphs" />
<xsl:apply-templates select="day" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
