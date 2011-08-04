<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="text" media-type="text/sql"/>
    <xsl:strip-space elements="*" />
    <xsl:param name="prefix" />
    <!--
        Entities you may need:
        \n: &#10;
        \t: &#09;
         ': &#39;
         ": &#34;
         &: &#38;
         <: &#60;
         >: &#62;
     -->

<!-- XSLT 1.0 compatible replace() -->
<xsl:template name="replace">
    <xsl:param name="string"/>
    <xsl:param name="from"/>
    <xsl:param name="to"/>
    <xsl:choose>
        <xsl:when test="contains($string, $from)">
            <xsl:value-of select="substring-before($string, $from)"/>
            <xsl:copy-of select="$to"/>
            <xsl:call-template name="replace">
                <xsl:with-param name="string" select="substring-after($string, $from)"/>
                <xsl:with-param name="from" select="$from"/>
                <xsl:with-param name="to" select="$to" />
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$string" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Create SQL -->
<xsl:template match="/">
    <xsl:for-each select="//sequence">
        <xsl:call-template name="sequence"/>
    </xsl:for-each>
    <xsl:for-each select="//table">
        <xsl:call-template name="table"/>
    </xsl:for-each>
    <xsl:call-template name="index"/>
    <xsl:call-template name="foreign"/>
    <xsl:for-each select="//function">
        <xsl:call-template name="function"/>
    </xsl:for-each>
    <xsl:for-each select="//view">
        <xsl:call-template name="view"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle indexes -->
<xsl:template name="index">
    <xsl:for-each select="//index">
        <xsl:variable name="tableName" select="concat($prefix, ../@name)"/>
        <xsl:variable name="indexName">
            <xsl:choose>
                <xsl:when test="@name">
                    <xsl:value-of select="@name"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="concat($tableName, '_', position(), '_idx')"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="indexType">
            <xsl:if test="@unique = 'yes'"><xsl:text>UNIQUE </xsl:text></xsl:if>
            <xsl:text>INDEX</xsl:text>
        </xsl:variable>
        <xsl:variable name="indexDeclaration">
            <xsl:for-each select="column">
                <xsl:value-of select="concat('&#34;', @name, '&#34;')"/>
                <xsl:if test="@length">
                    <xsl:value-of select="concat('(', @length, ')')"/>
                </xsl:if>
                <xsl:choose>
                    <xsl:when test="@sorting = 'ascending'"><xsl:text> ASC</xsl:text></xsl:when>
                    <xsl:when test="@sorting = 'descending'"><xsl:text> DESC</xsl:text></xsl:when>
                </xsl:choose>
                <xsl:if test="position() != last()">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:value-of select="concat('ALTER TABLE &#34;', $tableName, '&#34;')"/>
        <xsl:value-of select="concat(' ADD ', $indexType, ' &#34;', $indexName, '&#34; (', $indexDeclaration, ');&#10;')"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle foreign keys -->
