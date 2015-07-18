<?php
/**
 * Test-Utility
 *
 * Collection of unit- and performance-tests.
 *
 * @type       primary
 * @author     Thomas Meyer
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Check;

/**
 * collection of unit- and performance-tests
 *
 * @package    yana
 * @subpackage plugins
 */
class CheckPlugin extends \Yana\Plugins\AbstractPlugin
{
    /**
     * Default event handler
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * SQL command line interface.
     *
     * This is a console application to run tests on the FileDB database driver and SQL-/Query-parser.
     * It takes a SQL command as input and displays the transformed internal query as it would be understood
     * by the query-parser, together with the results of the query.
     *
     * Note: as this is meant for testing purposes only, you cannot modify values via this console application.
     * Queries of type INSERT, UPDATE, and DELETE are parsed and ignored.
     * Also you cannot access the user-database via this interface.
     *
     * parameters taken:
     *
     * <ul>
     * <li> string db  name of the database to run the query on </li>
     * <li> string sql sql code </li>
     * </ul>
     *
     * @menu        group: start, title: SQL command line
     * @type        primary
     * @user        group: admin, level: 100
     * @template    null
     * @safemode    true
     * @title       SQL command line
     *
     * @access  public
     * @param   string  $db   database schema
     * @param   string  $sql  SQL statement
     */
    public function check_sql($db = 'check', $sql = '')
    {
        print '<h1>SQL command line</h1>';

        $registry = \Yana\VDrive\Registry::getGlobalInstance();
        if (!empty($sql)) {
            $sql = stripcslashes($sql);
            $fileDb = new \Yana\Db\FileDb\Connection(\Yana\Files\XDDL::getDatabase($db));
            $query = null;
            try {

                $parser = new \Yana\Db\Queries\Parser($fileDb);
                $query = $parser->parseSQL($sql);

            } catch (\Exception $e) {
                $result = "Invalid query. " . $e->getMessage();
            }
            if (strcasecmp($db, 'user') === 0) {
                $result = "Access denied.";
            } elseif ($query instanceof \Yana\Db\Queries\AbstractQuery) {
                $result = (string) $query . "\n\n";
                try {
                    switch ($query->getType())
                    {
                        case \Yana\Db\Queries\TypeEnumeration::SELECT:
                            $result .= print_r($query->getResults(), true);
                        break;
                        case \Yana\Db\Queries\TypeEnumeration::COUNT:
                            $result .= print_r($query->doesExist(), true);
                        break;
                        case \Yana\Db\Queries\TypeEnumeration::EXISTS:
                            $result .= print_r($query->countResults(), true);
                        break;
                        case \Yana\Db\Queries\TypeEnumeration::UPDATE:
                            $fileDb->update($query);
                        break;
                        case \Yana\Db\Queries\TypeEnumeration::INSERT:
                            $fileDb->insert($query);
                        break;
                        case \Yana\Db\Queries\TypeEnumeration::DELETE:
                            $fileDb->remove($query);
                        break;
                    }
                } catch (\Yana\Db\DatabaseException $e) {
                    $result .= $e->getMessage();
                }
            }
        } else {
            $sql = "";
            $result = "";
        }

        print '<form method="POST" action="' . $registry->getVar('PHP_SELF') . '">' .
        '<input type="hidden" name="' . session_name() . '" value="' . session_id() . '">' .
        '<input type="hidden" name="action" value="' . __FUNCTION__ . '">' .
        '<label>Database: <select name="db">';
        foreach (\Yana\Db\Ddl\DDL::getListOfFiles() as $database)
        {
            print '<option value="' . $database . '"' . (($database === $db) ? ' selected>' : '>') . $database .
                '</option>';
        }
        print '</select></label>&nbsp;<label>SQL: <input size="100" name="sql" value="' .
            htmlspecialchars($sql, ENT_COMPAT, 'UTF-8') . '"></label>' .
        '<input type="submit" value="OK">' .
        '<pre>' . htmlspecialchars($result, ENT_NOQUOTES, 'UTF-8') . '</pre>';
        '</form>';
        exit;
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, level: 100
     */
    public function check_foo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, role: default, level: 80
     */
    public function check_newfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, level: 60
     */
    public function check_oldfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, role: manager, level: 40
     */
    public function check_presentfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: admin, role: default, level: 75
     * @user  group: default, role: manager, level: 75
     */
    public function check_insertfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, role: user, level: 75
     */
    public function check_selectfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: admin, role: user, level: 60
     */
    public function check_editfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: default, role: otherusers, level: 50
     */
    public function check_tesafoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: bar, role: sales, level: 80
     */
    public function check_barfoo()
    {
    }

    /**
     * dummy
     *
     * @type  primary
     * @user  group: bar, role: sales, level: 70
     * @user  group: foobar, level: 50
     */
    public function check_redirectfoo()
    {
    }

    /**
     * dummy
     *
     * @type primary
     * @user  role: print, level: 50
     * @user  group: foobar, level: 60
     */
    public function check_addfoobar()
    {
    }

    /**
     * dummy
     *
     * @type primary
     * @user  group: bar
     * @user  role: helpdesk
     */
    public function check_readfoobar()
    {
    }

    /**
     * dummy
     *
     * @type primary
     * @user  group: bar, role: sales, level: 90
     * @user  group: foobar, role: helpdesk, level: 55
     */
    public function check_deletebar()
    {
    }

    /**
     * dummy
     *
     * @type primary
     * @user  level: 60
     */
    public function check_baricons()
    {
    }

}

?>
