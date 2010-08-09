<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2004 Brent Cook                                        |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA|
// +----------------------------------------------------------------------+
// | Authors: Brent Cook <busterbcook@yahoo.com>                          |
// |          Jason Pell <jasonpell@hotmail.com>                          |
// |          Lauren Matheson <inan@canada.com>                           |
// |          John Griffin <jgriffin316@netscape.net>                     |
// +----------------------------------------------------------------------+
//
// $Id: Parser.php,v 1.23 2004/05/11 05:09:02 busterb Exp $
//

require_once 'Lexer.php';
require_once 'ParserError.php';

/**
 * A sql parser
 *
 * @author  Brent Cook <busterbcook@yahoo.com>
 * @version 0.5
 * @access  public
 * @package SQL_Parser
 */
class SQL_Parser
{
    protected $lexer;
    protected $token;

// symbol definitions
    protected $functions = array();
    protected $types = array();
    protected $symbols = array();
    protected $operators = array();
    protected $synonyms = array();

    protected $dialects = array("ANSI", "MySQL");

// {{{ function __construct($dialect = "ANSI")
    public function __construct($dialect = "ANSI") {
        $this->setDialect($dialect);
    }
// }}}

// {{{ function setDialect($dialect)
    protected function setDialect($dialect) {
        if (in_array($dialect, $this->dialects)) {
            include 'Dialect_'.$dialect.'.php';
            $this->types = array_flip($dialect['types']);
            $this->functions = array_flip($dialect['functions']);
            $this->operators = array_flip($dialect['operators']);
            $this->commands = array_flip($dialect['commands']);
            $this->synonyms = $dialect['synonyms'];
            $this->symbols = array_merge(
                $this->types,
                $this->functions,
                $this->operators,
                $this->commands,
                array_flip($dialect['reserved']),
                array_flip($dialect['conjunctions']));
        } else {
            return $this->raiseError('Unknown SQL dialect:'.$dialect);
        }
    }
// }}}

// {{{ getParams(&$values, &$types)
    protected function getParams(&$values, &$types) {
        $values = array();
        $types = array();
        while ($this->token != ')') {
            $this->getTok();
            if ($this->isVal() || ($this->token == 'ident')) {
                $values[] = $this->lexer->current();
                $types[] = $this->token;
            } elseif ($this->token == ')') {
                return false;
            } else {
                return $this->raiseError('Expected a value');
            }
            $this->getTok();
            if (($this->token != ',') && ($this->token != ')')) {
                return $this->raiseError('Expected , or )');
            }
        }
    }
// }}}

    // {{{ raiseError($message)
    protected function raiseError($message) {
        throw new ParserError($this->lexer->formatError($message));
    }
    // }}}

    // {{{ isType()
    protected function isType() {
        return isset($this->types[$this->token]);
    }
    // }}}

    // {{{ isVal()
    protected function isVal() {
       return (($this->token == 'real_val') ||
               ($this->token == 'int_val') ||
               ($this->token == 'text_val') ||
               ($this->token == 'null'));
    }
    // }}}

    // {{{ isFunc()
    protected function isFunc() {
        return isset($this->functions[$this->token]);
    }
    // }}}

    // {{{ isCommand()
    protected function isCommand() {
        return isset($this->commands[$this->token]);
    }
    // }}}

    // {{{ isReserved()
    protected function isReserved() {
        return isset($this->symbols[$this->token]);
    }
    // }}}

    // {{{ isOperator()
    protected function isOperator() {
        return isset($this->operators[$this->token]);
    }
    // }}}

    // {{{ getTok()
    protected function getTok() {
        $this->lexer->next();
        $this->token = $this->lexer->key(); // cache value
        // echo $this->lexer->key()."\t".$this->lexer->current()."\n";
    }
    // }}}

