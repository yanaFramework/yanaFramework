<?php
/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author     Moxiecode
 * @copyright  Copyright � 2004-2007, Moxiecode Systems AB, All rights reserved.
 * @package    SpellChecker
 */

class PSpell extends SpellChecker
{
	/**
	 * Spellchecks an array of words.
	 *
	 * @param string $lang Language code like sv or en.
	 * @param array $words Array of words to spellcheck.
	 * @return array Array of misspelled words.
	 */
	public function &checkWords($lang, $words) {
		$plink = $this->_getPLink($lang);

		$outWords = array();
		foreach ($words as $word)
        {
			if (!pspell_check($plink, trim($word))) {
				$outWords[] = utf8_encode($word);
            }
		}

		return $outWords;
	}

	/**
	 * Returns suggestions of for a specific word.
	 *
	 * @param string $lang Language code like sv or en.
	 * @param string $word Specific word to get suggestions for.
	 * @return array Array of suggestions for the specified word.
	 */
	public function &getSuggestions($lang, $word)
    {
		$words = pspell_suggest($this->_getPLink($lang), $word);

		for ($i=0; $i<count($words); $i++)
        {
			$words[$i] = utf8_encode($words[$i]);
        }

		return $words;
	}

	/**
	 * Opens a link for pspell.
	 * @param string $lang Language code like sv or en.
	 */
	private function &_getPLink($lang)
    {
		// Check for native PSpell support
		if (!function_exists("pspell_new")) {
			$this->throwError("PSpell support not found in PHP installation.");
        }

		// Setup PSpell link
		$plink = pspell_new(
			$lang,
			$this->_config['PSpell.spelling'],
			$this->_config['PSpell.jargon'],
			$this->_config['PSpell.encoding'],
			$this->_config['PSpell.mode']
		);

		if (!$plink) {
			$this->throwError("No PSpell link found opened.");
        }

		return $plink;
	}
}

?>
