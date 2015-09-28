<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="main.xslt" />

<!--
//
//  Login template
//
//-->
<xsl:template match="login">
<xsl:call-template name="show_messages" />
<form action="{url}" method="post" autocomplete="off" onSubmit="javascript:hash_password(); return true;">
<label for="username">Username:</label>
<input type="text" autocapitalize="off" autocorrect="off" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Password:</label>
<input type="password" id="password" name="password" class="form-control" />
<p>Bind session to IP (<span style="font-size:10px"><xsl:value-of select="remote_addr" /></span>): <input type="checkbox" name="bind_ip">
<xsl:if test="bind">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input></p>
<div class="btn-group">
<input type="submit" value="Login" class="btn btn-default" />
</div>

<input type="hidden" id="use_cr_method" name="use_cr_method" value="no" />
</form>
<input type="hidden" id="challenge" value="{challenge}" />

<p>If you have forgotten your password, click <a href="/password">here</a>.</p>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Login</h1>
<img src="/images/hiawatha_logo.png" class="logo" />
<xsl:apply-templates select="login" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
