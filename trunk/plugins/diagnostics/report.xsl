<?xml version="1.0" ?>
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns="http://www.w3.org/1999/xhtml"
    exclude-result-prefixes="html">

    <xsl:output
        encoding="utf-8"
        method="xml"
        doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"
        doctype-public="-//W3C//DTD XHTML 1.1//EN"
        indent="yes"
    />
<xsl:param name="details"/>
<xsl:param name="urlChooseDetails"/>
<xsl:param name="urlChooseXml"/>

<xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="robots" content="noindex, nofollow"/>
            <title>Self-Test Logfile</title>
            <style type="text/css">
                div.error,
                div.success,
                div.notice,
                div.text
                {
                    margin-top: 10px;
                    margin-bottom: 10px;
                    display: block;
                }
                .instruction .description
                {
                    text-align: justify;
                }
                .error
                {
                    color: #ff0000;
                }
                .warning
                {
                    color: #aaaa00;
                }
                .success
                {
                    color: #00a000;
                }
                .notice
                {
                    color: #0000a0;
                }
                .text
                {
                    color: #000000;
                }
                .instruction
                {
                    border: 1px solid #888;
                    margin: 10px;
                    padding: 10px;
                    color: #000000;
                    background: #f8f8f8;
                }
                .description
                {
                    color: #808080;
                }
                .label
                {
                    font-weight: bold; margin-right: 10px;
                }
                .option
                {
                    margin-left: 30px;
                }
                body
                {
                    background: #ffffff;
                    color: #000000;
                    font-family: "Arial", sans-serif;
                }
                h1
                {
                    padding: 5px;
                    font-family: "Arial", sans-serif;
                    background: #dde5fa;
                    color: #000000;
                }
                h2
                {
                    padding: 5px;
                    font-family: "Arial", sans-serif;
                    background: #e0e0e0;
                    color: #000000;
                }
                h3
                {
                    padding: 5px;
                    font-size: 14px;
                    font-family: "Arial", sans-serif;
                    background: #f0f0f0;
                    color: #000000;
                }
                .report .report
                {
                    margin-left: 10px;
                }
                .report .report .report
                {
                    margin-left: 20px;
                }
                .report .report .report .report
                {
                    margin-left: 30px;
                }
                .report .report .report .report .report
                {
                    margin-left: 40px;
                }
            </style>
        </head>
        <body>
            <h1>Self-Test Logfile</h1>
            <div id="documentation">
                <div class="instruction">
                    <h2>Anleitung</h2>
                    <p class="description">Dieses Skript führt eine Reihe von Tests zur Selbstdiagnose durch, um festzustellen,
                       ob Ihr Programm korrekt installiert wurde. Sie können diese Log-Datei selbst auf Fehlermeldungen untersuchen - oder aber,
                       falls das Programm dennoch nicht funktioniert, können Sie mir auch eine E-Mail senden, um Hilfe bei der Lösung
                       Ihres Problems zu erhalten. Wenn Sie Fragen via Mail stellen, hängen Sie bitte dieses Diagnoseprotokoll an Ihre Mail.
                       Viele typische Fehler lassen sich bereits aus diesem Protokoll erkennen und Sie erhalten schneller eine Antwort.</p>
                    <ul class="description">
                       <li>Falls ein Test fehlschlägt, werden <span class="error">Fehlermeldungen</span> rot markiert.</li>
                       <li>Ist ein Test erfolgreich, werden <span class="success">Bestätigungsmeldungen</span> grün markiert.</li>
                       <li>Alle <span class="text">normalen Status-Informationen</span> werden schwarz dargestellt.</li>
                    </ul>
                    <p class="description">Sollten Sie rote Zeilen finden: prüfen Sie bitte Ihre Konfiguration auf die beschriebenen Fehler.<br />
                       Bitte beachten Sie: dieses Programm erkennt zwar einige der <i>häufigsten Fehler</i>,
                       aber es kann nicht alle möglichen Fehlerquellen entdecken.
                    </p>
                </div>
                <div class="instruction">
                    <h2>Instructions</h2>
                    <p class="description">This self-diagnosis script performs a number of tests to tell, if your program
                       has been installed correctly. You may check this log-file yourself for error-messages - or, if it still does not work,
                       email me to get help fixing your problem. By doing so, please include a copy of this diagnosis protocol with your mail.
                       Many typical problems may be recognized by reviewing this protocol and you will possibly get an answer for your problem
                       a little faster.
                    </p>
                    <ul class="description">
                       <li>If a test fails, <span class="error">failure-messages</span> are marked red.</li>
                       <li>If a test succeeds, <span class="success">success-messages</span> are marked green.</li>
                       <li>All <span class="text">normal status-information</span> is printed black.</li>
                    </ul>
                    <p class="description">If you find any red lines: check your configuration for these errors.<br />
                       Please note: this program detects some of the <i>most common errors</i>, but it does not find all possible problems.
                    </p>
                </div>
                <div class="instruction">
                    <ul>
                        <li>
                            <a>
                            <xsl:attribute name="href">
                                <xsl:value-of select="$urlChooseDetails"/>
                            </xsl:attribute>
                                <xsl:choose>
                                    <xsl:when test="$details = '1'">
                                        show report with less details
                                    </xsl:when>
                                    <xsl:otherwise>
                                        show report with more details
                                    </xsl:otherwise>
                                </xsl:choose>
                            </a>
                        </li>
                        <li>
                            <a>
                            <xsl:attribute name="href">
                                <xsl:value-of select="$urlChooseXml"/>
                            </xsl:attribute>
                                show report as XML
                            </a>
                        </li>
                    </ul>
                </div>
                <xsl:apply-templates/>
            </div>
        </body>
    </html>
