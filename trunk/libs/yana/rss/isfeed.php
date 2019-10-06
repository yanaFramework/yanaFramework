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

namespace Yana\RSS;

/**
 * <<interface>> RSS feed.
 *
 * @package     yana
 * @subpackage  rss
 */
interface IsFeed
{

    /**
     * Returns the title of the channel.
     *
     * @return  string
     */
    public function getTitle(): string;

    /**
     * Set the name/title of the channel.
     *
     * @param   string  $title  e.g. title of the website the channel refers to
     * @return  $this
     */
    public function setTitle(string $title);

    /**
     * Returns a link that points to the originating website of the feed.
     *
     * @return  string
     */
    public function getLink(): string;

    /**
     * Set URL for link to the channel's website.
     *
     * @param   string  $link  link that points to the originating website of the feed
     * @return  $this
     */
    public function setLink(string $link);

    /**
     * Returns a text to describe context and purpose of this channel.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Set a channel description.
     *
     * @param   string  $description  a text to describe context and purpose of this channel
     * @return  $this
     */
    public function setDescription(string $description);

    /**
     * Get locale/language the channel is written in.
     *
     * @return string
     */
    public function getLanguage(): string;

    /**
     * Set locale/language the channel is written in.
     *
     * Each channel should use 1 consistent language.
     * (If you need 2 languages, you should use 2 channels.)
     * The language or "locale" consists of a 2-letter language abbreviation in small letters,
     * optionally followed by a dash and a 2-letter country code in capital letters.
     * See documentation on the internet if you need more details.
     *
     * This settings is auto-detected from the currently used locale. Only change it if needed.
     *
     * @param   string  $language  valid language/locale string, e.g. en, en-US
     * @return  $this
     * @see     \Yana\Translations\Facade
     */
    public function setLanguage(string $language);

    /**
     * Returns copyright notice for this channel
     *
     * @return  string
     */
    public function getCopyright(): string;

    /**
     * Set a copyright notice of your choice.
     *
     * Use this if you wish or need to set up copyright information on the channel.
     *
     * @param   string  $copyright  an URL or other reference to a license text
     * @return  $this
     */
    public function setCopyright(string $copyright);

    /**
     * Get e-mail of person responsible for editorial content.
     *
     * @return  string
     */
    public function getManagingEditor(): string;

    /**
     * Set e-mail of person responsible for editorial content.
     *
     * In some legislations you may be required to provide a managing editor.
     * This is the person a reader might turn to if he/she thinks some of the channel's content
     * is incorrect or doubtful and thus needs to be changed or deleted.
     *
     * The managing editor is responsible for content, but not for questions regarding technical issues.
     * That will be the webmaster instead.
     *
     * The webmaster and the managing editor may be the same person in practice, in wich case you should name it twice.
     *
     * @param   string  $managingEditor  valid e-mail address
     * @return  $this
     */
    public function setManagingEditor(string $managingEditor);

    /**
     * Get e-mail of person responsible for technical issues.
     *
     * @return  string
     */
    public function getWebMaster(): string;

    /**
     * Set e-mail of person responsible for technical issues.
     *
     * In some organizations may require you to name a person responsible for
     * questions regarding technical issues with the channel.
     *
     * The webmaster is responsible technical concerns, but not for the content of the channel.
     * That will be the managing editor instead.
     *
     * The webmaster and the managing editor may be the same person in practice, in wich case you should name it twice.
     *
     * @param   string  $webMaster  valid e-mail address
     * @return  $this
     */
    public function setWebMaster(string $webMaster);

    /**
     * Get "time to live" in minutes.
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     * Defaults to 0.
     *
     * @return  int
     */
    public function getTimeToLive(): int;

    /**
     * Set "time to live".
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     *
     * @param   int  $ttl  number of minutes, must be >= 0
     * @return  $this
     */
    public function setTimeToLive(int $ttl);

    /**
     * Get image URL.
     *
     * @return  string
     */
    public function getImage(): string;

    /**
     * Set image URL.
     *
     * URL to *.jpg, *.gif, or *.png image to display with the feed, e.g. a website logo.
     *
     * @param   string  $image  must be a valid URL.
     * @return  $this
     */
    public function setImage(string $image);

    /**
     * Returns a list of categories that the channel belongs to.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate channels of similar types.
     * E.g. "private", "news" aso.
     *
     * @return  array
     */
    public function getCategory(): array;

    /**
     * Include channel in one or more categories.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate channels of similar types.
     * E.g. "private", "news" aso.
     *
     * @param   array  $category  list of strings
     * @return  $this
     */
    public function setCategory(array $category);

    /**
     * Returns an URL to a CSS stylesheet that formats the channel.
     *
     * @return  string
     */
    public function getCss(): string;

    /**
     * Set URL to a CSS stylesheet that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param   string  $css  must be a valid URL
     * @return  $this
     */
    public function setCss(string $css);

    /**
     * Returns an URL to a XSL transformation that formats the channel.
     *
     * @return string
     */
    public function getXslt(): string;

    /**
     * Set URL to a XSL transformation that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param   string  $xslt  must be a valid URL
     * @return  $this
     */
    public function setXslt(string $xslt);

    /**
     * Add RSS feed item to this channel.
     *
     * Note: this function does not check for duplicate guid's.
     *
     * @param   \Yana\RSS\Item  $item  new RSSitem
     * @return  $this
     */
    public function addItem(\Yana\RSS\Item $item);

    /**
     * Get RSS feed items of this channel.
     *
     * @return  \Yana\RSS\Item[]
     */
    public function getItems(): array;

    /**
     * Convert item to XML fragment.
     *
     * Returns a RSS channel-fragment based on RSS 2.0 standard.
     *
     * @return  \SimpleXMLElement
     */
    public function toSimpleXml(): \SimpleXMLElement;

}

?>