    // {{{ &parseFieldOptions()
    protected function parseFieldOptions()
    {
        // parse field options
        $namedConstraint = false;
        $options = array();
        while (($this->token != ',') && ($this->token != ')') &&
                ($this->token != null)) {
            $option = $this->token;
            $haveValue = true;
            switch ($option) {
                case 'constraint':
                    $this->getTok();
                    if ($this->token == 'ident') {
                        $constraintName = $this->lexer->current();
                        $namedConstraint = true;
                        $haveValue = false;
                    } else {
                        return $this->raiseError('Expected a constraint name');
                    }
                    break;
                case 'default':
                    $this->getTok();
                    if ($this->isVal()) {
                        $constraintOpts = array('type'=>'default_value',
                                                'value'=>$this->lexer->current());
                    } elseif ($this->isFunc()) {
                        $results = $this->parseFunctionOpts();
                        $results['type'] = 'default_function';
                        $constraintOpts = $results;
                    } else {
                        return $this->raiseError('Expected default value');
                    }
                    break;
                case 'primary':
                    $this->getTok();
                    if ($this->token == 'key') {
                        $constraintOpts = array('type'=>'primary_key',
                                                'value'=>true);
                    } else {
                        return $this->raiseError('Expected "key"');
                    }
                    break;
                case 'not':
                    $this->getTok();
                    if ($this->token == 'null') {
                        $constraintOpts = array('type'=>'not_null',
                                                'value' => true);
                    } else {
                        return $this->raiseError('Expected "null"');
                    }
                    break;
                case 'check':
                    $this->getTok();
                    if ($this->token != '(') {
                        return $this->raiseError('Expected (');
                    }
                    $results = $this->parseSearchClause();
                    $results['type'] = 'check';
                    $constraintOpts = $results;
                    if ($this->token != ')') {
                        return $this->raiseError('Expected )');
                    }
                    break;
                case 'unique':
                    $this->getTok();
                    if ($this->token != '(') {
                        return $this->raiseError('Expected (');
                    }
                    $constraintOpts = array('type'=>'unique');
                    $this->getTok();
                    while ($this->token != ')') {
                        if ($this->token != 'ident') {
                            return $this->raiseError('Expected an identifier');
                        }
                        $constraintOpts['columns'][] = $this->lexer->current();
                        $this->getTok();
                        if (($this->token != ')') && ($this->token != ',')) {
                            return $this->raiseError('Expected ) or ,');
                        }
                    }
                    if ($this->token != ')') {
                        return $this->raiseError('Expected )');
                    }
                    break;
                case 'month': case 'year': case 'day': case 'hour':
                case 'minute': case 'second':
                    $intervals = array(
                                    array('month'=>0,
                                          'year'=>1),
                                    array('second'=>0,
                                          'minute'=>1,
                                          'hour'=>2,
                                          'day'=>3));
                    foreach ($intervals as $class) {
                        if (isset($class[$option])) {
                            $constraintOpts = array('quantum_1'=>$this->token);
                            $this->getTok();
                            if ($this->token == 'to') {
                                $this->getTok();
                                if (!isset($class[$this->token])) {
                                    return $this->raiseError(
                                        'Expected interval quanta');
                                }
                                if ($class[$this->token] >=
                                    $class[$constraintOpts['quantum_1']]) {
                                    return $this->raiseError($this->token.
                                        ' is not smaller than '.
                                        $constraintOpts['quantum_1']);
                                }
                                $constraintOpts['quantum_2'] = $this->token;
                            } else {
                                $this->lexer->reverseChar();
                            }
                            break;
                        }
                    }
                    if (!isset($constraintOpts['quantum_1'])) {
                        return $this->raiseError('Expected interval quanta');
                    }
                    $constraintOpts['type'] = 'values';
                    break;
                case 'null':
                    $haveValue = false;
                    break;
                default:
                    return $this->raiseError('Unexpected token '
                                        .$this->lexer->current());
            }
            if ($haveValue) {
                if ($namedConstraint) {
                    $options['constraints'][$constraintName] = $constraintOpts;
                    $namedConstraint = false;
                } else {
                    $options['constraints'][] = $constraintOpts;
                }
            }
            $this->getTok();
        }
        return $options;
    }
    // }}}