<xsl:template name="foreign">
    <xsl:for-each select="//foreign">
        <xsl:variable name="tableName" select="concat($prefix, ../@name)"/>
        <xsl:variable name="foreignTableName" select="concat($prefix, @table)"/>
        <xsl:variable name="foreignKeyName">
            <xsl:text>CONSTRAINT &#34;</xsl:text>
            <xsl:choose>
                <xsl:when test="@name">
                    <xsl:value-of select="@name"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="concat($tableName, '_', position(), '_fk')"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text>&#34;</xsl:text>
        </xsl:variable>
        <xsl:variable name="onUpdate">
            <xsl:choose>
                <xsl:when test="@onupdate = 'restrict'"><xsl:text> ON UPDATE RESTRICT</xsl:text></xsl:when>
                <xsl:when test="@onupdate = 'cascade'"><xsl:text> ON UPDATE CASCADE</xsl:text></xsl:when>
                <xsl:when test="@onupdate = 'set-null'"><xsl:text> ON UPDATE SET NULL</xsl:text></xsl:when>
                <!-- set default is not supported in MySQL -->
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="onDelete">
            <xsl:choose>
                <xsl:when test="@ondelete = 'restrict'"><xsl:text> ON DELETE RESTRICT</xsl:text></xsl:when>
                <xsl:when test="@ondelete = 'cascade'"><xsl:text> ON DELETE CASCADE</xsl:text></xsl:when>
                <xsl:when test="@ondelete = 'set-null'"><xsl:text> ON DELETE SET NULL</xsl:text></xsl:when>
                <!-- set default is not supported in MySQL -->
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="sourceDeclaration">
            <xsl:for-each select="key">
                <xsl:value-of select="concat('&#34;', @name, '&#34;')"/>
                <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:variable name="targetDeclaration">
            <xsl:for-each select="key">
                <xsl:text>&#34;</xsl:text>
                <xsl:choose>
                    <xsl:when test="@column != ''"><xsl:value-of select="@column"/></xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="../../../table[@name = $foreignTableName]/primarykey"/>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text>&#34;</xsl:text>
                <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:value-of select="concat('ALTER TABLE &#34;', $tableName, '&#34;')"/>
        <xsl:value-of select="concat(' ADD ', $foreignKeyName, ' FOREIGN KEY (', $sourceDeclaration, ')')"/>
        <xsl:value-of select="concat(' REFERENCES &#34;', $foreignTableName, '&#34; (', $targetDeclaration, ')')"/>
        <xsl:value-of select="concat($onDelete, $onUpdate, ';&#10;')"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle tables -->
<xsl:template name="table">
    <xsl:variable name="tableName" select="concat($prefix, @name)"/>
    <xsl:value-of select="concat('CREATE TABLE &#34;', $tableName, '&#34; (&#10;')"/>
    <xsl:for-each select="declaration/*">
        <!-- Start with column name -->
        <xsl:value-of select="concat('&#09;&#34;', @name, '&#34;')"/>
        <xsl:choose>
            <!-- Columns of type Reference contain a foreign key constraint
                 and must be resolved to type of target column. -->
            <xsl:when test="name() = 'reference'">
                <xsl:variable name="columnName" select="@name"/>
                <!-- Get table name either from attribute or by scanning
                     foreign key constraints. -->
                <xsl:variable name="table">
                    <xsl:choose>
                        <xsl:when test="@table">
                            <xsl:value-of select="@table"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="../../foreign[key/@name = $columnName]/@table"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <!-- Get column name either from attribute or by scanning
                     foreign key constraints. -->
                <xsl:variable name="column">
                    <xsl:choose>
                        <xsl:when test="@column">
                            <xsl:value-of select="@column"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="../../foreign/key[@name = $columnName]/@column"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <!-- Get default value of base column -->
                <xsl:variable name="default">
                    <xsl:call-template name="default"/>
                </xsl:variable>
                <!-- Get type of target column -->
                <xsl:for-each select="../../../table[@name = $table]/declaration/*[@name = $column]">
                    <xsl:call-template name="column">
                        <xsl:with-param name="tableName" select="$tableName"/>
                        <xsl:with-param name="isReference" select="1"/>
                        <xsl:with-param name="default" select="$default"/>
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:when>

            <!-- Other column type -->

            <xsl:otherwise>
                <xsl:variable name="default">
                    <xsl:call-template name="default"/>
                </xsl:variable>
                <xsl:call-template name="column">
                    <xsl:with-param name="isReference" select="0"/>
                    <xsl:with-param name="default" select="$default"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>

        <!-- Add comment -->
        <xsl:if test="@title">
            <xsl:text> COMMENT '</xsl:text>
            <xsl:call-template name="replace">
                <xsl:with-param name="string" select="@title"/>
                <xsl:with-param name="from">'</xsl:with-param>
                <xsl:with-param name="to">\'</xsl:with-param>
            </xsl:call-template>
            <xsl:text>'</xsl:text>
        </xsl:if>

        <xsl:value-of select="',&#10;'"/><!-- End of column -->
    </xsl:for-each>

    <!-- Add PRIMARY KEY constraint -->
    <xsl:value-of select="concat('&#09;PRIMARY KEY (&#34;', primarykey/., '&#34;)')"/><!-- Create primary-key -->

    <!-- Add UNIQUE constraints -->
    <xsl:if test="declaration/*[@unique = 'yes']">
        <xsl:value-of select="concat(', &#10;&#09;UNIQUE &#34;', $tableName, '&#34; (')"/>
        <xsl:for-each select="declaration/*[@unique = 'yes']">
            <xsl:value-of select="concat('&#34;', @name, '&#34;')"/>
            <xsl:if test="position() != last()">
                <xsl:text>, </xsl:text>
            </xsl:if>
        </xsl:for-each>
        <xsl:value-of select="')'"/>
    </xsl:if>

    <xsl:value-of select="'&#10;)'"/><!-- End of columns -->

    <xsl:value-of select="';&#10;'"/><!-- End of table -->

