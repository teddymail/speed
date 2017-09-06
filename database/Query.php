<?php
/**
 * The Query.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-8-18 19:02:29
 *
 * @since 0.0.2
 */

namespace supjos\database;

use App;
use Object;
use PDO;
use supjos\config\Config;
use supjos\exception\ParameterNotMatch;

/**
 * Author: Josin <www.supjos.cn>
 * Class Query
 *
 * An Query class means an query for the database, such as MySQL、Oracle and so on.
 *
 * ```Use Example```
 *
 * $query = new Query();
 *
 * $query->select(['id', 'name', 'age'])->from('{{%user}}')->where(['id'=>'33', 'name'=>'Josin'])->one();
 *
 * Like the example before, the result MySQL query statement like below:
 *
 * SELECT `id`,`name`,`age` FROM `www_user` WHERE `id`=33 AND `name` = 'Josin` LIMIT 1;
 *
 * Also you can use the raw MySQL statements using the ````$query->createCommand('SELECT * FROM www_user')->queryOne();
 *
 * @package supjos\database
 */
class Query extends Object implements QueryInterface
{
    
    /**
     * @var \PDO $queryInstance The instance of the Query class
     */
    private static $queryInstance = NULL;
    
    /**
     * @var Query The Query Instance for the Query class
     */
    private static $instance = NULL;
    /**
     * @var The fetchStyle or the $fetchRealStyle
     */
    public $fetchStyle = 'array', $fetchRealStyle = ['array'  => PDO::FETCH_ASSOC, 'both' => PDO::FETCH_BOTH,
                                                     'class'  => PDO::FETCH_CLASS, 'into' => PDO::FETCH_INTO,
                                                     'lazy'   => PDO::FETCH_LAZY, 'num' => PDO::FETCH_NUM,
                                                     'object' => PDO::FETCH_OBJ];
    /**
     * @var The variables which will be used to generate the SQL statement.
     */
    private $SELECT = '*', $DISTINCT, $FROM, $WHERE, $GROUP, $HAVING, $ORDER, $LIMIT;
    /**
     * @var string The SQL Raw which will be join into the Prepare statement execute
     */
    private $sqlStatements = 'SELECT {DISTINCT} {SELECT} FROM {FROM} {WHERE} {GROUP} {HAVING} {ORDER} {LIMIT}';
    /**
     * @var array The binding array data to bind the value to the real SQL executing
     */
    private $bindArray = [];
    /**
     * @var \PDOStatement $currentPdoStatements The current pdoStatements
     */
    private $currentPdoStatements;
    
    /**
     * Query constructor.
     * To create the $queryInstance if not exists, otherwise return it directly.
     */
    private function __construct()
    {
        
        $config = App::createObject( Config::getClass() );
        
        $configDb = $config->getConfig( 'db' );
        
        if ( isset( $configDb[ 'dsn' ] ) && isset( $configDb[ 'username' ] ) && isset( $configDb[ 'password' ] ) ) {
            static::$queryInstance = new PDO( $configDb[ 'dsn' ], $configDb[ 'username' ], $configDb[ 'password' ] );
        } else {
            throw new ParameterNotMatch( "Parameter dsn|username|password Not Set!" );
        }
    }
    
