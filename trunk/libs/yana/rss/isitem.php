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
 * <<interface>> RSS item.
 *
 * @package     yana
 * @subpackage  rss
 */
interface IsItem
{

    /**
     * Returns the general unique identifier.
     *
     * This value is always auto-generated based on the title and description.
     *
     * @return  string 
     */
    public function getGUID(): string;

    /**
     * Returns the title of the item.
     *
     * @return  string 
     */
    public function getTitle(): string;

    /**
     * Get URL of the item.
     *
     * @return  string
     */
    public function getLink(): string;

    /**
     * Set URL of the item.
     *
     * Any item must refer to an existing web-resource.
     *
     * @param   string  $link  must be a valid URL
     * @return  $thid
     */
    public function setLink(string $link);

    /**
     * Item synopsis, note that this may contain HTML.
     *
     * @return  string
     */
    public function getDescription(): string;

    /**
     * Set item synopsis.
     *
     * @param   string  $description  some text - may contain HTML
     * @return  $this
     */
    public function setDescription(string $description);

    /**
     * Get e-mail address of the author.
     *
     * @return  string
     */
    public function getAuthor(): string;

    /**
     * Set e-mail address of the author.
     *
     * @param   string  $author  must be valid e-mail address
     * @return  $this
     */
    public function setAuthor(string $author);

    /**
     * Get list of categories.
     *
     * @return  array
     */
    public function getCategory(): array;

    /**
     * Include item in one or more categories.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate items of similar types.
     * E.g. all "announcements", all "news-updates" aso.
     *
     * @param   array  $category  list of strings
     * @return  $this
     */
    public function setCategory(array $category);

    /**
     * Get URL to a page for comments.
     *
     * @return  string
     */
    public function getComments(): string;

    /**
     * Set URL to a page for comments.
     *
     * Optionally the reader may comment on the read content.
     * If you provide a comment page or forum to do so, enter the URL here.
     * The comment-URL and the content-URL may be identical.
     *
     * @param   string  $comments  must be a valid URL
     * @return  $this
     */
    public function setComments(string $comments);

    /**
     * Indicates when the item was published.
     *
     * @return  string
     */
    public function getPubDate(): string;

    /**
     * Set a publication date.
     *
     * This value indicates when the item was published.
     * It is auto-generated if not provided.
     *
     * Note: Use date('r'); to create a valid string.
     *
     * @param   string  $pubDate  formatted date using RFC 2822
     * @return  $this
     */
    public function setPubDate(string $pubDate);

    /**
     * Convert item to XML fragment.
     *
     * Returns an item-fragment based on RSS 2.0 standard.
     *
     * @param   \SimpleXMLElement  $channel  parent XML node, for valid RSS this should be a "channel" element
     * @return  \SimpleXMLElement
     */
    public function toSimpleXml(?\SimpleXMLElement $channel = null): \SimpleXMLElement;

}

?>