</xsl:template>

<!-- Handle columns -->
<xsl:template name="column">
    <xsl:param name="tableName"/>
    <xsl:param name="isReference"/>
    <xsl:param name="default"/>

    <xsl:variable name="type">
        <xsl:call-template name="type"/>
    </xsl:variable>

    <xsl:variable name="length">
        <xsl:call-template name="length">
            <xsl:with-param name="type" select="$type" />
        </xsl:call-template>
    </xsl:variable>

    <xsl:value-of select="concat(' ', $type)"/>
    <xsl:if test="$length != '0'">
        <xsl:value-of select="concat('(', $length, ')')"/>
    </xsl:if>

    <xsl:if test="@unsigned = 'yes'"> UNSIGNED</xsl:if>
    <xsl:if test="($type = 'INT' or $type = 'DOUBLE') and @fixed = 'yes'"> ZEROFILL</xsl:if>

    <!-- Add NOT NULL constraint -->
    <xsl:if test="$isReference = 0">
        <xsl:if test="@notnull = 'yes'"> NOT NULL</xsl:if>
    </xsl:if>

    <!-- Add default value -->
    <xsl:if test="$isReference = 0 and $default != 'NULL'">
        <xsl:value-of select="concat(' DEFAULT ', $default)"/>
    </xsl:if>
</xsl:template>

<!-- Handle column type -->
<xsl:template name="type">
    <xsl:choose>
        <xsl:when test="@autoincrement = 'yes' and (not(@length) or @length &gt; 8)">bigserial</xsl:when>
        <xsl:when test="@autoincrement = 'yes'">serial</xsl:when>
        <xsl:when test="name() = 'bool'">boolean</xsl:when>
        <xsl:when test="name() = 'color'">char</xsl:when>
        <xsl:when test="name() = 'date'">date</xsl:when>
        <!-- @todo look up datatype LIST/ARRAY -->
        <xsl:when test="name() = 'float' and @precision">numeric</xsl:when>
        <xsl:when test="name() = 'float'' and (not(@length) or @length &gt; 4)">double precision</xsl:when>
        <xsl:when test="name() = 'float'">real</xsl:when>
        <xsl:when test="name() = 'range'">double precision</xsl:when>
        <xsl:when test="name() = 'text'">text</xsl:when>
        <xsl:when test="name() = 'time'">datetime</xsl:when>
        <xsl:when test="name() = 'timestamp'">integer</xsl:when>
        <xsl:when test="name() = 'integer' and @length and @length &lt; 3">smallint</xsl:when>
        <xsl:when test="name() = 'integer' and @length and @length &gt; 8">bigint</xsl:when>
        <xsl:when test="name() = 'integer'">integer</xsl:when>
        <!-- @todo look up datatype INET -->
        <xsl:when test="name() = 'inet'">inet</xsl:when>
        <xsl:when test="name() = 'string' and @fixed = 'yes'">char</xsl:when>
        <xsl:when test="not(@length)">text</xsl:when>
        <xsl:otherwise>varchar</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle column default value -->
