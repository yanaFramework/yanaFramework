<?php
/**
 * Hello World example
 *
 * This is an example to show newbies how to make their first steps using the framework.
 * Feel free to view, test and modify the source code.
 * 
 * @type       default
 * @priority   2
 * @author     Thomas Meyer
 * @package    yana
 * @subpackage plugins
 */

/**
 * <<plugin>> Example plugin
 *
 * @package     yana
 * @subpackage  plugins
 */
class Plugin_HelloWorld extends StdClass implements \Yana\IsPlugin
{

    /**
     * Default event handler.
     *
     * The default event handler catches all events, whatever they might be.
     * If you don't need it, you may deactive it by adding an @ignore to the annotations below.
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Basic usage example.
     *
     * Call: index.php?action=exampleHelloWorld
     * Outputs: Hello World.
     *
     * @access  public
     */
    public function exampleHelloWorld()
    {
        print "Hello World.";
    }

    /**
     * Web-Service example.
     *
     * This example shows you how to implement a simple REST-service.
     *
     * Call: index.php?action=exampleSum&a=1&b=1
     * Outputs: 2
     *
     * @access  public
     * @param   int  $a  first operand
     * @param   int  $b  second operand
     * @return  int
     */
    public function exampleSum($a, $b)
    {
        return $a + $b;
    }

    /**
     * Template usage example.
     *
     * This example shows you how to use Smarty templates.
     *
     * Call: index.php?action=exampleTemplate
     * Outputs: Hello PHP-World!.
     *
     * (Note on annotations:
     * - "menu" adds a link to an existing menu.
     * - "title" selects the menu text.
     * - "template" selects the path to the used Smarty template.
     * )
     *
     * @access    public
     *
     * @menu      group: start
     * @title     Hello World
     *
     * @template  templates/example.html.tpl
     */
    public function exampleTemplate()
    {
        // sets the template var $world to 'PHP-World'
        \Yana\Application::getInstance()->setVar('world', 'PHP-World!');
    }

    /**
     * Database usage example
     *
     * Call: index.php?action=exampleDatabase
     *
     * (Note on annotations:
     * - "user" restricts access to this function to users with the listed minimum privileges
     * You may have multiple "user" tags to set up multiple alternatives.
     * In this example, only registered users with a minimum level of 1 or members of role "example"
     * are granted to call this function.
     * )
     *
     * @access    public
     * @user      group: registered, level: 1
     * @user      role: example
     */
    public function exampleDatabase()
    {
        // Open connection to database "log"
        $connection = \Yana\Application::connect('log');

        // Select contents of table "log"
        $rows = $connection->select('log');

        // Another way to write a query:
        $select = new \Yana\Db\Queries\Select($connection);
        $select->setTable('log');
        $select->setLimit(50);
        $rows = $select->getResults();

        // Printing the rows.
        print_r($rows);
    }

}

?>