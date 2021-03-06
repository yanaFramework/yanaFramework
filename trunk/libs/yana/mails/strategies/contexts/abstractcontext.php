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
abstract class AbstractContext extends \Yana\Core\StdObject implements \Yana\Mails\Strategies\Contexts\IsContext
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
     * The mailing strategy is an algorithm that handles sending the actual e-mail.
     *
     * For examples on how to use this:
     * You could inject a NULL-object (that doesn't actually send an e-mail) for unit testing purposes.
     * An object, that just displays the mail instead of sending it for debugging.
     * An object, that sends the mail to an internal messenging system for community mailing aso.
     * Or use a custom mail handler to connect to an SMTP that is not the default SMTP server of your system.
     *
     * @param \Yana\Mails\Strategies\IsStrategy $strategy
     */
    public function __construct(\Yana\Mails\Strategies\IsStrategy $strategy)
    {
        $this->_strategy = $strategy;
    }

    /**
     * Returns the chosen mailing strategy.
     *
     * The mailing strategy object is an algorithm that handles the sending of e-mails.
     *
     * @return \Yana\Mails\Strategies\IsStrategy
     */
    protected function _getMailingStrategy(): \Yana\Mails\Strategies\IsStrategy
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
    protected function _sanitizeHeaders(array $headers): array
    {
        $errorCount = 0;

        // Iterates over all array-elements and sanitizes the contents.
        $sanitizedHeaders = $this->_walkHeader($headers, $errorCount);

        if ($errorCount > 0) {
            $headerProtection = '1 (Dropped suspicious header attributes for security reasons. '.
                'Mail might contain errors)';
            $sanitizedHeaders['x-yana-php-header-protection'] = $headerProtection;
        }

        return $sanitizedHeaders;
    }

    /**
     * Iterates over all array-elements and sanitizes the contents.
     *
     * @param   array  $values       header values
     * @param   int    &$errorCount  number of removed headers
     * @return  array
     */
    private function _walkHeader(array $values, &$errorCount): array
    {
        $sanitizedHeaders = array();
        foreach ($values as $key => $value)
        {
            if (!preg_match('/^[a-z\d\-]+$/', $key) || (is_string($value) && preg_match('/[\r\n\f]/', $value))) {
                $errorCount++;
                continue;
            } elseif (is_array($value)) {
                $value = $this->_walkHeader($value, $errorCount);
            } else {
                $value = \Yana\Data\StringValidator::sanitize($value, 128, \Yana\Data\StringValidator::LINEBREAK);
            }
            $sanitizedHeaders[$key] = $value;
        }
        return $sanitizedHeaders;
    }

    /**
     * Restricts all headers.
     *
     * Allowed headers are:
     * "cc",
     * "bcc",
     * "return-path",
     * "reply-to",
     * "from",
     * "sender",
     * "importance",
     * "content-type",
     * "date",
     * "mime-type",
     * "content-transfer-encoding",
     * "x-priority",
     * "x-mailer",
     * "x-sender-ip",
     * "x-server-time",
     * "x-yana-php-header-protection".
     *
     * Any other header found will be dropped.
     * When a header is dropped, a header value for "x-yana-php-header-protection" is added.
     * This is added to aid spam-filters.
     *
     * @param   array  $headers  key-value pairs of mail headers
     * @return  array
     */
    protected function _restrictHeaders(array $headers): array
    {
        $restrictedHeaders = array();

        assert(!isset($key), 'cannot redeclare variable $key');
        assert(!isset($value), 'cannot redeclare variable $value');
        foreach ($headers as $key => $value)
        {
            switch ($key)
            {
                case 'cc':
                case 'bcc':
                case 'reply-to':
                    $restrictedHeaders[$key] = "";

                    assert(!isset($mail), 'cannot redeclare variable $mail');
                    foreach ((array) $value as $mail)
                    {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                            if (!empty($restrictedHeaders[$key])) {
                                $restrictedHeaders[$key] .= "; ";
                            }
                            $restrictedHeaders[$key] .= $mail;
                        }
                    } /* end foreach */
                    unset($mail);
                    break;
                case 'return-path':
                case 'sender':
                case 'from':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $restrictedHeaders[$key] = "$value";
                    }
                    break;
                case 'importance':
                    switch (strtolower($value))
                    {
                        case 'high':
                        case 'normal':
                        case 'low':
                            $restrictedHeaders[$key] = "$value";
                    }
                    break;
                case 'content-type':
                    if (preg_match('/^(\w+\/\w+);( ?| +)charset="?[\w\d\-]+"?$/i', $value)) {
                        $restrictedHeaders['content-type'] = "$value";
                    }
                    break;
                case 'mime-version':
                    if (preg_match('/^\d\.\d$/', $value)) {
                        $restrictedHeaders['mime-version'] = "$value";
                    }
                    break;
                case 'content-transfer-encoding':
                    if (preg_match('/^\d{1,2}bit$/i', $value)) {
                        $restrictedHeaders['content-transfer-encoding'] = "$value";
                    }
                    break;
                case 'date':
                case 'x-priority':
                case 'x-mailer':
                case 'x-sender-ip':
                case 'x-server-time':
                case 'x-yana-php-header-protection':
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
    protected function _sanitizeSubject(string $subject): string
    {
        return strip_tags(\Yana\Data\StringValidator::sanitize($subject, 128, \Yana\Data\StringValidator::LINEBREAK));
    }

    /**
     * Create and add default headers.
     *
     * @param  array  $headers  key-value pairs of mail headers
     * @return array
     */
    protected function _addDefaultHeaders(array $headers): array
    {
        $defaultHeaders = array(
            'x-mailer' => "PHP/". phpversion(),
            'x-sender-ip' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR']: 'not available',
            'x-server-time' => date("c", time()),
            'content-type' => 'text/plain; charset=UTF-8',
            'mime-version' => '1.0',
            'date' => \date('r'),
            'x-yana-php-header-protection' => '0 (no suspicious header found)'
        );

        return $headers + $defaultHeaders;
    }

    /**
     * Converts the array to a string.
     * 
     * @param   array  $headers  key-value pairs of mail headers
     * @return  string
     */
    protected function _convertHeadersToString(array $headers): string
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
    protected function _sanitizeText(string $text, string $contentType): string
    {
        $sanitizedText = $textWithAtStripped = preg_replace('/@/', '[at]', "$text");
        if (preg_match('/^text\/plain/i', $contentType)) {
            $sanitizedText = wordwrap($textWithAtStripped, 70);
        } elseif (preg_match('/^text\/html/i', $contentType)) {
            // basically all except form tags, images, frames, header tags and script elements
            $allowableTags = '<a>,<abbr>,<acronym>,<address>,' .
                '<b>,<big>,<br>,<blockquote>,' .
                '<caption>,<center>,<cite>,<code>,<col>,<colgroup>,' .
                '<dd>,<del>,<dfn>,<dir>,<div>,<dl>,<dt>,' .
                '<em>,' .
                '<font>,' .
                '<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<hr>,' .
                '<i>,<ins>,' .
                '<kbd>,' .
                '<li>,' .
                '<ol>,' .
                '<p>,<pre>,' .
                '<q>,' .
                '<samp>,<small>,<span>,<strong>,<sub>,<sup>' .
                '<table>,<tbody>,<td>,<tfoot>,<th>,<thead>,<tr>,<tt>';
            $textWithStrippedTags = \strip_tags($textWithAtStripped, $allowableTags);
            // strip_tags does not remove malicious attributes, so we do this here:
            $sanitizedText = \preg_replace('/<(\S+)\s[^<]*o\s*n\s*[^<]+>/Uis', '<$1>', $textWithStrippedTags);
        }
        return $sanitizedText;
    }

}

?>