<xsl:template name="default">
    <!-- get raw default value -->
    <xsl:variable name="default">
        <xsl:choose>
            <xsl:when test="default[@dbms = 'postgresql']">
                <xsl:value-of select="default[@dbms = 'postgresql']/."/>
            </xsl:when>
            <xsl:when test="default[not(@dbms)]">
                <xsl:value-of select="default[not(@dbms)]/."/>
            </xsl:when>
            <xsl:otherwise>NULL</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- handle exceptions -->
    <xsl:choose>
        <xsl:when test="$default = 'NULL'">NULL</xsl:when>
        <!-- Type Bool is not supported in MySQL, must be simulated -->
        <xsl:when test="name() = 'bool'">
            <xsl:choose>
                <xsl:when test="$default = 'true' or $default = 'TRUE'">true</xsl:when>
                <xsl:when test="$default = 'false' or $default = 'FALSE'">false</xsl:when>
                <xsl:otherwise><xsl:value-of select="number($default)"/></xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <!-- Type default value REMOTE_ADDR is not supported and must be simulated -->
        <xsl:when test="name() = 'inet' and $default = 'REMOTE_ADDR'">NULL</xsl:when>
        <!-- CURRENT_TIMESTAMP must be mapped for types Date and DateTime

             There is an issue with CURRENT_TIMESTAMP: MySQL allows at most ONE column
             with this default value. Otherwise it reports an error.
             Thus this feature must be simulated.
        -->
        <xsl:when test="(name() = 'date') and $default = 'CURRENT_TIMESTAMP'">NULL</xsl:when>
        <xsl:when test="(name() = 'time') and $default = 'CURRENT_TIMESTAMP'">NULL</xsl:when>
        <xsl:when test="(name() = 'timestamp') and $default = 'CURRENT_TIMESTAMP'">NULL</xsl:when>
        <!-- Number types -->
        <xsl:when test="name() = 'integer' or name() = 'float'">
            <xsl:choose>
                <xsl:when test="string(number($default)) = 'NaN'">NULL</xsl:when>
                <xsl:otherwise><xsl:value-of select="number($default)"/></xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>'</xsl:text>
            <xsl:call-template name="replace">
                <xsl:with-param name="string" select="@title"/>
                <xsl:with-param name="from">'</xsl:with-param>
                <xsl:with-param name="to">\'</xsl:with-param>
            </xsl:call-template>
            <xsl:text>'</xsl:text>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle column length -->
<xsl:template name="length">
    <xsl:param name="type"/>
    <xsl:choose>
        <!-- Hex-color values are mapped to Char(7), example: #123456 -->
        <xsl:when test="name() = 'color'">7</xsl:when>
        <xsl:when test="name() = 'date' or name() = 'time' or name() = 'timestamp'">0</xsl:when>
        <xsl:when test="name() = 'file' or name() = 'image'">128</xsl:when>
        <xsl:when test="name() = 'text'">0</xsl:when>
        <xsl:when test="name() = 'float' and @length">
            <xsl:value-of select="@length"/>
            <xsl:text>, </xsl:text>
            <xsl:value-of select="@precision"/>
        </xsl:when>
        <xsl:when test="@length"><xsl:value-of select="@length"/></xsl:when>
        <xsl:otherwise>0</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle functions -->