    // {{{ parseIdentifier()
    protected function parseIdentifier()
    {
        $prevTok = $this->token;
        $prevTokText = $this->lexer->current();
        $this->getTok();

        if ($prevTok != 'ident') {
            return $this->raiseError('Expected column name');
        }
        return $prevTokText;
    }
    // }}}

    // {{{ parseSearchClause()
    protected function parseSearchClause($subSearch = false)
    {
        $clause = array();
        // parse the first argument
        $this->getTok();
        if ($this->token == 'not') {
            $clause['neg'] = true;
            $this->getTok();
        }

        $foundSubclause = false;
        if ($this->token == '(') {
            $clause['arg_1']['value'] = $this->parseSearchClause(true);
            $clause['arg_1']['type'] = 'subclause';
            if ($this->token != ')') {
                return $this->raiseError('Expected ")"');
            }
            $foundSubclause = true;
        } else if ($this->isReserved()) {
            return $this->raiseError('Expected a column name or value');
        } else if ($this->token == 'ident') {
            $column = $this->parseIdentifier();
            $clause['arg_1']['value'] = $column;
            $clause['arg_1']['type'] = 'ident';
            $this->lexer->reverse();
        } else {
            $clause['arg_1']['value'] = $this->lexer->current();
            $clause['arg_1']['type'] = $this->token;
        }

        // parse the operator
        if (!$foundSubclause) {
            $this->getTok();
            if (!$this->isOperator()) {
                return $this->raiseError('Expected an operator');
            }
            $clause['op'] = $this->token;

            $this->getTok();
            switch ($clause['op']) {
                case 'is':
                    // parse for 'is' operator
                    if ($this->token == 'not') {
                        $clause['neg'] = true;
                        $this->getTok();
                    }
                    if ($this->token != 'null') {
                        return $this->raiseError('Expected "null"');
                    }
                    $clause['arg_2']['value'] = '';
                    $clause['arg_2']['type'] = $this->token;
                    break;
                case 'not':
                    // parse for 'not in' operator
                    if ($this->token != 'in') {
                        return $this->raiseError('Expected "in"');
                    }
                    $clause['op'] = $this->token;
                    $clause['neg'] = true;
                    $this->getTok();
                case 'in':
                    // parse for 'in' operator
                    if ($this->token != '(') {
                        return $this->raiseError('Expected "("');
                    }

                    // read the subset
                    $this->getTok();
                    // is this a subselect?
                    if ($this->token == 'select') {
                        $clause['arg_2']['value'] = $this->parseSelect(true);
                        $clause['arg_2']['type'] = 'command';
                    } else {
                        $this->lexer->reverse();
                        // parse the set
                        $result = $this->getParams($clause['arg_2']['value'],
                                                $clause['arg_2']['type']);
                    }
                    if ($this->token != ')') {
                        return $this->raiseError('Expected ")"');
                    }
                    break;
                case 'and': case 'or':
                    $this->lexer->reverseChar();
                    break;
                default:
                    // parse for in-fix binary operators
                    if ($this->isReserved()) {
                        return $this->raiseError('Expected a column name or value');
                    }
                    if ($this->token == '(') {
                        $clause['arg_2']['value'] = $this->parseSearchClause(true);
                        $clause['arg_2']['type'] = 'subclause';
                        $this->getTok();
                        if ($this->token != ')') {
                            return $this->raiseError('Expected ")"');
                        }
                    } else if ($this->token == 'ident') {
                        $column = $this->parseIdentifier();
                        $clause['arg_2']['value'] = $column;
                        $clause['arg_2']['type'] = 'ident';
                        $this->lexer->reverse();
                    } else {
                        $clause['arg_2']['value'] = $this->lexer->current();
                        $clause['arg_2']['type'] = $this->token;
                    }
            }
        }

        $this->getTok();
        if (($this->token == 'and') || ($this->token == 'or')) {
            $op = $this->token;
            $subClause = $this->parseSearchClause($subSearch);
            $clause = array('arg_1' => $clause,
                            'op' => $op,
                            'arg_2' => $subClause);
        } else {
            $this->lexer->reverseChar();
        }
        return $clause;
    }
    // }}}

