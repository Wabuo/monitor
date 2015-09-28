<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  PHP extensions template
//
//-->
<xsl:template match="php_extensions">
<p>The following required PHP extensions are missing:</p>
<ul>
<xsl:for-each select="extension"><li><xsl:value-of select="." /></li></xsl:for-each>
</ul>
<p>Install and enable them and refresh this page.</p>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Refresh</a>
</div>
</xsl:template>

<!--
//
//  Database settings template
//
//-->
<xsl:template match="db_settings">
<p>Enter your database settings in settings/website.conf and refresh this page.</p>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Refresh</a>
</div>
</xsl:template>

<!--
//
//  Create database template
//
//-->
<xsl:template match="create_db">
<xsl:call-template name="show_messages" />

<p>Enter the MySQL root credentials to create a database and a user for your website.</p>
<form action="/{/output/page}" method="post">
<label for="username">Username:</label>
<input type="text" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Password:</label>
<input type="password" id="password" name="password" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Create database" class="btn btn-default" />
</div>
</form>
</xsl:template>

<!--
//
//  Import SQL template
//
//-->
<xsl:template match="import_sql">
<xsl:call-template name="show_messages" />

<p>The next step is to import the file database/mysql.sql into your database.</p>
<form action="/{/output/page}" method="post">
<input type="submit" name="submit_button" value="Import SQL" class="btn btn-default" />
</form>
</xsl:template>

<!--
//
//  Update database template
//
//-->
<xsl:template match="update_db">
<p>Your database is outdated. Update your database to continue.</p>
<form action="/{/output/page}" method="post">
<input type="submit" name="submit_button" value="Update database" class="btn btn-default" />
</form>
</xsl:template>

<!--
//
//  Done template
//
//-->
<xsl:template match="done">
<p>Done! You can now login with username 'admin' and password 'monitor'.</p>
<p>Don't forget to disable this setup module by removing it from settings/public_modules.conf.</p>

<div class="btn-group">
<a href="/" class="btn btn-default">Continue</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Monitor setup</h1>
<xsl:apply-templates select="php_extensions" />
<xsl:apply-templates select="db_settings" />
<xsl:apply-templates select="create_db" />
<xsl:apply-templates select="import_sql" />
<xsl:apply-templates select="update_db" />
<xsl:apply-templates select="done" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
