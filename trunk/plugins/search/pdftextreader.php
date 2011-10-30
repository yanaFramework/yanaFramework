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

/**
 * Use this class to read the text-content of a PDF file.
 *
 * Note: this does not check for images. It extracts paragraphs and text-lines only.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class PdfTextReader extends BufferedReader
{

    /**
     * Reads and decodes the contents of the PDF file
     *
     * @access  public
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the file does not exist
     */
    public function read()
    {
        $file = "";
        do
        {
            parent::read();
            $file .= $this->getContent();
        } while (strpos($file, 'endstream') === false && $this->hasMoreContent());

        // extract all text streams
        preg_match_all('/>>\s*stream...(.*?)[\r\n\f]+endstream[\r\n\f]+/s', $file, $hits);

        /* @var $content string */
        $content = "";

        foreach ($hits[1] as $hit)
        {
            $hit = @gzinflate($hit);
            if ($hit) {
                // convert all escape-sequences
                $hit = preg_replace(
                    '/\\\\(\d{3})/e',
                    'mb_convert_encoding("&#" . octdec($1) . ";", "ISO-8859-1", "HTML-ENTITIES");',
                    $hit
                );
                // find all paragraphs
                preg_match_all('/(?:^|Tf|Td)\s*\[(.*?)\]\s*TJ\s*$/m', $hit, $paragraphs);
                foreach ($paragraphs[1] as $paragraph)
                {
                    // find all lines
                    preg_match_all('/\((.*?)\)/s', $paragraph, $lines);
                    foreach ($lines[1] as $line)
                    {
                        // remove special chars and numbers
                        $content .= preg_replace('/[^\w\s]|\d/i', '', $line);
                    }
                }
            }
        }
        // the result should be a list of words separated by spaces
        $this->content = explode("\n", $content);
    }

}

?>