    // {{{ parseFieldList()
    protected function parseFieldList()
    {
        $this->getTok();
        if ($this->token != '(') {
            return $this->raiseError('Expected (');
        }

        $fields = array();
        while (1) {
            // parse field identifier
            $this->getTok();
            if ($this->token == 'ident') {
                $name = $this->lexer->current();
            } elseif ($this->token == ')') {
                return $fields;
            } else {
                return $this->raiseError('Expected identifier');
            }

            // parse field type
            $this->getTok();
            if ($this->isType($this->token)) {
                $type = $this->token;
            } else {
                return $this->raiseError('Expected a valid type');
            }

            $this->getTok();
            // handle special case two-word types
            if ($this->token == 'precision') {
                // double precision == double
                if ($type == 'double') {
                    return $this->raiseError('Unexpected token');
                }
                $this->getTok();
            } elseif ($this->token == 'varying') {
                // character varying() == varchar()
                if ($type == 'character') {
                    $type == 'varchar';
                    $this->getTok();
                } else {
                    return $this->raiseError('Unexpected token');
                }
            }
            $fields[$name]['type'] = $this->synonyms[$type];
            // parse type parameters
            if ($this->token == '(') {
                $results = $this->getParams($values, $types);
                switch ($fields[$name]['type']) {
                    case 'numeric':
                        if (isset($values[1])) {
                            if ($types[1] != 'int_val') {
                                return $this->raiseError('Expected an integer');
                            }
                            $fields[$name]['decimals'] = $values[1];
                        }
                    case 'float':
                        if ($types[0] != 'int_val') {
                            return $this->raiseError('Expected an integer');
                        }
                        $fields[$name]['length'] = $values[0];
                        break;
                    case 'char': case 'varchar':
                    case 'integer': case 'int':
                        if (sizeof($values) != 1) {
                            return $this->raiseError('Expected 1 parameter');
                        }
                        if ($types[0] != 'int_val') {
                            return $this->raiseError('Expected an integer');
                        }
                        $fields[$name]['length'] = $values[0];
                        break;
                    case 'set': case 'enum':
                        if (!sizeof($values)) {
                            return $this->raiseError('Expected a domain');
                        }
                        $fields[$name]['domain'] = $values;
                        break;
                    default:
                        if (sizeof($values)) {
                            return $this->raiseError('Unexpected )');
                        }
                }
                $this->getTok();
            }

            $options = $this->parseFieldOptions();

            $fields[$name] += $options;

            if ($this->token == ')') {
                return $fields;
            } elseif (is_null($this->token)) {
                return $this->raiseError('Expected )');
            }
        }
    }
    // }}}

    // {{{ parseFunctionOpts()
    protected function parseFunctionOpts()
    {
        $function = $this->token;
        $opts['name'] = $function;
        $this->getTok();
        if ($this->token != '(') {
            return $this->raiseError('Expected "("');
        }
        switch ($function) {
            case 'count':
                $this->getTok();
                switch ($this->token) {
                    case 'distinct':
                        $opts['distinct'] = true;
                        $this->getTok();
                        if ($this->token != 'ident') {
                            return $this->raiseError('Expected a column name');
                        }
                    case 'ident': case '*':
                        $opts['arg'][] = $this->lexer->current();
                        break;
                    default:
                        return $this->raiseError('Invalid argument');
                }
                break;
            case 'concat':
                $this->getTok();
                while ($this->token != ')') {
                    switch ($this->token) {
                        case 'ident': case 'text_val':
                            $opts['arg'][] = $this->lexer->current();
                            break;
                        case ',':
                            // do nothing
                            break;
                        default:
                            return $this->raiseError('Expected a string or a column name');
                    }
                    $this->getTok();
                }
                $this->lexer->reverse();
                break;
            case 'avg': case 'min': case 'max': case 'sum':
            default:
                $this->getTok();
                $opts['arg'] = $this->lexer->current();
                break;
        }
        $this->getTok();
        if ($this->token != ')') {
            return $this->raiseError('Expected ")"');
        }
        return $opts;
    }
    // }}}

