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
		<div class="menu"><ul>
		<xsl:for-each select="/output/menu/item">
			<li><a href="{link}"><xsl:value-of select="text" /></a></li>
		</xsl:for-each>
		</ul></div>
	</xsl:if>
	</div>

	<div class="page">
		<xsl:apply-templates select="/output/system_messages" />
		<xsl:apply-templates select="/output/content" />
	</div>

	<div class="footer">
		<span>Hiawatha Monitor v<xsl:value-of select="/output/monitor_version" /></span>
		<xsl:if test="/output/user">
			<span>Logged in as <a href="/profile"><xsl:value-of select="/output/user" /></a></span>
			<span><a href="/session">Session manager</a></span>
		</xsl:if>
		<span>Built upon the <a href="http://www.banshee-php.org/" target="_blank">Banshee PHP framework</a></span>
		<xsl:if test="/output/user/@admin='yes'">
			<span><a href="/admin">CMS</a></span>
		</xsl:if>
	</div>
</div>
<xsl:apply-templates select="/output/internal_errors" />
</body>

</html>
</xsl:template>

</xsl:stylesheet>
