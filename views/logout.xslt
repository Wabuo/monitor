<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="includes/banshee.xslt" />

<xsl:template match="content">
<h1>Logout</h1>
<p>You are now logged out.</p>
<xsl:call-template name="redirect">
<xsl:with-param name="url">admin/switch</xsl:with-param>
</xsl:call-template>
</xsl:template>

</xsl:stylesheet>