    // {{{ parseCreate()
    protected function parseCreate() {
        $this->getTok();
        switch ($this->token) {
            case 'table':
                $tree = array('command' => 'create_table');
                $this->getTok();
                if ($this->token == 'ident') {
                    $tree['tables'][] = $this->lexer->current();
                    $fields = $this->parseFieldList();
                    $tree['column_defs'] = $fields;
//                    $tree['columns'] = array_keys($fields);
                } else {
                    return $this->raiseError('Expected table name');
                }
                break;
            case 'index':
                $tree = array('command' => 'create_index');
                break;
            case 'constraint':
                $tree = array('command' => 'create_constraint');
                break;
            case 'sequence':
                $tree = array('command' => 'create_sequence');
                break;
            default:
                return $this->raiseError('Unknown object to create');
        }
        return $tree;
    }
    // }}}

    // {{{ parseInsert()
    protected function parseInsert() {
        $this->getTok();
        if ($this->token == 'into') {
            $tree = array('command' => 'insert');
            $this->getTok();
            if ($this->token == 'ident') {
                $tree['tables'][] = $this->lexer->current();
                $this->getTok();
            } else {
                return $this->raiseError('Expected table name');
            }
            if ($this->token == '(') {
                $results = $this->getParams($values, $types);
                if (sizeof($values)) {
                    $tree['columns'] = $values;
                }
                $this->getTok();
            }
            if ($this->token == 'values') {
                $this->getTok();
                $results = $this->getParams($values, $types);
                if (isset($tree['column_defs']) &&
                    (sizeof($tree['column_defs']) != sizeof($values))) {
                    return $this->raiseError('field/value mismatch');
                }
                if (sizeof($values)) {
                    foreach ($values as $key=>$value) {
                        $values[$key] = array('value'=>$value,
                                                'type'=>$types[$key]);
                    }
                    $tree['values'] = $values;
                } else {
                    return $this->raiseError('No fields to insert');
                }
            } else {
                return $this->raiseError('Expected "values"');
            }
        } else {
            return $this->raiseError('Expected "into"');
        }
        $this->getTok();
        if (!is_null($this->token) && $this->token !== ';') {
            return $this->raiseError('Unexpected token');
        }
        return $tree;
    }
    // }}}

    // {{{ parseUpdate()
    protected function parseUpdate() {
        $this->getTok();
        if ($this->token == 'ident') {
            $tree = array('command' => 'update');
            $tree['tables'][] = $this->lexer->current();
        } else {
            return $this->raiseError('Expected table name');
        }
        $this->getTok();
        if ($this->token != 'set') {
            return $this->raiseError('Expected "set"');
        }
        while (true) {
            $this->getTok();
            if ($this->token != 'ident') {
                return $this->raiseError('Expected a column name');
            }
            $tree['columns'][] = $this->lexer->current();
            $this->getTok();
            if ($this->token != '=') {
                return $this->raiseError('Expected =');
            }
            $this->getTok();
            if (!$this->isVal($this->token)) {
                return $this->raiseError('Expected a value');
            }
            $tree['values'][] = array('value'=>$this->lexer->current(),
                                      'type'=>$this->token);
            $this->getTok();
            if ($this->token == 'where') {
                $clause = $this->parseSearchClause();
                $tree['where_clause'] = $clause;
                break;
            } elseif ($this->token != ',') {
                return $this->raiseError('Expected "where" or ","');
            }
        }
        $this->getTok();
        if (!is_null($this->token) && $this->token !== ';') {
            return $this->raiseError('Unexpected token');
        }
        return $tree;
    }
    // }}}

