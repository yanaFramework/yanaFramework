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

namespace Yana\Mails\Strategies\Contexts;

/**
 * <<context>> Abstract base class for all mailing strategies.
 *
 * @package     yana
 * @subpackage  mails
 */
abstract class AbstractContext extends \Yana\Core\Object
    implements \Yana\Mails\Strategies\Contexts\IsContext
{

    /**
     * Mailing strategy
     *
     * @var  \Yana\Mails\Strategies\IsStrategy
     */
    private $_strategy = null;

    /**
     * Sets the mailing strategy.
     *
     * The mailing strategy is an algorithm that 
     *
     * @param \Yana\Mails\Strategies\IsStrategy $strategy
     */
    public function __construct(\Yana\Mails\Strategies\IsStrategy $strategy)
    {
        $this->_strategy = $strategy;
    }

    protected function _getMailingStrategy()
    {
        return $this->_strategy;
    }

    /**
     * Scans the headers and sanitized the keys and values.
     *
     * This scans for line-breaks and illegal characters in keys and values of the headers.
     * If an invalid value is found, the function drops that header value and adds a value
     * for a "x-yana-php-header-protection" header.
     * 
     * @param   array  $headers  key-value pairs of mail headers
     * @return  array
     */
    protected function _sanitizeHeaders(array $headers)
    {
        $sanitizedHeaders = array();
        $errorCount = 0;

        assert('!isset($key); /* cannot redeclare variable $key */');
        assert('!isset($value); /* cannot redeclare variable $value */');
        foreach ($headers as $key => $value)
        {
            if (!preg_match('/^[a-z\d-]+$/', $key) || preg_match('/[\r\n]/', $value)) {
                $errorCount++;
                continue;
            }
            $value = \Yana\Data\StringValidator::sanitize($value, 128, \Yana\Data\StringValidator::LINEBREAK);
            $sanitizedHeaders[$key] = $value;
        }
        unset($key, $value);

        if ($errorCount > 0) {
            $headerProtection = '1 (Dropped  suspicious header attributes for security reasons. '.
                'Mail might contain errors)';
            $sanitizedHeaders['x-yana-php-header-protection'] = $headerProtection;
        }

        return $sanitizedHeaders;
    }

    /**
     * Restricts all headers.
     *
     * Allowed headers are "cc", "return-path", "from", "content-type", "mime-type", "content-transfer-encoding",
     * "x-mailer", "x-sender-ip", "x-server-time", "x-yana-php-header-protection" and "x-yana-php-spam-protection".
     *
     * Any other header found will be dropped.
     * When a header is dropped, a header value for "x-yana-php-header-protection" is added.
     * When a "bcc" header is found, a header value for "x-yana-php-spam-protection" is added.
     * These are added to aid spam-filters.
     *
     * @param   array  $headers  key-value pairs of mail headers
     * @return  array
     */
    protected function _restrictHeaders(array $headers)
    {
        $restrictedHeaders = array();

        assert('!isset($key); /* cannot redeclare variable $key */');
        assert('!isset($value); /* cannot redeclare variable $value */');
        foreach ($headers as $key => $value)
        {
            switch ($key)
            {
                case 'cc':
                    $restrictedHeaders['cc'] = "";

                    assert('!isset($ccValue); /* cannot redeclare variable $ccValue */');
                    foreach ((array) $value as $ccValue)
                    {
                        if (filter_var($ccValue, FILTER_VALIDATE_EMAIL)) {
                            if (!empty($restrictedHeaders['cc'])) {
                                $restrictedHeaders['cc'] .= "; ";
                            }
                            $restrictedHeaders['cc'] .= $ccValue;
                        }
                    } /* end foreach */
                    unset($ccValue);
                    break;
                case 'bcc':
                    /* bcc is not allowed! */
                    $spamProtection = '1 (bcc is not allowed in mail - recipients were dropped)';
                    $restrictedHeaders['x-yana-php-spam-protection'] = $spamProtection;
                    break;
                case 'return-path':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $restrictedHeaders['return-path'] = "$value";
                    }
                    break;
                case 'from':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $restrictedHeaders['from'] = "$value";
                    }
                    break;
                case 'content-type':
                    if (preg_match('/^(\w+\/\w+);( ?| +)charset="?[\w\d-]+"?$/i', $value)) {
                        $restrictedHeaders['content-type'] = "$value";
                    }
                    break;
                case 'mime-version':
                    if (preg_match('/^\d\.\d$/', $value)) {
                        $restrictedHeaders['mime-version'] = "$value";
                    }
                    break;
                case 'content-transfer-encoding':
                    if (preg_match('/^\d{,2}bit$/i', $value)) {
                        $restrictedHeaders['content-transfer-encoding'] = "$value";
                    }
                    break;
                case 'x-mailer':
                case 'x-sender-ip':
                case 'x-server-time':
                case 'x-yana-php-header-protection':
                case 'x-yana-php-spam-protection':
                    $restrictedHeaders[$key] = "$value";
                    break;
                default:
                    $headerProtection = '1 (Dropped suspicious header attributes for security reasons. ' .
                        'Mail might contain errors)';
                    $restrictedHeaders['x-yana-php-header-protection'] = $headerProtection;
                    break;
            }
        } /* end foreach */
        unset($key, $value);

        return $restrictedHeaders;
    }

    /**
     * Sanitizes the subject line of an e-mail.
     *
     * Scans the subject for line-breaks, tags and limits the length to 128 characters.
     *
     * @param  string  $subject  some text
     * @return string
     */
    protected function _sanitizeSubject($subject)
    {
        assert('is_string($subject); // Invalid argument $subject: string expected');

        return strip_tags(\Yana\Data\StringValidator::sanitize($subject, 128, \Yana\Data\StringValidator::LINEBREAK));
    }

    /**
     * Create and add default headers.
     *
     * @param  array  $headers  key-value pairs of mail headers
     * @return array
     */
    protected function _addDefaultHeaders(array $headers)
    {
        $defaultHeaders = array(
            'x-mailer' => "PHP/". phpversion(),
            'x-sender-ip' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR']: 'not available',
            'x-server-time' => date("c", time()),
            'content-type' => 'text/plain; charset=UTF-8',
            'mime-version' => '1.0',
            'x-yana-php-header-protection' => '0 (no suspicious header found)',
            'x-yana-php-spam-protection' => '0 (no recipients were dropped)'
        );

        return $headers + $defaultHeaders;
    }

    /**
     * Converts the array to a string.
     * 
     * @param   array  $headers  key-value pairs of mail headers
     * @return  string
     */
    protected function _convertHeadersToString(array $headers)
    {
        $headerString = "";
        $replaceCharacters = array("\n", "\r", "\f", ":");
        foreach ($headers as $key => $string)
        {
            $value = \str_replace($replaceCharacters, "", $string);
            $headerString .= $key . ": " . $value . "\r\n";
        }
        return $headerString;
    }

    /**
     * Strips tags from HTML content.
     *
     * @param   string   $text         mail body
     * @param   string   $contentType  may be plain text or HTML
     * @return  string
     */
    protected function _sanitizeText($text, $contentType)
    {
        $text = preg_replace('/@/', '[at]', "$text");
        if (preg_match('/^text\/plain/i', $contentType)) {
            $text = wordwrap($text, 70);
        } elseif (preg_match('/^text\/html/i', $contentType)) {
            while (preg_match('/<\/?(\?|\!|link|meta|script|style|img|embed|object|param|).*>/Usi', $text))
            {
                $text = preg_replace('/<\/?(\?|\!|link|meta|script|style|img|embed|object|param|).*>/Usi', '', $text);
            }
        }
        return $text;
    }

}

?>