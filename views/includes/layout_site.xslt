<?xml version="1.0" ?>
<xsl:stylesheet	version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="layout_site">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="{language}" />
<meta name="author" content="Hugo Leisink" />
<meta name="copyright" content="Copyright (C) by Hugo Leisink. All rights reserved. Protected by the Copyright laws of the Netherlands and international treaties." />
<meta name="description" content="{description}" />
<meta name="keywords" content="{keywords}" />
<title><xsl:value-of select="title" /></title>
<xsl:for-each select="alternates/alternate">
<link rel="alternate" title="{.}"  type="{@type}" href="{@url}" />
</xsl:for-each>
<link rel="stylesheet" type="text/css" href="/css/includes/layout_site.css" />
<xsl:for-each select="styles/style">
<link rel="stylesheet" type="text/css" href="{.}" />
</xsl:for-each>
<xsl:if test="inline_css!=''">
<style type="text/css">
<xsl:value-of select="inline_css" />
</style>
</xsl:if>
<xsl:for-each select="javascripts/javascript">
<script type="text/javascript" src="{.}"></script><xsl:text>
</xsl:text></xsl:for-each>
</head>

<body>
<div class="wrapper">
	<div class="header">
		<div class="title">Hiawatha Monitor</div>
	</div>

	<div class="topbar">
	<xsl:if test="/output/menu">
	</xsl:if>
	</div>

	<xsl:if test="/output/menu">
	<div class="menu">
	<div class="menu-trigger"><a href="" onClick="javascript:return false">Menu</a></div>
	<div class="menu-content">
	<xsl:for-each select="/output/menu/item">
		<div class="section">
			<xsl:choose>
				<xsl:when test="link=''">
					<xsl:if test="menu/item">
						<div class="title"><xsl:value-of select="text" /></div>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<div class="link"><a href="{link}"><xsl:value-of select="text" /></a></div>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="menu/item">
				<div class="submenu">
				<xsl:for-each select="menu/item">
					<div class="link"><a href="{link}"><xsl:value-of select="text" /></a></div>
				</xsl:for-each>
				</div>
			</xsl:if>
		</div>
	</xsl:for-each>
	<br clear="both" />
	</div>
	</div>
	</xsl:if>

	<div class="page">
		<xsl:apply-templates select="/output/system_messages" />
		<xsl:apply-templates select="/output/content" />
	</div>

	<div class="footer">
		<p class="copyright">Hiawatha Monitor v<xsl:value-of select="/output/monitor_version" /> &#160;&#8226;&#160; Built upon the <a href="http://www.banshee-php.org/" target="_blank">Banshee PHP framework</a> v<xsl:value-of select="/output/banshee_version" /> &#160;&#8226;&#160; Design by <a href="http://www.freecsstemplates.org/" target="_blank">Free CSS Templates</a></p>
	</div>
</div>
<xsl:apply-templates select="/output/internal_errors" />
</body>

</html>
</xsl:template>

</xsl:stylesheet>