    // {{{ parseDelete()
    protected function parseDelete() {
        $this->getTok();
        if ($this->token != 'from') {
            return $this->raiseError('Expected "from"');
        }
        $tree = array('command' => 'delete');
        $this->getTok();
        if ($this->token != 'ident') {
            return $this->raiseError('Expected a table name');
        }
        $tree['tables'][] = $this->lexer->current();
        $this->getTok();
        if ($this->token == 'where') {
            $clause = $this->parseSearchClause();
            $tree['where_clause'] = $clause;
            if ($this->token == 'order') {
                $this->getTok();
                if ($this->token != 'by') {
                    return $this->raiseError('Expected "by"');
                }
                $this->getTok();
                while ($this->token == 'ident') {
                    $col = $this->lexer->current();
                    $this->getTok();
                    if (isset($this->synonyms[$this->token])) {
                        $order = $this->synonyms[$this->token];
                        if (($order != 'asc') && ($order != 'desc')) {
                            return $this->raiseError('Unexpected token');
                        }
                        $this->getTok();
                    } else {
                        $order = 'asc';
                    }
                    if ($this->token == ',') {
                        $this->getTok();
                    }
                    $tree['sort_order'][$col] = $order;
                }
            }
        }

        $this->getTok();
        if (!is_null($this->token) && $this->token !== ';') {
            return $this->raiseError('Unexpected token');
        }
        return $tree;
    }
    // }}}

    // {{{ parseDrop()
    protected function parseDrop() {
        $this->getTok();
        switch ($this->token) {
            case 'table':
                $isTable = true;
                // fall through
            case 'index':
            case 'constraint':
            case 'sequence':
                $tree = array('command' => 'drop_' . $this->token);
                $this->getTok();
                if ($this->token != 'ident') {
                    return $this->raiseError('Expected a ' . $this->token . ' name');
                }
                $tree['target'][] = $this->lexer->current();
                if (!empty($isTable)) {
                    $this->getTok();
                    if (($this->token == 'restrict') ||
                        ($this->token == 'cascade')) {
                        $tree['drop_behavior'] = $this->token;
                    }
                }
                break;
            default:
                return $this->raiseError('Unknown object to drop');
        }
        $this->getTok();
        if (!is_null($this->token) && $this->token !== ';') {
            return $this->raiseError('Unexpected token');
        }
        return $tree;
    }
    // }}}

