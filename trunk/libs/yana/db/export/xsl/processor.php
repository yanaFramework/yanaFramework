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
declare(strict_types=1);

namespace Yana\Db\Export\Xsl;

/**
 * Helper object that assists in dealing with XSL transforms.
 *
 * This class encapsulates PHP's XSLTProcessor, which is the most basic library for the purpose.
 * While it lacks features, it offers the highest level of compatibility.
 *
 * @package     yana
 * @subpackage  db
 */
class Processor extends \Yana\Core\StdObject implements \Yana\Db\Export\Xsl\IsProcessor
{

    /**
     * <<constructor>> Initialize object.
     *
     * @throws  \Yana\Db\Export\Xsl\ProcessorException  When the class XSLTProcessor is not found
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!\class_exists('\XSLTProcessor')) {
            $message = "The PHP XSL extension was not found. Windows users: add 'extension=php_xsl.dll' to your php.ini file." .
                " On Linux please use 'apt-get install php5-xsl' on your console.";
            throw new \Yana\Db\Export\Xsl\ProcessorException($message, \Yana\Log\TypeEnumeration::ERROR);
        }
    }

    /**
     * Transform XML source to SQL statements via XSLT.
     *
     * This function uses the DOM-extension and XSLTProcessor to
     * transform a XDDL soure string to a list of SQL commands by using
     * a XSL template.
     *
     * Note: due to restrictions of this XSLT processor, you are limited
     * to XSL version 1.0. Using XSL 2.0 will cause an error to be thrown.
     *
     * The function returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * @param   \DOMDocument $xmlDocument  XML source to transform
     * @param   \DOMDocument $xslDocument  XSL template that will do the transformation
     * @return  array list of SQL commands
     */
    public function transformDocument(\DOMDocument $xmlDocument, \DOMDocument $xslDocument)
    {
        // XSLT processor
        $xsltProcessor = new \XSLTProcessor();
        $xsltProcessor->importStyleSheet($xslDocument); // attach the xsl rules

        // Transform to SQL
        $sql = $xsltProcessor->transformToXml($xmlDocument);
        $array = array();
        foreach (preg_split('/(?<=;)$/m', $sql) as $line)
        {
            $line = trim($line);
            if ($line !== "") {
                $array[] = $line;
            }
        }
        return $array;
    }

}

?>