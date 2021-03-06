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
 * <<entity>> RSS feed.
 *
 * This class represents a RSS feed.
 *
 * Example of usage:
 * <code>
 *  // Creating and printing a RSS feed inside a plugin:
 *  public function rssFoobar($args)
 *  {
 *       // create rss feed
 *       $rss = new \Yana\RSS\Feed('this is my RSS feed');
 *       $rss->setTitle('foo');
 *       // create 1st item
 *       $item = new \Yana\RSS\Item('1st entry');
 *       $item->setLink('http://some.url')
 *          ->setDescription('some text');
 *       $rss->addItem($item);
 *       // create 2nd item
 *       $item = new \Yana\RSS\Item('2nd entry');
 *       $item->setLink('http://some.other.url')
 *          ->setDescription('some text');
 *       $rss->addItem($item);
 *       // print rss feed
 *       print (string) $rss;
 *       // when you're done, terminate the program
 *       exit(0);
 *  }
 * </code>
 *
 * @package     yana
 * @subpackage  rss
 */
class Feed extends \Yana\Core\StdObject implements \Yana\RSS\IsFeed
{

    /**
     * name of the channel, e.g. title of the website the channel refers to
     *
     * @var string
     */
    private $_title = "";
    /**
     * a link that points to the originating website of the feed
     *
     * @var string
     */
    private $_link = "";
    /**
     * a text to describe context and purpose of this channel
     *
     * @var string
     */
    private $_description = "";
    /**
     * language the channel is written in
     *
     * @var string
     */
    private $_language = "";
    /**
     * copyright notice of your choice
     *
     * @var string
     */
    private $_copyright = "";
    /**
     * mail of person responsible for editorial content
     *
     * @var string
     */
    private $_managingEditor = "";
    /**
     * mail of person responsible for technical issues
     *
     * @var string
     */
    private $_webMaster = "";
    /**
     * "time to live".
     *
     * A number of minutes that indicates how long a channel can be cached before refreshing from the source.
     *
     * @var int
     */
    private $_ttl = 0;
    /**
     * *.jpg, *.gif, or *.png image to display with the feed
     *
     * @var string
     */
    private $_image = "";
    /**
     * one or more categories that the channel belongs to
     *
     * @var array
     */
    private $_category = array();
    /**
     * a CSS stylesheet that formats the channel
     *
     * @var string
     */
    private $_css = "";
    /**
     * a XSL transformation that formats the channel
     *
     * @var string
     */
    private $_xslt = "";
    /**
     * list of one or more items of the channel
     *
     * @var array
     */
    private $_items = array();
    /**
     * pubDate of most recent item (auto-generated)
     *
     * @var string
     */
    private $_lastBuildDate = "";

    /**
     * Creates and initializes a new instance of this class.
     *
     * @param  string  $description  a text to describe context and purpose of this channel
     */
    public function __construct(string $description, string $title = "", string $locale = "")
    {
        $this->setDescription($description);
        $this->_title = $title;
        $this->_language = $locale;
    }

    /**
     * Returns the title of the channel.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    /**
     * Set the name/title of the channel.
     *
     * @param   string  $title  e.g. title of the website the channel refers to
     * @return  $this
     */
    public function setTitle(string $title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Returns a link that points to the originating website of the feed.
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->_link;
    }

    /**
     * Set URL for link to the channel's website.
     *
     * @param   string  $link  link that points to the originating website of the feed
     * @return  $this
     */
    public function setLink(string $link)
    {
        $this->_link = $link;
        return $this;
    }

    /**
     * Returns a text to describe context and purpose of this channel.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->_description;
    }

    /**
     * Set a channel description.
     *
     * @param   string  $description  a text to describe context and purpose of this channel
     * @return  $this
     */
    public function setDescription(string $description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Get locale/language the channel is written in.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->_language;
    }

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
    public function setLanguage(string $language)
    {
        assert((bool) preg_match("/^(?:|\w{2}(?:\-\w{2})?)$/s", $language), 'Invalid syntax: $language');

        $this->_language = $language;
        return $this;
    }

    /**
     * Returns copyright notice for this channel
     *
     * @return string
     */
    public function getCopyright(): string
    {
        return $this->_copyright;
    }

    /**
     * Set a copyright notice of your choice.
     *
     * Use this if you wish or need to set up copyright information on the channel.
     *
     * @param   string  $copyright  an URL or other reference to a license text
     * @return  $this
     */
    public function setCopyright(string $copyright)
    {
        $this->_copyright = $copyright;
        return $this;
    }