</xsl:template>

<xsl:template match="report">
    <div class="report">
        <xsl:apply-templates/>
    </div>
</xsl:template>

<xsl:template match="report/report">
    <xsl:choose>
        <xsl:when test="$details = '1'">
            <div class="report">
                <xsl:apply-templates/>
            </div>
        </xsl:when>
        <xsl:otherwise>
            <xsl:choose>
                <xsl:when test=".//error | .//warning">
                    <div class="report">
                        <xsl:apply-templates select="title"/>
                        <div class="error"><span class="label">Found <xsl:value-of select="count(.//error | .//warning)"/> errors.</span></div>
                        <xsl:for-each select=".//report[error or warning]">
                            <div class="report">
                                 <xsl:apply-templates/>
                            </div>
                        </xsl:for-each>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="report">
                        <xsl:apply-templates select="title"/>
                        <div class="success"><span class="label">No problems found.</span></div>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="/report/title">
    <h1><xsl:value-of select="."/></h1>
</xsl:template>

<xsl:template match="/report/report/title">
    <h2><xsl:value-of select="."/></h2>
</xsl:template>

<xsl:template match="title">
    <h3><xsl:value-of select="."/></h3>
</xsl:template>

<xsl:template match="notice">
    <div class="notice">
        <span class="label">Notice:</span>
        <span class="description"><xsl:value-of select="."/></span>
    </div>
</xsl:template>

<xsl:template match="text">
    <xsl:if test="$details = '1'">
        <div class="text"><xsl:value-of select="."/></div>
    </xsl:if>
</xsl:template>

<xsl:template match="success">
    <div class="success">
        <span class="label">OK:</span>
        <span class="description"><xsl:value-of select="."/></span>
    </div>
</xsl:template>

<xsl:template match="warning">
    <div class="warning">
        <span class="label">Warning:</span>
        <span class="description"><xsl:value-of select="."/></span>
    </div>
</xsl:template>

<xsl:template match="error">
    <div class="error">
        <span class="label">Error:</span>
        <span class="description"><xsl:value-of select="."/></span>
    </div>
</xsl:template>

</xsl:stylesheet>
