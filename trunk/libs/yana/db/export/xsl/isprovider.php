<?php

/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Db\Export\Xsl;

/**
 * <<interface>> A XSL-DOMDocument provider.
 *
 * It creates and returns an instance of DOMDocument,
 * containing the XSL-Template for the requested DBMS.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsProvider
{
    /**
     * MySQL
     */
    const MYSQL = 1;

    /**
     * PostGreSQL
     */
    const POSTGRESQL = 2;

    /**
     * Microsoft SQL-Server
     */
    const MSSQL = 3;

    /**
     * Oracle Database
     */
    const ORACLEDB = 4;

    /**
     * IBM DB2
     */
    const DB2 = 5;

    /**
     * Creates a new \DOMDocument of an XSL-template.
     *
     * @param   int  $name  of dbms
     * @return  \DOMDocument
     * @throws  \Yana\Db\Export\Xsl\InvalidNameException
     */
    public function getXslDocument($name);

}

?>