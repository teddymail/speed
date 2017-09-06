<?php
/**
 * The QueryRaw.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-20 05:50:29
 *
 * @since 0.0.2
 */

namespace supjos\database;

use Object;

class QueryRaw extends Object
{
    
    /**
     * @var \supjos\database\QueryRaw $queryRawInstance The instance of the QueryRaw class
     */
    private static $queryRawInstance = NULL;
    /**
     * @var string $executeSql The sql which you want to execute
     */
    private static $executeSql = '';
    /**
     * @var The rawSql|$queryInstance[Query Class]|$bindValues to do the execute operation
     */
    private $rawSql, $queryInstance, $bindValues;
    
    /**
     * Deny public invoke to create QueryRaw Object
     * QueryRaw constructor.
     */
    private function __construct()
    {
    }
    
    /**
     * @return \supjos\database\QueryRaw
     */
    public static function getQueryRaw()
    {
        if ( static::$queryRawInstance === NULL ) {
            static::$queryRawInstance = new static();
        }
        
        return static::$queryRawInstance;
        
    }
    
    /**
     * @param                        $rawSql
     *
     * @param \supjos\database\Query $queryInstance
     *
     * @param                        $bindValues
     *
     * @return \supjos\database\QueryRaw
     */
    public function setRawSql( $rawSql, $queryInstance, $bindValues )
    {
        $this->rawSql = $rawSql;
        $this->queryInstance = $queryInstance;
        $this->bindValues = $bindValues;
        
        return $this;
    }
    
    /**
     * Return one row set of the data
     *
     * @return mixed
     */
    public function queryOne()
    {
        $pdoStatements = $this->queryInstance->execute( $this->rawSql, $this->bindValues );
        $fetchResult =
            $pdoStatements->fetch( $this->queryInstance->fetchRealStyle[ $this->queryInstance->fetchStyle ] );
        $this->flushTempData();
        
        return $fetchResult;
    }
    
    /**
     * After each query or execute, flush the temp-data for the next use
     */
    private function flushTempData()
    {
        $this->rawSql = NULL;
        $this->queryInstance = NULL;
        $this->bindValues = [];
        
        return TRUE;
    }
    
    /**
     * Return all the data in the database' table
     *
     * @return mixed
     */
    public function queryAll()
    {
        $pdoStatements = $this->queryInstance->execute( $this->rawSql, $this->bindValues );
        $fetchResult =
            $pdoStatements->fetchAll( $this->queryInstance->fetchRealStyle[ $this->queryInstance->fetchStyle ] );
        $this->flushTempData();
        
        return $fetchResult;
    }
    
    /**
     * Return the affected rows
     *
     * @return mixed
     */
    public function execute()
    {
        $pdoStatements = $this->queryInstance->execute( $this->rawSql, $this->bindValues );
        $this->flushTempData();
        
        return $pdoStatements->rowCount();
    }
    
    /**
     * Deny clone function
     */
    private function __clone()
    {
        
    }
    
}