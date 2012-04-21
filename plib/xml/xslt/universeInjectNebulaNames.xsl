<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!--
	This xslt will search a universe xml for occurences of nebulas and replace the colors
	that are provided in the xml with the textual representation of the nebula.

	TODO: This xslt only works for german colors at the moment. So we should check what
	happens to universe xml files from the english version
	-->

	<xsl:output method="xml" indent="yes"/>
	<xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>
	<xsl:template match="nebel">
		<xsl:if test="text()='blau'">
		  <nebel>blau</nebel>
			<injectedNebulaName>blauer Nebel</injectedNebulaName>
		</xsl:if>
		<xsl:if test="text()='gelb'">
			<nebel>gelb</nebel>
      <injectedNebulaName>gelber Nebel</injectedNebulaName>
		</xsl:if>
		<xsl:if test="text()='gruen'">
			<nebel>gruen</nebel>
      <injectedNebulaName>grüner Nebel</injectedNebulaName>
		</xsl:if>
		<xsl:if test="text()='rot'">
			<nebel>rot</nebel>
      <injectedNebulaName>roter Nebel</injectedNebulaName>
		</xsl:if>
		<xsl:if test="text()='violett'">
			<nebel>violett</nebel>
      <injectedNebulaName>violetter Nebel</injectedNebulaName>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