<xsl:template name="function">
    <xsl:variable name="functionName" select="concat($prefix, @name)"/>
    <xsl:for-each select="implementation[@dbms = 'postgresql']">
        <xsl:text>CREATE OR REPLACE FUNCTION </xsl:text>
        <xsl:value-of select="concat('&#34;', $functionName, '&#34;')"/>
        <xsl:if test="param">
            <xsl:text> (</xsl:text>
            <xsl:for-each select="param">
                <xsl:if test="@mode">
                    <xsl:value-of select="concat(@mode, ' ')"/>
                </xsl:if>
                <xsl:value-of select="concat(@name, ' ', @type)"/>
                <xsl:if test="position() != last()">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
            <xsl:text>)</xsl:text>
        </xsl:if>
        <xsl:if test="return">
            <xsl:value-of select="concat(' RETURNS ', return/.)"/>
        </xsl:if>
        <xsl:value-of select="concat('&#10;', code/.)"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle views -->
<xsl:template name="view">
    <xsl:value-of select="concat('CREATE OR REPLACE VIEW &#34;', $prefix, @name, '&#34;')"/>
    <xsl:if test="field">
        <xsl:text> (</xsl:text>
        <xsl:for-each select="field">
            <xsl:value-of select="@column"/>
            <xsl:if test="position() != last()">
                <xsl:text>, </xsl:text>
            </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
    </xsl:if>
    <xsl:text> AS </xsl:text>
    <xsl:choose>
        <xsl:when test="select[@dbms = 'postgresql']">
            <xsl:value-of select="select[@dbms = 'postgresql']/."/>
        </xsl:when>
        <xsl:when test="select[not(@dbms)]">
            <xsl:value-of select="select[not(@dbms)]/."/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>SELECT </xsl:text>
            <xsl:choose>
                <xsl:when test="field">
                    <xsl:for-each select="field">
                        <xsl:if test="@table">
                            <xsl:value-of select="concat('&#34;', $prefix, @table, '&#34;.')"/>
                        </xsl:if>
                        <xsl:value-of select="concat('&#34;', @column, '&#34;')"/>
                        <xsl:value-of select="concat(' ', @alias)"/>
                        <xsl:if test="position() != last()">
                            <xsl:text>, </xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>* </xsl:text>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:value-of select="concat('FROM ', @tables, ' ')"/>
            <xsl:if test="@where">
                <xsl:value-of select="concat('WHERE ', @where, ' ')"/>
            </xsl:if>
            <xsl:if test="@orderby">
                <xsl:value-of select="concat('ORDER BY ', @orderby, ' ')"/>
                <xsl:if test="@sorting = 'descending'">
                    <xsl:text> DESC</xsl:text>
                </xsl:if>
            </xsl:if>
       </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
        <xsl:when test="@checkoption = 'local'">
            <xsl:text> WITH LOCAL CHECK OPTION</xsl:text>
        </xsl:when>
        <xsl:when test="@checkoption = 'cascaded'">
            <xsl:text> WITH CASCADED CHECK OPTION</xsl:text>
        </xsl:when>
    </xsl:choose>
</xsl:template>

<xsl:template name="sequence">
    <xsl:variable name="sequenceName" select="concat($prefix, @name)"/>
    <xsl:text>CREATE SEQUENCE </xsl:text>
    <xsl:value-of select="concat('&#34;', $sequenceName ,'&#34;')"/>
    <xsl:if test="@increment">
        <xsl:value-of select="concat(' INCREMENT ', @increment)"/>
    </xsl:if>
    <xsl:if test="@min">
        <xsl:value-of select="concat(' MINVALUE ', @min)"/>
    </xsl:if>
    <xsl:if test="@max">
        <xsl:value-of select="concat(' MAXVALUE ', @max)"/>
    </xsl:if>
    <xsl:if test="@start">
        <xsl:value-of select="concat(' START ', @start)"/>
    </xsl:if>
    <xsl:if test="@cycle = 'yes'">
        <xsl:text> CYCLE</xsl:text>
    </xsl:if>
    <xsl:text>;</xsl:text>
</xsl:template>

<!-- Unhandled elements -->

</xsl:stylesheet>