    /**
     * Return the Query instance only once, for the current request time
     *
     * @return Query
     */
    public static function getQuery()
    {
        
        if ( static::$instance === NULL ) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * @param array|string $selectFields The fields which you want to search, or pass ```*``` to search all fields
     *                                   default [[*]] ```` For Example````
     *                                   $selectFields => ['id', 'age', 'name', 'version'] or the same as:
     *                                   $selectFields = 'id, age, name, version'
     *
     * @return Query
     */
    function select( $selectFields = '*' )
    {
        
        if ( is_array( $selectFields ) && !empty( $selectFields ) ) {
            foreach ( $selectFields as $field ) {
                $this->SELECT .= "`$field`" . ', ';
            }
            $this->SELECT = substr( $this->SELECT, 0, -2 );
        } else if ( is_string( $selectFields ) && !empty( $selectFields ) ) {
            $this->SELECT = $selectFields;
        } else if ( empty( $selectFields ) ) {
            $this->SELECT = '*';
        }
        
        return $this;
    }
    
    /**
     * @param string $fromTable The table which you want to CURD, Notice: you can use the ```{{%xxx}}``` to use the
     *                          table prefix, otherwise the tableName
     *
     * @return Query
     */
    function from( $fromTable )
    {
        
        if ( is_string( $fromTable ) && !empty( $fromTable ) ) {
            $this->FROM = preg_replace( '/\{\{%(\w+)\}\}/', $this->getConfigDbPrefix() . '$1', $fromTable );
        }
        
        return $this;
    }
    
    /**
     *
     * @return string The config's db prefix
     *
     * @throws ParameterNotMatch
     * @throws \supjos\exception\NotFoundException
     */
    private function getConfigDbPrefix()
    {
        
        $config = App::createObject( Config::getClass() );
        
        $configDb = $config->getConfig( 'db' );
        
        if ( isset( $configDb[ 'prefix' ] ) ) {
            $prefix = $configDb[ 'prefix' ];
        } else {
            $prefix = '';
        }
        
        return $prefix;
    }
    
    /**
     * @param array|mixed|string $whereCondition    The where condition which equal to the SQL where statements, In
     *                                              order to use the Prepare statement, the where condition will
     *                                              generate the binding statement, ```For example```
     *                                              $whereCondition = [
     *                                              'id'=>111, 'name'=>'Josin'
     *                                              ], it can ba invoked serial times
     *
     * @return Query
     */
    function where( $whereCondition )
    {
        
        if ( is_array( $whereCondition ) && !empty( $whereCondition ) ) {
            $this->WHERE = 'WHERE ';
            foreach ( $whereCondition as $key => $value ) {
                $this->WHERE .= "`$key`" . '=' . ":{$key}" . ' AND ';
                $this->bindArray[ ":{$key}" ] = $value;
            }
            $this->WHERE = substr( $this->WHERE, 0, -5 );
        } else if ( is_string( $whereCondition ) && !empty( $whereCondition ) ) {
            $this->WHERE = 'WHERE ' . $whereCondition;
        }
        
        return $this;
    }
    
    /**
     * @param array|mixed|string $groupBy The SQL GROUP BY fields
     *
     * @return Query
     */
    function groupBy( $groupBy )
    {
        
        if ( is_array( $groupBy ) && !empty( $groupBy ) ) {
            $this->GROUP = 'GROUP BY ';
            foreach ( $groupBy as $colName => $sort ) {
                if ( is_numeric( $colName ) ) {
                    $this->GROUP .= $sort . ' ' . $this->getSqlSort() . ', ';
                } else if ( is_string( $colName ) ) {
                    $this->GROUP .= $colName . ' ' . $this->getSqlSort( $sort ) . ', ';
                }
            }
            $this->GROUP = substr( $this->GROUP, 0, -2 );
        }
        
        return $this;
    }
    
    /**
     *
     * @param int $phpSort
     *
     * @return string The SQL sort string
     */
    private function getSqlSort( $phpSort = SORT_ASC )
    {
        
        $sortOrder = [SORT_DESC => 'DESC', SORT_ASC => 'ASC'];
        
        return $sortOrder[ $phpSort ];
    }
    
    /**
     * @param array|string|mixed $having The having where condition
     *
     * @return Query
     */
    function having( $having )
    {
        
        if ( is_array( $having ) && !empty( $having ) ) {
            $this->HAVING = 'HAVING ';
            foreach ( $having as $key => $value ) {
                $this->HAVING .= "`$key`" . '=' . ":{$key}" . ' AND ';
                $this->bindArray[ ":{$key}" ] = $value;
            }
            $this->HAVING = substr( $this->HAVING, 0, -5 );
        } else if ( is_string( $having ) && !empty( $having ) ) {
            $this->HAVING = 'HAVING ' . $having;
        }
        
        return $this;
    }
    
    /**
     * @param array|mixed|string $orderBy The ORDER BY col-expression
     *
     * @return Query
     */
    function orderBy( $orderBy )
    {
        
        if ( is_array( $orderBy ) && !empty( $orderBy ) ) {
            $this->ORDER = 'ORDER BY ';
            foreach ( $orderBy as $colName => $sort ) {
                if ( is_numeric( $colName ) ) {
                    $this->ORDER .= $sort . ' ' . $this->getSqlSort( SORT_ASC ) . ', ';
                } else if ( is_string( $colName ) ) {
                    $this->ORDER .= $colName . ' ' . $this->getSqlSort( $sort ) . ', ';
                }
            }
            $this->ORDER = substr( $this->ORDER, 0, -2 );
        }
        
        return $this;
    }
    
    /**
     * @param string $limit The LIMIT expression only can be string, not array
     *
     * @return Query
     */
    function limit( $limit )
    {
        
        if ( !empty( $limit ) ) {
            $this->LIMIT = " LIMIT $limit";
        }
        
        return $this;
    }
    
    /**
     * To generate a DISTINCT SELECT QUERY STATEMENT
     *
     * @return Query
     */
    function distinct()
    {
        $this->DISTINCT = 'DISTINCT';
    }
    
    /**
     * Return one row of the data from the database
     *
     * @return mixed One row result of the data
     */
    function one()
    {
        $prepareStatements = $this->execute( $this->buildPrepareSql(), $this->bindArray );
        
        $resultRow = $prepareStatements->fetch( $this->fetchRealStyle[ $this->fetchStyle ] );
        $this->flushTempData();
        
        return $resultRow;
    }
    
    /**
     * Return the status of the execute status
     *
     * @param The   $prepareSql
     *
     * @param array $bindValues
     *
     * @return \PDOStatement The PDOStatement
     */
    function execute( $prepareSql, $bindValues = [] )
    {
        
        $this->currentPdoStatements = $pdoStatement = static::$queryInstance->prepare( $prepareSql );
        $pdoStatement->execute( $bindValues );
        
        if ( $this->getIsOk() ) {
            return $pdoStatement;
        } else {
            exit( $this->getSqlErrors() );
        }
        
    }
    
    /**
     * Return whether the previous is ok
     *
     * @return bool true success, otherwise false
     */
    public function getIsOk()
    {
        
        return $this->getErrorCode() === '00000';
    }
    
    /**
     * Return the Error code
     *
     * @return mixed
     */
    public function getErrorCode()
    {
        
        return $this->currentPdoStatements->errorCode();
    }
    
    /**
     * Return the previous error info
     *
     * @return string|NULL The previous error info
     */
    public function getSqlErrors()
    {
        
        $errorInfo = $this->currentPdoStatements->errorInfo();
        
        return empty( $errorInfo ) ? NULL :
            'Code: ' . $errorInfo[ 0 ] . ' Driver Code: ' . $errorInfo[ 1 ] . ' Driver Info: ' . $errorInfo[ 2 ];
    }
    
    /**
     * @return string The prepare Sql which you will be to run get the data
     */
    private function buildPrepareSql()
    {
        
        return str_replace( ['{SELECT}', '{DISTINCT}', '{FROM}', '{WHERE}', '{GROUP}', '{HAVING}', '{ORDER}',
                                '{LIMIT}'],
                            [$this->SELECT, $this->DISTINCT, $this->FROM, $this->WHERE, $this->GROUP, $this->HAVING,
                                $this->ORDER, $this->LIMIT], $this->sqlStatements );
    }
    
    /**
     * This method must be invoked after each Query search
     */
    private function flushTempData()
    {
        
        $this->SELECT = NULL;
        $this->DISTINCT = NULL;
        $this->FROM = NULL;
        $this->WHERE = NULL;
        $this->GROUP = NULL;
        $this->HAVING = NULL;
        $this->ORDER = NULL;
        $this->LIMIT = NULL;
        $this->bindArray = [];
        
    }
    
    /**
     * Return the data exists in database
     *
     * @return mixed All the data sets will fetched
     */
    function all()
    {
        
        $prepareStatements = $this->execute( $this->buildPrepareSql(), $this->bindArray );
        
        $resultRows = $prepareStatements->fetchAll( $this->fetchRealStyle[ $this->fetchStyle ] );
        $this->flushTempData();
        
        return $resultRows;
    }
    
    /**
     * @param $updateSql  The Update Sql which will be participate in the prepare step
     * @param $bindValues The bindValue which will be bind to the Sql
     *
     * @return int The rowCount which affected by the operation
     */
    function update( $updateSql, $bindValues )
    {
        
        $pdoStatements = $this->execute( $this->buildExecuteSql( $updateSql ), $bindValues );
        
        return $pdoStatements->rowCount();
    }
    
    /**
     * @param $executeSql The SQL which you want to participate in the UPDATE or DELETE operation
     *
     * @return string The result of the execute SQl to join the prepare
     */
    private function buildExecuteSql( $executeSql )
    {
        return preg_replace( ['/\{\{%(\w+)\}\}/', '/\[\[(\w+)\]\]/'], [$this->getConfigDbPrefix() . '$1', "`$1`"],
                             $executeSql );
    }
    
    /**
     * @param $deleteSql  The delete Sql
     * @param $bindValues The delete operation bind-values
     *
     * @return int The rowCount affected by the operation
     */
    function delete( $deleteSql, $bindValues )
    {
        
        $pdoStatements = $this->execute( $this->buildExecuteSql( $deleteSql ), $bindValues );
        
        return $pdoStatements->rowCount();
    }
    
    /**
     * @param string $insertTable  The Table which you want insert data into
     * @param array  $insertFields The data fields
     * @param array  $insertArrays The corresponding value with the data fields
     *
     * @return int The rowCount of the rows you insert into
     */
    function insert( $insertTable, $insertFields = [], $insertArrays = [] )
    {
        
        $insertSql = 'INSERT INTO ';
        if ( !empty( $insertTable ) && !empty( $insertFields ) ) {
            $insertSql .= $insertTable . ' (';
            foreach ( $insertFields as $field ) {
                $insertSql .= "`{$field}`, ";
            }
            $insertSql = substr( $insertSql, 0, -2 );
            
            $insertSql .= ') VALUES (';
            
            $loopIndex = 1;
            
            foreach ( $insertArrays as $kIndex => $value ) {
                if ( is_array( $value ) ) {
                    $insertSql = substr( $insertSql, 0, -1 );
                    $insertSql .= '(';
                    foreach ( $value as $key => $fieldValue ) {
                        $insertSql .= ':' . $loopIndex . ',';
                        $this->bindArray[ ':' . $loopIndex ] = $fieldValue;
                        $loopIndex++;
                    }
                    $insertSql = substr( $insertSql, 0, -1 );
                    $insertSql .= '),,';
                } else {
                    $insertSql .= ':' . $loopIndex . ',';
                    $this->bindArray[ ':' . $loopIndex ] = $value;
                    $loopIndex++;
                }
            }
            if ( substr( $insertSql, -2 ) !== ',,' ) {
                $insertSql = substr( $insertSql, 0, -1 );
                $insertSql .= ')';
            } else {
                $insertSql = substr( $insertSql, 0, -2 );
            }
            
            $pdoStatements = $this->execute( $this->buildExecuteSql( $insertSql ), $this->bindArray );
            
            return $pdoStatements->rowCount();
        }
    }
    
    /**
     * @param $resultStyle Setting the result style, It can be one of the following: ```array```, ```both```,
     *                     ```class```, ```into```, ```lazy```, ```num```, ```object```
     *
     * @return bool TRUE success, otherwise false
     */
    public function setResultStyle( $resultStyle )
    {
        
        if ( array_key_exists( $resultStyle, $this->fetchRealStyle ) ) {
            $this->fetchStyle = $resultStyle;
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * @return id The last insert id returned by SQL
     */
    public function getPreviousInsertId()
    {
        
        return static::$queryInstance->lastInsertId();
    }
    
    /**
     * To let the SQL in transaction, close the auto-commit
     *
     * @return bool true success, otherwise false
     */
    public function beginTransaction()
    {
        
        return static::$queryInstance->beginTransaction();
    }
    
    /**
     * Commit the All SQLs in once
     *
     * @return bool true success, otherwise false
     */
    public function commit()
    {
        
        return static::$queryInstance->commit();
    }
    
    /**
     * Roll all the SQL commit back to the before
     *
     * @return bool true success, otherwise false
     */
    public function rollBack()
    {
        
        return static::$queryInstance->rollBack();
    }
    
    /**
     * @param                            $rawSql     The raw sql which you want to use
     * @param array|\supjos\database\The $bindValues The bind-value you used in the raw sql
     *
     * @return \supjos\database\QueryRaw
     */
    public function createCommand( $rawSql, $bindValues = [] )
    {
        
        $finishedRawSql = $this->buildExecuteSql( $rawSql );
        
        $queryRaw = QueryRaw::getQueryRaw();
        
        return $queryRaw->setRawSql( $finishedRawSql, $this, $bindValues );
    }
    
    /**
     * To deny clone the object from the Query object
     */
    private function __clone()
    {
        
    }
    
    
}