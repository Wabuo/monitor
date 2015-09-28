<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr>
<th>Name</th>
<th>IP address</th>
<th>Port</th>
<th>TLS</th>
<th>Active</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="webservers/webserver">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="ip_address" /></td>
<td><xsl:value-of select="port" /></td>
<td><xsl:value-of select="tls" /></td>
<td><xsl:value-of select="active" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New webserver</a>
<a href="/cms" class="btn btn-default">Back</a>
</div>
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

<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{webserver/name}" class="form-control" />
<label for="ip_address">IP address:</label>
<input type="text" id="ip_address" name="ip_address" value="{webserver/ip_address}" class="form-control" />
<label for="port">Port:</label>
<input type="text" id="port" name="port" value="{webserver/port}" class="form-control" />
<div><label>TLS:</label>
<input type="checkbox" name="tls" id="tls" onChange="javascript:set_port_number(this)" ><xsl:if test="webserver/tls='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></div>
<div><label>Active:</label>
<input type="checkbox" name="active"><xsl:if test="webserver/active='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></div>
<label>Users:</label> <xsl:for-each select="users/user">
<div><input type="checkbox" name="users[]" value="{@id}"><xsl:if test="@checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input><xsl:value-of select="." /></div>
</xsl:for-each>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save webserver" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="webserver/@id">
<input type="submit" name="submit_button" value="Delete webserver" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
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