    /**
     * Get e-mail of person responsible for editorial content.
     *
     * @return string
     */
    public function getManagingEditor(): string
    {
        return $this->_managingEditor;
    }

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
    public function setManagingEditor(string $managingEditor)
    {
        $this->_managingEditor = (string) filter_var($managingEditor, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * Get e-mail of person responsible for technical issues.
     *
     * @return string
     */
    public function getWebMaster(): string
    {
        return $this->_webMaster;
    }

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
    public function setWebMaster(string $webMaster)
    {
        $this->_webMaster = (string) filter_var($webMaster, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * Get "time to live" in minutes.
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     * Defaults to 0.
     *
     * @return int
     */
    public function getTimeToLive(): int
    {
        return $this->_ttl;
    }

    /**
     * Set "time to live".
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     *
     * @param   int  $ttl  number of minutes, must be >= 0
     * @return  $this
     */
    public function setTimeToLive(int $ttl)
    {
        assert($ttl >= 0, 'Invalid argument $ttl: must be positive');

        $this->_ttl = $ttl;
        return $this;
    }

    /**
     * Get image URL.
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->_image;
    }

    /**
     * Set image URL.
     *
     * URL to *.jpg, *.gif, or *.png image to display with the feed, e.g. a website logo.
     *
     * @param   string  $image  must be a valid URL.
     * @return  $this
     */
    public function setImage(string $image)
    {
        $this->_image = $image;
        return $this;
    }

    /**
     * Returns a list of categories that the channel belongs to.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate channels of similar types.
     * E.g. "private", "news" aso.
     *
     * @return array
     */
    public function getCategory(): array
    {
        return $this->_category;
    }

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
    public function setCategory(array $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Returns an URL to a CSS stylesheet that formats the channel.
     *
     * @return string
     */
    public function getCss(): string
    {
        return $this->_css;
    }

    /**
     * Set URL to a CSS stylesheet that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param   string  $css  must be a valid URL
     * @return  $this
     */
    public function setCss(string $css)
    {
        $this->_css = $css;
        return $this;
    }

    /**
     * Returns an URL to a XSL transformation that formats the channel.
     *
     * @return string
     */
    public function getXslt(): string
    {
        return $this->_xslt;
    }


    /**
     * Set URL to a XSL transformation that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param   string  $xslt  must be a valid URL
     * @return  $this
     */
    public function setXslt(string $xslt)
    {
        $this->_xslt = $xslt;
        return $this;
    }

    /**
     * Add RSS feed item to this channel.
     *
     * Note: this function does not check for duplicate guid's.
     *
     * @param   \Yana\RSS\Item  $item  new RSS item
     * @return  $this
     */
    public function addItem(\Yana\RSS\Item $item)
    {
        assert(is_array($this->_items));
        $this->_items[] = $item;
        return $this;
    }

    /**
     * Get RSS feed items of this channel.
     *
     * @return \Yana\RSS\Item[]
     */
    public function getItems(): array
    {
        return $this->_items;
    }

    /**
     * This returns this object as a string in RSS 2.0 syntax.
     *
     * @return  string
     */
    public function __toString()
    {
        $xmlString = preg_replace('/<\?xml.*?\?>/', '', $this->toSimpleXml()->asXML());
        $string = '<?xml version="1.0"?>' . "\n";
        if ($this->getXslt()) {
            $string .= '<?xml-stylesheet type="text/xsl" href="' . $this->getXslt() . '" version="1.0"?>' . "\n";
        }
        if ($this->getCss()) {
            $string .= '<?xml-stylesheet type="text/css" href="' . $this->getCss() . '" media="screen"?>' . "\n";
        }
        return $string . $xmlString;
    }

    /**
     * Convert item to XML fragment.
     *
     * Returns a RSS channel-fragment based on RSS 2.0 standard.
     *
     * @return \SimpleXMLElement
     */
    public function toSimpleXml(): \SimpleXMLElement
    {
        $rss = new \SimpleXMLElement('<rss version="2.0"/>', LIBXML_NOXMLDECL);
        $channel = $rss->addChild('channel');
        $channel->addChild('docs', 'http://blogs.law.harvard.edu/tech/rss');
        // The following fields are mandatory, as stated at http://blogs.law.harvard.edu/tech/rss
        $channel->addChild('title', $this->getTitle());
        if ($this->getLink() > "") {
            $channel->addChild('link', $this->getLink());
        } else {
            $channel->addChild('link', \Yana\Http\Uris\CanonicalUrlBuilder::buildFromSuperGlobals()->__invoke());
        }
        $channel->addChild('description', $this->getDescription());
        // The following fields are optional
        $channel->addChild('language', $this->getLanguage());
        $channel->addChild('pubDate', date('r'));
        $channel->addChild('generator', "Yana Framework " . YANA_VERSION);

        assert(!isset($lastBuildDate), 'Cannot redeclare var $lastBuildDate');
        $lastBuildDate = 0;
        $items = $this->getItems();
        assert(!isset($item), 'Cannot redeclare var $item');
        foreach ($items as $item)
        {
            $tmp = strtotime($item->getPubDate());
            if (is_int($tmp) && $tmp > $lastBuildDate) {
                $lastBuildDate = $tmp;
            }
            unset($tmp);
        } // end foreach
        unset($item);
        if ($lastBuildDate > 0) {
            $channel->addChild('lastBuildDate', (string) $lastBuildDate);
            $this->_lastBuildDate = date('r', $lastBuildDate);
        }
        unset($lastBuildDate);

        if ($this->getCopyright()) {
            $channel->addChild('copyright', $this->getCopyright());
        }
        if ($this->getManagingEditor()) {
            $channel->addChild('managingEditor', $this->getManagingEditor());
        }
        if ($this->getWebMaster()) {
            $channel->addChild('webMaster', $this->getWebMaster());
        }
        if ($this->getTimeToLive()) {
            $channel->addChild('ttl', (string) $this->getTimeToLive());
        }

        assert(!isset($category), 'Cannot redeclare var $category');
        foreach ($this->getCategory() as $category)
        {
            $channel->addChild('category', $category);
        }
        unset($category);

        assert(!isset($imageXML), 'Cannot redeclare var $imageXML');
        if ($this->getImage()) {
            $imageXML = $channel->addChild('image');
            $imageXML->addChild('title', (string) $this->getTitle());
            $imageXML->addChild('link', (string) $this->getLink());
            $imageXML->addChild('url', (string) $this->getImage());
        }
        unset($imageXML);

        assert(!isset($item), 'Cannot redeclare var $item');
        /* @var $item Item */
        foreach ($items as $item)
        {
            $item->toSimpleXml($channel); // add item to channel
        }
        unset($item);

        return $rss;
    }

}

?>