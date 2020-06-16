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
                <xsl:value-of select="concat('`', @name, '`')"/>
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
        <xsl:value-of select="concat('ALTER TABLE `', $tableName, '`')"/>
        <xsl:value-of select="concat(' ADD ', $indexType, ' `', $indexName, '` (', $indexDeclaration, ');&#10;')"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle foreign keys -->
<xsl:template name="foreign">
    <xsl:for-each select="//foreign">
        <xsl:variable name="tableName" select="concat($prefix, ../@name)"/>
        <xsl:variable name="foreignTableName" select="concat($prefix, @table)"/>
        <xsl:variable name="foreignKeyName">
            <xsl:text>CONSTRAINT `</xsl:text>
            <xsl:choose>
                <xsl:when test="@name">
                    <xsl:value-of select="@name"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="concat($tableName, '_', position(), '_fk')"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text>`</xsl:text>
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
                <xsl:value-of select="concat('`', @name, '`')"/>
                <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:variable name="targetDeclaration">
            <xsl:for-each select="key">
                <xsl:text>`</xsl:text>
                <xsl:choose>
                    <xsl:when test="@column != ''"><xsl:value-of select="@column"/></xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="../../../table[@name = $foreignTableName]/primarykey"/>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text>`</xsl:text>
                <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:value-of select="concat('ALTER TABLE `', $tableName, '`')"/>
        <xsl:value-of select="concat(' ADD ', $foreignKeyName, ' FOREIGN KEY (', $sourceDeclaration, ')')"/>
        <xsl:value-of select="concat(' REFERENCES `', $foreignTableName, '` (', $targetDeclaration, ')')"/>
        <xsl:value-of select="concat($onDelete, $onUpdate, ';&#10;')"/>
    </xsl:for-each>
</xsl:template>

<!-- Handle tables -->
<xsl:template name="table">
    <xsl:variable name="tableName" select="concat($prefix, @name)"/>
    <xsl:value-of select="concat('CREATE TABLE IF NOT EXISTS `', $tableName, '` (&#10;')"/>
    <xsl:for-each select="declaration/*">
        <!-- Start with column name -->
        <xsl:value-of select="concat('&#09;`', @name, '`')"/>
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
    <xsl:value-of select="concat('&#09;PRIMARY KEY (`', primarykey/., '`)')"/><!-- Create primary-key -->
    <xsl:if test="constraint[@dbms = 'mysql']"> CHECK (<xsl:value-of select="constraint[@dbms = 'mysql']"/>)</xsl:if>
    
    <!-- Add UNIQUE constraints -->
    <xsl:if test="declaration/*[@unique = 'yes']">
        <xsl:value-of select="concat(',&#10;&#09;UNIQUE `', $tableName, '` (')"/>
        <xsl:for-each select="declaration/*[@unique = 'yes']">
            <xsl:value-of select="concat('`', @name, '`')"/>
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
    <xsl:if test="constraint[@dbms = 'mysql']"> CHECK (<xsl:value-of select="constraint[@dbms = 'mysql']"/>)</xsl:if>

    <!-- Add default value -->
    <xsl:if test="$isReference = 0">
        <xsl:choose>
            <xsl:when test="@autoincrement = 'yes'">
                <xsl:text> AUTO_INCREMENT</xsl:text>
            </xsl:when>
            <xsl:when test="$default != 'NULL'">
                <xsl:value-of select="concat(' DEFAULT ', $default)"/>
            </xsl:when>
        </xsl:choose>
    </xsl:if>
</xsl:template>

