<?php
/**
 * The QueryInterface.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-8-18 19:02:29
 *
 * ````All Speed Query Class must implement the following function````
 *
 * @since 0.0.2
 */

namespace supjos\database;

interface QueryInterface
{
    
    /**
     * @param array|string $selectFields The fields which you want to search, or pass ```*``` to search all fields
     *
     * @return Query
     */
    function select( $selectFields );
    
    /**
     * To generate a DISTINCT SELECT QUERY STATEMENT
     *
     * @return Query
     */
    function distinct();
    
    /**
     * @param string $fromTable The table which you want to CURD
     *
     * @return Query
     */
    function from( $fromTable );
    
    /**
     * @param array|mixed|string $whereCondition The where condition which equal to the SQL where statements
     *
     * @return Query
     */
    function where( $whereCondition );
    
    /**
     * @param array|mixed|string $groupBy The SQL GROUP BY fields
     *
     * @return Query
     */
    function groupBy( $groupBy );
    
    /**
     * @param array|string|mixed $having The having where condition
     *
     * @return Query
     */
    function having( $having );
    
    /**
     * @param array|mixed|string $orderBy The ORDER BY col-expression
     *
     * @return Query
     */
    function orderBy( $orderBy );
    
    /**
     * @param string $limit The LIMIT expression only can be string, not array
     *
     * @return Query
     */
    function limit( $limit );
    
    /**
     * Return one row of the data from the database
     *
     * @return mixed One row result of the data
     */
    function one();
    
    /**
     * Return the data exists in database
     *
     * @return mixed All the data sets will fetched
     */
    function all();
    
    /**
     * Return the status of the execute status
     *
     * @param $prepareSql The SQL you want to prepare
     *
     * @param $bindValues The value which you want to bind to the SQL
     *
     * @return bool True success, otherwise false
     */
    function execute( $prepareSql, $bindValues );
    
    /**
     * @param $updateSql  The Update Sql which will be participate in the prepare step
     * @param $bindValues The bindValue which will be bind to the Sql
     *
     * @return int The rowCount which affected by the operation
     */
    function update( $updateSql, $bindValues );
    
    /**
     * @param $deleteSql  The delete Sql
     * @param $bindValues The delete operation bind-values
     *
     * @return int The rowCount affected by the operation
     */
    function delete( $deleteSql, $bindValues );
    
    /**
     * @param string $insertTable The Table which you want insert data into
     * @param array  $insertFields The data fields
     * @param array  $insertArrays The corresponding value with the data fields
     *
     * @return mixed
     */
    function insert( $insertTable, $insertFields = [], $insertArrays = [] );
}