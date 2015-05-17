<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../includes/banshee.xslt" />
<xsl:include href="../includes/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="list">
<tr>
<th class="name">Name</th>
<th class="ip_address">IP address</th>
<th class="port">Port</th>
<th class="tls">TLS</th>
<th class="active">Active</th>
</tr>
<xsl:for-each select="webservers/webserver">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="ip_address" /></td>
<td><xsl:value-of select="port" /></td>
<td><xsl:value-of select="tls" /></td>
<td><xsl:value-of select="active" /></td>
</tr>
</xsl:for-each>
</table>
<xsl:apply-templates select="pagination" />

<input type="button" value="New webserver" class="button" onClick="javascript:document.location='/admin/webserver/new'" />
<input type="button" value="Back" class="button" onClick="javascript:document.location='/admin'" />
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="webserver/@id">
<input type="hidden" name="id" value="{webserver/@id}" />
</xsl:if>

<table class="edit">
<tr><td>Name:</td><td><input type="text" name="name" value="{webserver/name}" class="text" /></td></tr>
<tr><td>IP address:</td><td><input type="text" name="ip_address" value="{webserver/ip_address}" class="text" /></td></tr>
<tr><td>Port:</td><td><input type="text" name="port" id="port" value="{webserver/port}" class="text" /></td></tr>
<tr><td>TLS:</td><td><input type="checkbox" name="tls" id="tls" onChange="javascript:set_port_number(this)" ><xsl:if test="webserver/tls='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></td></tr>
<tr><td>Active:</td><td><input type="checkbox" name="active"><xsl:if test="webserver/active='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></td></tr>
<tr><td>Users:</td><td> <xsl:for-each select="users/user">
<div><input type="checkbox" name="users[]" value="{@id}"><xsl:if test="@checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input><xsl:value-of select="." /></div>
</xsl:for-each>
</td></tr>
</table>

<input type="submit" name="submit_button" value="Save webserver" class="button" />
<input type="button" value="Cancel" class="button" onClick="javascript:document.location='/{/output/page}'" />
<xsl:if test="webserver/@id">
<input type="submit" name="submit_button" value="Delete webserver" class="button" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Webserver administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