<!-- Handle column type -->
<xsl:template name="type">
    <xsl:choose>
        <!--
            MySQL doesn't have a native boolean data type. So we simulate using TINYINT.
            Note that there is no difference between "TINYINT" without length, and "TINYINT(1)", MySQL ignores the length given.
            We add it anyway for our own documentation purposes as many people expect any TINYINT(1) they see to be a boolean.
        -->
        <xsl:when test="name() = 'bool'">TINYINT</xsl:when>
        <!-- Colors are stored as text #RRGGBB (without alpha-channel). -->
        <xsl:when test="name() = 'color'">CHAR</xsl:when>
        <xsl:when test="name() = 'date'">DATE</xsl:when>
        <xsl:when test="name() = 'float' and @precision">DECIMAL</xsl:when>
        <xsl:when test="name() = 'float'">DOUBLE</xsl:when>
        <xsl:when test="name() = 'range'">DOUBLE</xsl:when>
        <xsl:when test="name() = 'text'">TEXT</xsl:when>
        <xsl:when test="name() = 'time'">DATETIME</xsl:when>
        <xsl:when test="name() = 'timestamp'">BIGINT</xsl:when>
        <xsl:when test="name() = 'integer'">INT</xsl:when>
        <xsl:when test="name() = 'tel'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'mail'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'url'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'inet'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'password'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'image'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'file'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'string' and @fixed = 'yes' and @length">CHAR</xsl:when>
        <!--
          Types "list" and "array" contain JSON-encoded strings.
        -->
        <xsl:when test="name() = 'list'">VARCHAR</xsl:when>
        <xsl:when test="name() = 'array'">VARCHAR</xsl:when>
        <!--
          MySQL has a native SET type that is implemented as an integer (a byte-array to be precise).
          Thus the SET data type is limited to 64 elements max, due to the max size of the byte array.
          SET is not supported by most other RDBMS.
        -->
        <xsl:when test="name() = 'set'">
            <xsl:text>SET (</xsl:text>
            <!--
                The set tag may contain "option" and "optgroup" elements, where "optgroup" contains more "option" elements.
            -->
            <xsl:for-each select="option | optgroup/option">
                <xsl:text>'</xsl:text>
                <xsl:choose>
                    <xsl:when test="@value">
                        <xsl:value-of select="@value"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="."/>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text>'</xsl:text>
                <xsl:if test="position() != last()">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
            <xsl:text>)</xsl:text>
        </xsl:when>
        <xsl:when test="name() = 'enum'">
            <xsl:text>ENUM (</xsl:text>
            <!--
                The enum tag may contain "option" and "optgroup" elements, where "optgroup" contains more "option" elements.
            -->
            <xsl:for-each select="option | optgroup/option">
                <xsl:text>'</xsl:text>
                <xsl:choose>
                    <xsl:when test="@value">
                        <xsl:value-of select="@value"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="."/>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text>'</xsl:text>
                <xsl:if test="position() != last()">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
            <xsl:text>)</xsl:text>
        </xsl:when>
        <xsl:when test="not(@length)">TEXT</xsl:when>
        <xsl:otherwise>VARCHAR</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle column default value -->
