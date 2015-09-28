<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/tablemanager.xslt" />

<xsl:template match="tablemanager/label">
<table class="label">
<tr><td>Key:</td><td><xsl:value-of select="key" /></td></tr>
<tr><td>Type:</td><td><xsl:value-of select="type" /></td></tr>
</table>
</xsl:template>

<xsl:template match="content">
<xsl:apply-templates select="tablemanager" />
<xsl:apply-templates select="tablemanager/label" />
</xsl:template>

</xsl:stylesheet>
