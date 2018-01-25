<?php
/**
 * Look up updates for the framework installation.
 *
 * @menu       group: setup, title: {lang id="configmenu"}
 * @author     Thomas Meyer
 * @type       config
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Config;

/**
 * <<smarty function>> update checker function.
 *
 * @package    yana
 * @subpackage plugins
 */
class UpdateChecker extends \Yana\Core\Object implements \Yana\Views\Helpers\IsFunction,
    \Yana\Data\Adapters\IsCacheable
{

    /**
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cacheAdapter = null;

    /**
     * @var  \Yana\Translations\Facade
     */
    private $_language = null;

    /**
     * @var  resource
     */
    private $_socket = null;

    /**
     * @var  string
     */
    private $_updateServer = "";

    /**
     * <<constructor>> Create a new instance.
     *
     * This also loads the configuration.
     *
     * @param  \Yana\Translations\Facade  $language      facade
     * @param  string                     $updateServer  URL
     */
    public function __construct(\Yana\Translations\Facade $language, $updateServer)
    {
        $cache = new \Yana\Data\Adapters\ArrayAdapter();
        $this->setCache($cache);
        $this->_language = $language;
        $this->_updateServer = (string) $updateServer;
    }

    /**
     * Set a caching method for this class.
     *
     * @param  \Yana\Data\Adapters\IsDataAdapter  $cache  implements cache storage
     * @return \UpdateChecker
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cacheAdapter = $cache;
        return $this;
    }

    /**
     * Returns the currently selected cache adapter.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getCache()
    {
        return $this->_cacheAdapter;
    }

    /**
     * Returns the currently selected language adapter.
     *
     * @return  \Yana\Translations\Facade
     */
    protected function _getLanguage()
    {
        return $this->_language;
    }

    /**
     * Returns URL to update server.
     *
     * @return  string
     */
    protected function _getUpdateServer()
    {
        return $this->_updateServer;
    }

    /**
     * <<smarty function>> updateCheck
     *
     * This checks for updates and returns the result.
     * If the server is not reachable it returns a link instead.
     *
     * Note: since version look-ups can be very time consuming
     * (e.g. if the server is slow or temporarily unreachable)
     * this function caches the results for 8 hours before
     * searching for updates again.
     *
     * @param  array                      $params  ignored
     * @param  \Smarty_Internal_Template  $smarty  ignored
     * @return  string
     */
    /**
     * 
     * @return string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $language = $this->_getLanguage();
        $cache = $this->_getCache();
        $cacheId = $language->getLocale();

        /* cache results */
        if (isset($cache[$cacheId])) {
            return $cache[$cacheId];
        }

        /* create link to check for new version */
        $url = $this->_getUpdateServer();
        $url = str_replace(YANA_LEFT_DELIMITER . '$VERSION' . YANA_RIGHT_DELIMITER, YANA_VERSION, $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$IS_STABLE' . YANA_RIGHT_DELIMITER, YANA_IS_STABLE, $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$LANG' . YANA_RIGHT_DELIMITER, @$_SESSION['language'], $url);
        $href = str_replace(YANA_LEFT_DELIMITER . '$AS_NUMBER' . YANA_RIGHT_DELIMITER, '', $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$AS_NUMBER' . YANA_RIGHT_DELIMITER, 'true', $url);
        $url = html_entity_decode($url);
        $link = '<a href="' . $href .  '" target="_blank">' . $language->getVar('INDEX_13') . '</a>';

        assert('!isset($urlInfo); // Cannot redeclare var $urlInfo');
        assert('!isset($errno); // Cannot redeclare var $errno');
        assert('!isset($errstr); // Cannot redeclare var $errstr');
        assert('!isset($latestVersion); // Cannot redeclare var $latestVersion');
        $latestVersion = "";

        $urlInfo = parse_url($url);

        $errno = 0;
        $errstr = "";
        if ($urlInfo !== false) {
            $this->_socket = @fsockopen($urlInfo['host'], 80, $errno, $errstr, 2);
        }

        if ($urlInfo !== false && ($this->_socket) != false) {
            if (!empty($errno)) {
                $message = 'Update-check failed to open connection to server. Reason: ' . $errstr;
                $level = \Yana\Log\TypeEnumeration::WARNING;
                \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                @fclose($this->_socket);
                $cache[$cacheId] = $link;
                return $link;
            }
            unset($errno, $errstr);

            $targetUrl = (isset($urlInfo['path']) ? $urlInfo['path'] : '/') .
                (isset($urlInfo['query']) ? '?' . $urlInfo['query'] : '');
            /* send request header */
            @fputs($this->_socket, "GET " . $targetUrl . " HTTP/1.0\r\n");
            @fputs($this->_socket, "HOST: " . $urlInfo['host'] . "\r\n");
            @fputs($this->_socket, "Connection: close\r\n\r\n");

            /* read response */
            while (!@feof($this->_socket))
            {
                $latestVersion .= @fread($this->_socket, 1024);
            }
            @fclose($this->_socket);
            unset($this->_socket);

            /* cut off header data */
            $latestVersion = preg_replace('/^.*?\r\n\r\n(.*)$/si', '$1', $latestVersion);
            $latestVersion = mb_substr($latestVersion, 0, 20);

            /* print reply */
            if (!empty($latestVersion)) {
                /**
                 * Compare versions and return result;
                 */
                if (version_compare(YANA_VERSION, $latestVersion) < 0) {
                    $link = $language->getVar('INDEX_15') . ': ' .
                            htmlspecialchars($latestVersion, ENT_COMPAT, 'UTF-8') .
                            ' <a href="' . $href .  '" target="_blank">' . $language->getVar('INDEX_16') . '</a>';

                } else {
                    $link = $language->getVar('INDEX_14') . ': ' . YANA_VERSION;
                }
            }
        }

        $cache[$cacheId] = $link;
        return $link;
    }

    /**
     * Just in case we happened to forget to close the connection.
     *
     * PHP should free the resource for us anyway, but no need to force it.
     */
    public function __destruct()
    {
        if ($this->_socket) {
            @fclose($this->_socket);
        }
    }

}

?>