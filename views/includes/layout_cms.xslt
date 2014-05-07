<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="layout_cms">
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
<link rel="stylesheet" type="text/css" href="/css/includes/layout_cms.css" />
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
		<div class="title">Banshee Content Management System</div>
	</div>
	<div class="menu">
		<xsl:if test="/output/user">
		<ul class="admin">
			<li><a href="/">Website</a></li>
			<li><a href="/admin">CMS</a></li>
			<li><a href="/logout">Logout</a></li>
		</ul>
		</xsl:if>
	</div>
	<div class="page">
		<xsl:apply-templates select="/output/system_messages" />
		<xsl:apply-templates select="/output/content" />
	</div>
	<div class="footer">
		<span>Built upon the <a href="http://www.banshee-php.org/" target="_blank">Banshee PHP framework</a> v<xsl:value-of select="/output/banshee_version" /></span>
		<span>Logged in as <a href="/profile"><xsl:value-of select="/output/user" /></a></span>
		<span><a href="/session">Session manager</a></span>
		<span>Design by <a href="http://www.freecsstemplates.org/" target="_blank">Free CSS Templates</a></span>
	</div>
</div>
<xsl:apply-templates select="/output/internal_errors" />
</body>

</html>
</xsl:template>

</xsl:stylesheet>
