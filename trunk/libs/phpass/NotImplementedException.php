<?php
/**
 * Portable PHP password hashing framework.
 *
 * Version 0.3 / genuine.
 *
 * Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
 * the public domain.  Revised in subsequent years, still public domain.
 *
 * There's absolutely no warranty.
 *
 * The homepage URL for this framework is:
 *
 *	http://www.openwall.com/phpass/
 *
 * Please be sure to update the Version line if you edit this file in any way.
 * It is suggested that you leave the main version number intact, but indicate
 * your project name (after the slash) and add your own revision information.
 *
 * Please do not change the "private" password hashing method implemented in
 * here, thereby making your hashes incompatible.  However, if you must, please
 * change the hash type identifier (the "$P$") to something different.
 *
 * Obviously, since this code is in the public domain, the above are not
 * requirements (there can be none), but merely suggestions.
 *
 * @package PhPass
 */

namespace PhPass;

/**
 * @package PhPass
 */
class NotImplementedException extends \Exception
{
    // intentionally left blank
}

?>