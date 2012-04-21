<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" indent="yes"/>

  <xsl:template match="@*|node()">
    <xsl:copy>
      <xsl:apply-templates select="@*|node()"/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="//ressource/id|//ressource_tech_team/id">
    <xsl:variable name="filenameResources">http://www.icewars.de/portal/xml/de/ressourcen.xml</xsl:variable>
    <xsl:variable name="resourceId" select="."/>

    <id>
      <xsl:value-of select="."/>
    </id>
    <name>
      <xsl:value-of select="document($filenameResources)//ressource[id=$resourceId]/name"/>
    </name>
    </xsl:template>

</xsl:stylesheet>
