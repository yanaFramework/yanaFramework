<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
      <head>
          <title>Documentation</title>
          <style type="text/css">
              body { font-family: Arial, Helvetica, sans-serif; }
              table { background: -moz-linear-gradient(top left, #eee, #f0f0f0); background-color: #eee; width: 90%; border-collapse: collapse; }
              table tr * { text-align: left; width: 10%; border: 1px solid #ddd; padding: 2px; }
              table tr th { background: -moz-linear-gradient(top left, #eee, #fff); background-color: #f8f8f8; }
              .table { background: #f0f0f0; border: 1px solid #aaa; margin: 0 20px; padding: 5px;
                  -moz-box-shadow: 5px 5px 10px rgba(0,0,0,0.5); -webkit-box-shadow: 5px 5px 10px rgba(0,0,0,0.5);
                  box-shadow: 5px 5px 10px rgba(0,0,0,0.5);
                  -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; }
              .tables { background: -moz-linear-gradient(left, #f0f0f0, #fff); background-color: #f8f8f8;
                  border: 1px solid #ddd; margin: 0 20px; padding: 0 5px;
                  -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
                  -moz-box-shadow: 0 0 20px rgba(0,0,0,0.2); -webkit-box-shadow: 0 0 20px rgba(0,0,0,0.2);
                  box-shadow: 0 0 20px rgba(0,0,0,0.2); padding-bottom: 20px; }
              legend { color: #aaa; font-weight: bold; }
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