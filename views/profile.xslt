<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post" onSubmit="javascript:hash_passwords(); return true;">
<label for="email">E-mail address:</label>
<input type="text" id="email" name="email" value="{email}" class="form-control" />
<label for="current">Current password:</label>
<input type="password" id="current" name="current" class="form-control" />
<label for="password">New password:</label> <span class="blank" style="font-size:10px">(will not be changed when left blank)</span>
<input type="password" id="password" name="password" class="form-control" onKeyUp="javascript:password_strength(this, 'username')" />
<label for="repeat">Repeat password:</label>
<input type="password" id="repeat" name="repeat" class="form-control" />

<label for="method">Notification method:</label>
<select id="metbod" name="notification_method" class="form-control">
<xsl:for-each select="notification/method">
<option value="{.}"><xsl:if test=".=../../notification_method"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="@label" /></option>
</xsl:for-each>
</select>
<label for="key">Notification key:</label>
<input type="text" id="key" name="notification_key" value="{notification_key}" class="form-control" />
<div><label for="report">Daily report:</label>
<input type="checkbox" id="report" name="daily_report"><xsl:if test="daily_report='yes'"><xsl:attribute name="checked">check</xsl:attribute></xsl:if></input></div>



<div class="btn-group">
<input type="submit" name="submit_button" value="Update profile" class="btn btn-default" />
<xsl:if test="cancel">
<a href="/{cancel/@page}" class="btn btn-default"><xsl:value-of select="cancel" /></a>
</xsl:if>
</div>

<input type="hidden" id="username" value="{username}" />
<input type="hidden" id="password_hashed" name="password_hashed" value="no" />
</form>

<h3>Recent account activity</h3>
<table class="table table-striped table-xs">
<thead>
<tr>
<th>IP address</th>
<th>Timestamp</th>
<th>Message</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="actionlog/log">
<tr>
<td><xsl:value-of select="ip" /></td>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="message" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>User profile</h1>
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
