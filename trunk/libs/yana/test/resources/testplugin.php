<?php
/**
 * Title
 *
 * Description.
 *
 * {@translation
 *
 *   A: Title A
 *
 *       Translation A.
 *
 *   , B: Title B
 *
 *       Translation B.
 * }
 *
 * @author   Author 1
 * @author   Author 2
 * @type     primary
 * @group    my Group
 * @license  Some License
 * @priority 10
 * @extends  my Parent
 * @requires Dependency 1
 * @requires Dependency 2
 * @url      Some URL
 * @version  12.3 Beta
 * @active   2
 * @menu     group: groupname, title: Menu Title, image: icon1.png
 * @menu     group: groupname.sub, title: {lang id="menu.title"}, image: icon2.png
 *
 * @ignore
 * @package  test
 */

/**
 * @ignore
 * @package  test
 */
class TestPlugin
{

    /**
     * @type        read
     * @user        group: my_Group, role: my_Role, level: 12
     * @template    testplugin.php
     * @menu        group: groupname, title: Menu Title 2, image: icon3.png
     * @safemode    true
     * @onerror     goto: error_action, text: Error
     * @onsuccess   goto: success_action, text: Success
     * @overwrite   true
     * @subscribe   true
     * @script      Script1.js
     * @script      Script2.js
     * @script
     * @style       Style1.css
     * @style       Style2.css
     * @style
     *
     * @param       string  $a  Some text
     * @param       int     $b  some Number
     * @return      array
     */
    public function testA($a, $b = 123)
    {
        // do nothing
    }
}