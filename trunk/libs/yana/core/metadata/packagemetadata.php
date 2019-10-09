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

namespace Yana\Core\MetaData;

/**
 * This class holds information about a package.
 *
 * @package     yana
 * @subpackage  core
 */
class PackageMetaData extends \Yana\Core\StdObject implements \Yana\Core\MetaData\IsPackageMetaData
{

    /**
     * @var string
     */
    private $_previewImage = "";

    /**
     * @var int
     */
    private $_lastModified = null;

    /**
     * @var string
     */
    private $_title = "";

    /**
     * @var array
     */
    private $_texts = array();

    /**
     * @var string
     */
    private $_author = "";

    /**
     * @var string
     */
    private $_url = "";

    /**
     * @var string
     */
    private $_version = "";

    /**
     * Set preview image URI.
     *
     * Note that this does not check wether or not the URI is valid.
     *
     * @param   string  $previewImage  URI to image file (should be PNG, GIF or JPG)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setPreviewImage($previewImage)
    {
        assert(is_string($previewImage), 'Invalid argument $previewImage. String expected');
        $this->_previewImage = $previewImage;
        return $this;
    }

    /**
     * Get preview image URI.
     *
     * @return  string
     */
    public function getPreviewImage()
    {
        return $this->_previewImage;
    }

    /**
     * Set time when package was last modified.
     *
     * @param   int  $lastModified  UNIX timestamp
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setLastModified($lastModified)
    {
        assert(is_int($lastModified), 'Invalid argument $lastModified. Int expected');
        $this->_lastModified = (int) $lastModified;
        return $this;
    }

    /**
     * Get time when package was last modified.
     *
     * The result is returned as a UNIX timestamp.
     * If none has been set, it defaults to NULL.
     *
     * Note that the default can be mistaken for a valid timestamp, identifying January 1st 1970.
     * Make sure to test with the identity operator "===" not just "==".
     *
     * @return  int
     */
    public function getLastModified()
    {
        return $this->_lastModified;
    }

    /**
     * Set package title.
     *
     * @param   string   $title  some text (no HTML allowed)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setTitle($title)
    {
        assert(is_string($title), 'Invalid argument $title. String expected');
        $this->_title = $title;
        return $this;
    }

    /**
     * Get package title.
     *
     * If no title is given, it returns an empty string.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set package description.
     *
     * @param   array  $text  some text (no HTML allowed)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setTexts(array $text)
    {
        $this->_texts = $text;
        return $this;
    }

    /**
     * Get package description.
     *
     * If no description is given, it returns an empty string.
     *
     * @param   string  $language  target language
     * @param   string  $country   target country
     * @return string
     */
    public function getText($language = "", $country = "")
    {
        assert(is_string($language), 'Invalid argument $language: string expected');
        assert(is_string($country), 'Invalid argument $country: string expected');

        $text = "";
        if (!empty($country) && isset($this->_texts["{$language}-{$country}"])) {
            $text = $this->_texts["{$language}-{$country}"];
        } elseif (!empty($language) && isset($this->_texts[$language])) {
            $text = $this->_texts[$language];
        } elseif (isset($this->_texts[""])) {
            $text = $this->_texts[""];
        }
        return $text;
    }

    /**
     * Set name(s) of the autor(s).
     *
     * @param   string  $author  List of names (no HTML allowed)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setAuthor($author)
    {
        assert(is_string($author), 'Invalid argument $author. String expected');
        $this->_author = $author;
        return $this;
    }

    /**
     * Get name(s) of the autor(s).
     *
     * @return  string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Set URL to author's website.
     *
     * This should point the user to a website where more information and/or
     * updates are available for this package.
     *
     * @param   string  $url  valid URL (no HTML allowed)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setUrl($url)
    {
        assert(is_string($url), 'Invalid argument $url. String expected');
        $this->_url = $url;
        return $this;
    }

    /**
     * Get URL to author's website.
     *
     * This should point the user to a website where more information and/or
     * updates are available for this package.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set version of this package.
     *
     * See the manual on the function version_compare() if you want more information on what
     * version string should look like.
     *
     * @param   string  $version  some version string (no HTML allowed)
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function setVersion($version)
    {
        assert(is_string($version), 'Invalid argument $version: string expected');
        $this->_version = $version;
        return $this;
    }

    /**
     * Get version of this package.
     *
     * See the manual on the function version_compare() if you want more information on what
     * version string should look like.
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->_version;
    }

}

?>