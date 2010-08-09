<?php
/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author     Moxiecode
 * @copyright  Copyright � 2004-2007, Moxiecode Systems AB, All rights reserved.
 * @package    SpellChecker
 */

/**
 * spell checker base class
 * @package SpellChecker
 */
abstract class SpellChecker
{
    /**
     * configuration
     *
     * @var array
     */
    private $_config = array();
	/**
	 * Constructor.
	 *
	 * @param $config Configuration name/value array.
	 */
	public function __construct(&$config)
    {
		$this->_config = $config;
	}

	/**
	 * Simple loopback function everything that gets in will be send back.
	 *
	 * @param array $args  Arguments.
	 * @return array Array of all input arguments.
	 */
	public function &loopback(/* args.. */)
    {
		return func_get_args();
	}

	/**
	 * Spellchecks an array of words.
	 *
	 * @param string $lang Language code like sv or en.
	 * @param array $words Array of words to spellcheck.
	 * @return array Array of misspelled words.
	 */
	public abstract function &checkWords($lang, $words);

	/**
	 * Returns suggestions of for a specific word.
	 *
	 * @param string $lang Language code like sv or en.
	 * @param string $word Specific word to get suggestions for.
	 * @return array Array of suggestions for the specified word.
	 */
	public abstract function &getSuggestions($lang, $word);

	/**
	 * Throws an error message back to the user. This will stop all execution.
	 *
	 * @param string $str Message to send back to user.
	 */
	function throwError($str)
    {
		die('{"result":null,"id":null,"error":{"errstr":"' . addslashes($str) .
            '","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');
	}
}

?>
