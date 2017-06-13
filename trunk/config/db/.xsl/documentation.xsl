<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
      <head>
          <title>Documentation</title>
          <style type="text/css">
              body {
                font-family: Arial, Helvetica, sans-serif;
              }
              table {
                width: 90%;
                border-collapse: collapse;
              }
              table tr * {
                text-align: left;
                width: 10%;
                border: 1px solid #ddd;
                padding: 2px;
              }
              table tr:nth-child(odd) {
                background: #f8f8f8 linear-gradient(to top, #f0f0f0, #fff);
              }
              table tr:nth-child(even) {
                background: #eee linear-gradient(to top, #e0e0e0, #efefef);
              }
              table tr:nth-child(1) {
                background: #ddd linear-gradient(to top, #d8d8d8, #ddd, #d8d8d8);
              }
              .table {
                background: #eee linear-gradient(to top right, #e0e0e0, #f8f8f8);
                border: 1px solid #aaa;
                margin: 0 20px;
                padding: 5px;
                box-shadow: 5px 5px 10px rgba(0,0,0,0.5);
                border-radius: 5px;
              }
              .tables {
                background: #f8f8f8 linear-gradient(to right, #f0f0f0, #fff);
                border: 1px solid #ddd;
                margin: 0 20px;
                padding: 0 5px;
                border-radius: 5px;
                box-shadow: 0 0 20px rgba(0,0,0,0.2);
                padding-bottom: 20px;
              }
              legend {
                color: #888;
                font-weight: bold;
              }
          </style>
      </head>
      <body>
         <h1>Database documentation</h1>
         <h2>Tables</h2>
         <div class="tables">
             <xsl:for-each select="//table">
                 <h3><xsl:value-of select="@name"/></h3>
                 <div class="table">
                      <xsl:if test="description">
                          <p><xsl:value-of select="description"/></p>
                      </xsl:if>
                      <table>
                          <legend>Columns</legend>
                          <tr>
                              <th>Name</th>
                              <th>Type</th>
                              <th>Primary key</th>
                              <th>Not null</th>
                              <th>Read only</th>
                              <th>Default</th>
                          </tr>
                          <xsl:for-each select="./declaration/*">
                              <tr>
                                  <xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute>
                                  <th><xsl:value-of select="@name"/></th>
                                  <td>
                                      <xsl:value-of select="name()"/>
                                      <xsl:if test="@length">
                                          (<xsl:value-of select="@length"/>)
                                      </xsl:if>
                                  </td>
                                  <td>
                                      <xsl:choose>
                                          <xsl:when test="../../primarykey/. = @name">yes</xsl:when>
                                          <xsl:otherwise>no</xsl:otherwise>
                                      </xsl:choose>
                                  </td>
                                  <td>
                                      <xsl:choose>
                                          <xsl:when test="@notnull = 'yes'">yes</xsl:when>
                                          <xsl:otherwise>no</xsl:otherwise>
                                      </xsl:choose>
                                  </td>
                                  <td>
                                      <xsl:choose>
                                          <xsl:when test="@readonly = 'yes'">yes</xsl:when>
                                          <xsl:otherwise>no</xsl:otherwise>
                                      </xsl:choose>
                                  </td>
                                  <td>
                                      <xsl:value-of select="default[not(@dbms) or @dbms = 'generic']"/>
                                  </td>
                              </tr>
                          </xsl:for-each>
                      </table>
                  </div>
              </xsl:for-each>
          </div>
      </body>
  </html>
</xsl:template>

</xsl:stylesheet>