    // {{{ parseSelect()
    protected function parseSelect($subSelect = false) {
        $tree = array('command' => 'select');
        $this->getTok();
        if (($this->token == 'distinct') || ($this->token == 'all')) {
            $tree['set_quantifier'] = $this->token;
            $this->getTok();
        }
        if ($this->token == '*') {
            $tree['columns'] = array();
            $this->getTok();
        } elseif ($this->token == 'ident' || $this->token == 'int_val' || $this->isFunc()) {
            while ($this->token != 'from') {
                if ($this->token == 'ident' || $this->token == 'int_val') {
                    if ($this->token == 'ident') {
                        $column = $this->parseIdentifier();
                    } else {
                        $column = $this->lexer->current();
                        $this->getTok();
                    }

                    if ($this->token == 'as') {
                        $this->getTok();
                        if ($this->token == 'ident' ) {
                            $columnAlias = $this->lexer->current();
                        } else {
                            return $this->raiseError('Expected column alias');
                        }
                    } elseif ($this->token == 'ident') {
                        $columnAlias = $this->lexer->current();
                    } else {
                        $columnAlias = '';
                    }
                    if (!empty($columnAlias)) {
                        $tree['columns'][$columnAlias] = $column;
                    } else {
                        $tree['columns'][] = $column;
                    }
                    if ($this->token != 'from') {
                        $this->getTok();
                    }
                    if ($this->token == ',') {
                        $this->getTok();
                    }
                } elseif ($this->isFunc()) {
                    if (!isset($tree['set_quantifier'])) {
                        $result = $this->parseFunctionOpts();

                        // check for an alias
                        $this->getTok();
                        switch ($this->token) {
                            case ',':
                                $this->lexer->reverse();
                            // fall through
                            case 'from':
                                $columnAlias = '';
                                break;
                            case 'as':
                                $this->getTok();
                                if ($this->token != 'ident' ) {
                                    return $this->raiseError('Expected column alias');
                                }
                            // fall through
                            case 'ident':
                                $columnAlias = $this->lexer->current();
                                $this->getTok();
                                break;
                            default:
                                return $this->raiseError('Expected column alias, from or comma');
                        } // end switch
                        if (!empty($columnAlias)) {
                            $tree['set_function'][$columnAlias] = $result;
                        } else {
                            $tree['set_function'][] = $result;
                        }
                    } else {
                        return $this->raiseError('Cannot use "'.
                                $tree['set_quantifier'].'" with '.$this->token);
                    }
                } elseif ($this->token == ',') {
                    $this->getTok();
                } else {
                    return $this->raiseError('Unexpected token "'.$this->token.'"');
                }
            } // end while
        } else {
            return $this->raiseError('Expected columns or a set function');
        }
        if ($this->token != 'from') {
            return $this->raiseError('Expected "from"');
        }
        $this->getTok();
        while ($this->token == 'ident') {
            $tableName = $this->lexer->current();
            $this->getTok();
            switch ($this->token) {
                case 'as':
                    $this->getTok();
                    if ($this->token != 'ident') {
                        return $this->raiseError('Expected table alias');
                    }
                    // fall through
                case 'ident':
                    $tableAlias = $this->lexer->current();
                    $this->getTok();
                    break;
                default:
                    $tableAlias = '';
                    break;
            } // end switch
            if (!empty($tableAlias)) {
                $tree['tables'][$tableAlias] = $tableName;
            } else {
                $tree['tables'][] = $tableName;
            }
            if ($this->token == 'on') {
                $clause = $this->parseSearchClause();
                $tree['table_join_clause'][] = $clause;
            } else {
                $tree['table_join_clause'][] = '';
            }
            switch ($this->token) {
                case ',':
                    $tree['table_join'][] = ',';
                    $this->getTok();
                    break;
                case 'join':
                    $tree['table_join'][] = 'join';
                    $this->getTok();
                    break;
                case 'cross':
                case 'inner':
                    $join = $this->lexer->current();
                    $this->getTok();
                    if ($this->token != 'join') {
                        return $this->raiseError('Expected token "join"');
                    }
                    $tree['table_join'][] = $join.' join';
                    $this->getTok();
                    break;
                case 'left':
                case 'right':
                    $join = $this->lexer->current();
                    $this->getTok();
                    if ($this->token == 'join') {
                        $tree['table_join'][] = $join.' join';
                    } elseif ($this->token == 'outer') {
                            $join .= ' outer';
                        $this->getTok();
                        if ($this->token == 'join') {
                            $tree['table_join'][] = $join.' join';
                        } else {
                            return $this->raiseError('Expected token "join"');
                        }
                    } else {
                        return $this->raiseError('Expected token "outer" or "join"');
                    }
                    $this->getTok();
                    break;
                case 'natural':
                    $join = $this->lexer->current();
                    $this->getTok();
                    if ($this->token == 'join') {
                        $tree['table_join'][] = $join.' join';
                    } elseif (($this->token == 'left') ||
                                ($this->token == 'right')) {
                            $join .= ' '.$this->token;
                        $this->getTok();
                        if ($this->token == 'join') {
                            $tree['table_join'][] = $join.' join';
                        } elseif ($this->token == 'outer') {
                            $join .= ' '.$this->token;
                            $this->getTok();
                            if ($this->token == 'join') {
                                $tree['table_join'][] = $join.' join';
                            } else {
                                return $this->raiseError('Expected token "join" or "outer"');
                            }
                        } else {
                            return $this->raiseError('Expected token "join" or "outer"');
                        }
                    } else {
                        return $this->raiseError('Expected token "left", "right" or "join"');
                    }
                    $this->getTok();
                    break;
                    case 'where':
                    case 'order':
                    case 'limit':
                        break 2;
                    default:
                        if (is_null($this->token)) {
                            break 2;
                        }
                        break;
            }
        }
        while (!is_null($this->token) && (!$subSelect || $this->token != ')')
               && $this->token != ')') {
            switch ($this->token) {
                case 'having':
                    $clause = $this->parseSearchClause();
                    $tree['having_clause'] = $clause;
                    break;
                case 'where':
                    $clause = $this->parseSearchClause();
                    $tree['where_clause'] = $clause;
                    break;
                case 'order':
                    $this->getTok();
                    if ($this->token != 'by') {
                        return $this->raiseError('Expected "by"');
                    }
                    $this->getTok();
                    while ($this->token == 'ident') {
                        $col = $this->lexer->current();
                        $this->getTok();
                        if (isset($this->synonyms[$this->token])) {
                            $order = $this->synonyms[$this->token];
                            if (($order != 'asc') && ($order != 'desc')) {
                                return $this->raiseError('Unexpected token');
                            }
                            $this->getTok();
                        } else {
                            $order = 'asc';
                        }
                        if ($this->token == ',') {
                            $this->getTok();
                        }
                        $tree['sort_order'][$col] = $order;
                    }
                    break;
                case 'limit':
                    $this->getTok();
                    if ($this->token != 'int_val') {
                        return $this->raiseError('Expected an integer value');
                    }
                    $length = $this->lexer->current();
                    $start = 0;
                    $this->getTok();
                    if ($this->token == ',') {
                        $this->getTok();
                        if ($this->token != 'int_val') {
                            return $this->raiseError('Expected an integer value');
                        }
                        $start = $length;
                        $length = $this->lexer->current();
                        $this->getTok();
                    }
                    $tree['limit_clause'] = array('start'=>$start,
                                                  'length'=>$length);
                    break;
                case 'group':
                    $this->getTok();
                    if ($this->token != 'by') {
                        return $this->raiseError('Expected "by"');
                    }
                    $this->getTok();
                    while ($this->token == 'ident') {
                        $col = $this->lexer->current();
                        $this->getTok();
                        if ($this->token == ',') {
                            $this->getTok();
                        }
                        $tree['group_by'][] = $col;
                    }
                    break;
                case ';':
                    return $tree;
                default:
                    return $this->raiseError('Unexpected clause');
            }
        }
        return $tree;
    }
    // }}}

    // {{{ parse($string)
    public function parse($string)
    {
        // Initialize the Lexer with a 3-level look-back buffer
        $this->lexer = new Lexer($string, 3);
        $this->lexer->setSymbols($this->symbols);

        // get query action
        $this->getTok();
        switch ($this->token) {
            case null:
                // null == end of string
                return $this->raiseError('Nothing to do');
            case 'select':
                return $this->parseSelect();
            case 'update':
                return $this->parseUpdate();
            case 'insert':
                return $this->parseInsert();
            case 'delete':
                return $this->parseDelete();
            case 'create':
                return $this->parseCreate();
            case 'drop':
                return $this->parseDrop();
            default:
                return $this->raiseError('Unknown action :'.$this->token);
        }
    }
    // }}}
}
?>