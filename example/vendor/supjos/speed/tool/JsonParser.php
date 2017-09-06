<?php
/**
 * The JsonParser.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-21 04:50:29
 *
 * @since 0.0.2
 */

namespace supjos\tool;

use Object;
use supjos\exception\ParameterNotMatch;
use supjos\exception\ParseException;
use function is_object;
use function property_exists;

class JsonParser extends Object
{
    
    /**
     * @var string $jsonString The JSON string you want to parse
     */
    private $jsonString;
    
    /**
     * JsonParser constructor.
     *
     * @param string $jsonString Which JSON you want to parse
     */
    public function __construct( $jsonString = '' )
    {
        $this->jsonString = $jsonString;
    }
    
    /**
     * @param string $jsonString Setting the JSON string waiting for parsing
     *
     * @throws \supjos\exception\ParameterNotMatch
     */
    public function setJsonString( $jsonString )
    {
        if ( empty( $jsonString ) ) {
            throw new ParameterNotMatch( 'Parameter $jsonString must be JSON string.' );
        }
        $this->jsonString = $jsonString;
    }
    
    /**
     * @param string $propertyName The JSON property which you want to get value from
     *
     * @return mixed|null Return NULL means the property not exist in the given JSON
     */
    public function getJsonProperty( $propertyName )
    {
        $result = $this->getJsonResult();
        if ( is_object( $result ) ) {
            if ( property_exists( $result, $propertyName ) ) {
                return $result->$propertyName;
            }
            
            return NULL;
        } else if ( is_array( $result ) ) {
            if ( isset( $result[ $propertyName ] ) ) {
                return $result[ $propertyName ];
            }
            
            return NULL;
        }
    }
    
    /**
     * @param bool $returnArray if TRUE return Array, otherwise return Object
     *
     * @return mixed|array|object Determined by the parameter $returnArray [[ TRUE or FALSE]]
     * @throws \supjos\exception\ParseException
     */
    public function getJsonResult( $returnArray = TRUE )
    {
        $jsonResult = json_decode( $this->jsonString, $returnArray );
        if ( $jsonResult === NULL ) {
            throw new ParseException( "The JSON String were invalid." );
        }
        
        return $jsonResult;
    }
}