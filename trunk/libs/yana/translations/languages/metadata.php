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

namespace Yana\Translations\Languages;

/**
 * This class holds information about a language file.
 *
 * @package     yana
 * @subpackage  core
 */
class MetaData extends \Yana\Core\Object implements \Yana\Core\IsPackageMetaData
{

    /**
     * @var string
     */
    private $_previewImage = "";

    /**
     * @var int
     */
    private $_lastModified = 0;

    /**
     * @var string
     */
    private $_title = "";

    /**
     * @var string
     */
    private $_text = "";

    /**
     * @var string
     */
    private $_author = "";

    /**
     * @var string
     */
    private $_url = "";

    /**
     * @param string $previewImage
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setPreviewImage($previewImage)
    {
        assert('is_string($previewImage); // Invalid argument $previewImage. String expected');
        $this->_previewImage = $previewImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreviewImage()
    {
        return $this->_previewImage;
    }

    /**
     * @param int $lastModified
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setLastModified($lastModified)
    {
        assert('is_int($lastModified); // Invalid argument $lastModified. Int expected');
        $this->_lastModified = $lastModified;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * @param string $title
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $title. String expected');
        $this->_title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $text
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setText($text)
    {
        assert('is_string($text); // Invalid argument $text. String expected');
        $this->_text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * @param string $author
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setAuthor($author)
    {
        assert('is_string($author); // Invalid argument $author. String expected');
        $this->_author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * @param string $url
     * @return \Yana\Translations\Languages\MetaData
     */
    public function setUrl($url)
    {
        assert('is_string($url); // Invalid argument $url. String expected');
        $this->_url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

}

?>