<xsl:template name="default">
    <!-- get raw default value -->
    <xsl:variable name="default">
        <xsl:choose>
            <xsl:when test="default[@dbms = 'mysql']">
                <xsl:value-of select="default[@dbms = 'mysql']/."/>
            </xsl:when>
            <xsl:when test="default[not(@dbms) or @dbms = 'generic']">
                <xsl:value-of select="default[not(@dbms) or @dbms = 'generic']/."/>
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
                <xsl:when test="$default = 'true' or $default = 'TRUE'">1</xsl:when>
                <xsl:when test="$default = 'false' or $default = 'FALSE'">0</xsl:when>
                <xsl:otherwise><xsl:value-of select="number($default)"/></xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <!-- Type Inet is not supported in MySQL, must be simulated -->
        <xsl:when test="name() = 'inet' and $default = 'REMOTE_ADDR'">NULL</xsl:when>
        <!-- CURRENT_USER must be mapped for types String and Reference
        -->
        <xsl:when test="(name() = 'string') and $default = 'CURRENT_USER'"><xsl:text>NULL</xsl:text></xsl:when>
        <xsl:when test="(name() = 'reference') and $default = 'CURRENT_USER'">NULL</xsl:when>
        <!-- CURRENT_TIMESTAMP must be mapped for types Date and DateTime

             There is an issue with CURRENT_TIMESTAMP: MySQL allows at most ONE column
             with this default value. Otherwise it reports an error.
             Thus this feature must be simulated.
        -->
        <xsl:when test="(name() = 'date') and $default = 'CURRENT_TIMESTAMP'">CURRENT_DATE()</xsl:when>
        <xsl:when test="(name() = 'time') and $default = 'CURRENT_TIMESTAMP'">CURRENT_TIMESTAMP()</xsl:when>
        <xsl:when test="(name() = 'timestamp') and $default = 'CURRENT_TIMESTAMP'">NULL</xsl:when>
        <!-- Number types -->
        <xsl:when test="name() = 'range' or name() = 'integer' or name() = 'float'">
            <xsl:choose>
                <xsl:when test="string(number($default)) = 'NaN'">NULL</xsl:when>
                <xsl:otherwise><xsl:value-of select="number($default)"/></xsl:otherwise>
            </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>'</xsl:text>
            <xsl:call-template name="replace">
                <xsl:with-param name="string" select="$default"/>
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
        <xsl:when test="name() = 'enum'">0</xsl:when>
        <xsl:when test="name() = 'set'">0</xsl:when>
        <!-- MySQL maps Boolean to TinyInt(1) -->
        <xsl:when test="name() = 'bool'">1</xsl:when>
        <!-- Hex-color values are mapped to Char(7), example: #123456 -->
        <xsl:when test="name() = 'color'">7</xsl:when>
        <!--
          A typical IPv6 address has a maximum length of 39 characters: 0000:0000:0000:0000:0000:0000:0000:0000.
          However with an IPv4-mapped IPv6 address, it is possible to include the IPv4 base-10 address, resulting in:
          0000:0000:0000:0000:0000:ffff:192.168.100.100 = 45 characters. This is the maximum length of IP addresses.
        -->
        <xsl:when test="name() = 'inet'">45</xsl:when>
        <xsl:when test="name() = 'date' or name() = 'time' or name() = 'timestamp'">0</xsl:when>
        <!--
          Following international standard E.164, all int. phone numbers are limited to a max of 15 digits plus leading zeros.
          However, people may have a telephone system that adds more digits (we assume not more than 5).
          We will go with 30 digits by default, which should be more than enough for all that we know.
        -->
        <xsl:when test="name() = 'tel'">30</xsl:when>
        <!--
          According to RFC 5321, e-mail addresses may not exceed a maximum length of 254 octets.
          (Addresses containing multibyte characters will have to be shorter.)
        -->
        <xsl:when test="name() = 'mail'">254</xsl:when>
        <!--
          The maximum length of URLs supported by the majority of browsers is 2048 characters.
          We will use that as the default.
        -->
        <xsl:when test="name() = 'url'">2048</xsl:when>
        <!--
          Passwords will be stored as hashes. We do not want to exceed 255 characters on default.
        -->
        <xsl:when test="name() = 'password'">255</xsl:when>
        <!--
          Types "list" and "array" contain JSON-encoded strings.
          They are not limited in size per se, but we don't want them to end up being BLOBs either.
          The maximum size a varchar can be is 65535 characters, except: This is also the maximum row size.
          So this is only valid if this is the only varchar in the row.
          We thus go with a fairly reasonable 10k characters as a maximum.
          This is high enough that most arrays should fit, and still low enough that we can
          have a couple of them in a table without immediately hitting the row-size limit.
        -->
        <xsl:when test="name() = 'list' or name() = 'array'">10000</xsl:when>
        <!--
          Types "file" and "image" don't actually store the blobs in the database,
          they just store the filename. Ergo, we don't need more than 255 characters.
        -->
        <xsl:when test="name() = 'file' or name() = 'image'">128</xsl:when>
        <xsl:when test="name() = 'text'">0</xsl:when>
        <xsl:when test="name() = 'float' and @length">
            <xsl:value-of select="@length"/>
            <xsl:text>, </xsl:text>
            <xsl:value-of select="@precision"/>
        </xsl:when>
        <xsl:when test="@length"><xsl:value-of select="@length"/></xsl:when>
        <!-- 767 bytes is MySQL's maximum key length -->
        <xsl:otherwise>0</xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle functions -->
<xsl:template name="function">
    <xsl:variable name="functionName" select="concat($prefix, @name)"/>
    <xsl:for-each select="implementation[@dbms = 'mysql']">
        <xsl:choose>
            <!-- function -->
            <xsl:when test="return/.">
                <xsl:text>CREATE FUNCTION </xsl:text>
            </xsl:when>
            <!-- procedure -->
            <xsl:otherwise>
                <xsl:text>CREATE PROCEDURE </xsl:text>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:value-of select="concat('`', $functionName, '`')"/>
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
    <xsl:value-of select="concat('CREATE VIEW `', $prefix, @name, '`')"/>
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
        <xsl:when test="select[@dbms = 'mysql']">
            <xsl:value-of select="select[@dbms = 'mysql']/."/>
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
                            <xsl:value-of select="concat('`', $prefix, @table, '`.')"/>
                        </xsl:if>
                        <xsl:value-of select="concat('`', @column, '`')"/>
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
    <xsl:value-of select="';&#10;'"/><!-- End of view -->
</xsl:template>

<!-- Unhandled elements -->

</xsl:stylesheet>
