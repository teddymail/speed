<?php

/**
 * This is the supjos's Speed framework components named: Excel
 * License : MIT
 * Copyright (c) 2017-2020 supjos.cn All Rights Reserved.
 *
 * @author Josin <774542602@qq.com | www.supjos.cn>
 */

namespace supjos\tool;

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel' .
              DIRECTORY_SEPARATOR . 'Writer' . DIRECTORY_SEPARATOR . 'Excel2007.php' );

class Excel
{
    
    /**
     * @var \PHPExcel The excel object to the Excel
     */
    private static $_excelObject = NULL;
    
    /**
     * @var \supjos\tool\Excel The object of the Excel class
     */
    private static $instance     = NULL;
    private        $_author      = 'Josin', $_modifier = 'Josin';
    private        $_title, $_boldTitle, $_rowHeight;
    private        $_columnWidth = '20.00';
    private        $_showHead    = TRUE;
    private        $fullSize     = 0;
    private        $_borderBold  = FALSE;
    /**
     * @var type
     */
    private $_showTitle = TRUE;
    /**
     * @var string The Excel header color
     */
    private $_headColor = FALSE;
    /**
     * @var array The header of the Excel
     */
    private $_header = [];
    /**
     * @var array The Excel body
     */
    private $_body = [];
    /**
     * @var boolean The body's cell alignment, default center
     */
    private $_cellCenter = TRUE;
    
    /**
     * Deny to construct the Excel Object
     */
    private function __construct()
    {
        if ( is_null( self::$_excelObject ) ) {
            self::$_excelObject = new \PHPExcel();
            
            return self::$_excelObject;
        } else {
            return self::$_excelObject;
        }
    }
    
    /**
     * @return \supjos\tool\Excel excelObject of the instance of the PHPExcel
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new static();
            
            return self::$instance;
        } else {
            return self::$instance;
        }
    }
    
    /**
     * @return string The Excel export class version code
     */
    public static function getVersion()
    {
        return '2.2.0';
    }
    
    /**
     * @param string $author The author of the Excel
     * @param string $modifier The modifier of the Excel
     */
    public function setInfo( $author, $modifier )
    {
        $this->_author = empty( $author ) ? 'Josin' : $author;
        $this->_modifier = empty( $modifier ) ? 'Josin' : $modifier;
    }
    
    /**
     * @param string       $title The title of the title
     * @param boolean      $boldTitle Render a bold title or not
     * @param float|string $rowHeight Each row height of the excel, default 20.00
     */
    public function setTitleInfo( $title, $boldTitle, $rowHeight = '20.00' )
    {
        $this->_title = empty( $title ) ? 'JosinExcel表格' : $title;
        $this->_boldTitle = is_bool( $boldTitle ) ? $boldTitle : TRUE;
        $this->_rowHeight = is_double( $rowHeight ) ? $rowHeight : '20.00';
    }
    
    /**
     * @param double $columnWidth The excel's column width, if not set. The default value is '20.00'
     */
    public function setColumnWidth( $columnWidth )
    {
        $this->_columnWidth = is_double( $columnWidth ) ? $columnWidth : '20.00';
    }
    
    /**
     * @param boolean $headDisplay Setting whether to rendering the column of the excel
     */
    public function setHeadDisplay( $headDisplay )
    {
        $this->_showHead = is_bool( $headDisplay ) ? $headDisplay : TRUE;
    }
    
    /**
     * @param boolean $border Setting the excel's border style or not, default is false
     */
    public function setBorder( $border )
    {
        $this->_borderBold = is_bool( $border ) ? $border : FALSE;
    }
    
    /**
     * @param string $fileName The excel filename which you want to name the excel file
     */
    public function exportFile( $fileName = 'JosinExcel' )
    {
        $this->renderInfo();
        $this->renderTitle();
        $this->renderHeader();
        $this->renderBody();
        $this->renderBorder();
        ob_end_clean();
        header( 'Content-Type: application/vnd.ms-excel' );
        header( 'Content-Disposition: attachment;filename="' . $fileName . '.xlsx"' );
        header( 'Cache-Control: max-age=0' );
        header( 'Cache-Control: max-age=1' );
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: cache, must-revalidate' );
        header( 'Pragma: public' );
        $objWriter = new \PHPExcel_Writer_Excel2007( self::$_excelObject );
        $objWriter->save( 'php://output' );
    }
    
    /**
     * @internal param string $author Rendering the
     * @internal param string $modifier
     */
    private function renderInfo()
    {
        self::$_excelObject->getProperties()
                           ->setCreated( $this->_author )
                           ->setModified( $this->_modifier );
    }
    
    /**
     * Render the title of the excel
     */
    private function renderTitle()
    {
        $highestColumn = 1;
        if ( empty( $this->_header ) && empty( $this->_body ) ) {
            throw new Exception( "Can not determine the column to occupy!" );
        } else {
            $highestColumn = $this->getCompatibleArray()[ $this->getDataSize() - 1 ];
        }
        self::$_excelObject->getActiveSheet()
                           ->mergeCells( 'A1:' . $highestColumn . '1' );
        if ( $this->_showTitle ) {
            self::$_excelObject->getActiveSheet()
                               ->setCellValue( 'A1', $this->_title );
            self::$_excelObject->getActiveSheet()
                               ->getStyle( 'A1' )
                               ->getFont()
                               ->setBold( $this->_boldTitle );
            self::$_excelObject->getActiveSheet()
                               ->getStyle( 'A1' )
                               ->getAlignment()
                               ->setVertical( \PHPExcel_Style_Alignment::VERTICAL_CENTER )
                               ->setHorizontal( \PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            self::$_excelObject->getActiveSheet()
                               ->getRowDimension( '1' )
                               ->setRowHeight( $this->_rowHeight );
        }
    }
    
    /**
     * @return array Get a full compatible array of the given data to pull
     */
    private function getCompatibleArray()
    {
        if ( !empty( $this->_header ) ) {
            $this->fullSize = $this->checkSize( $this->_header, range( 'A', 'Z' ) );
        } elseif ( !empty( $this->_body ) ) {
            $this->fullSize = $this->checkSize( $this->_body[ 0 ], range( 'A', 'Z' ) );
        }
        
        return $this->fullSize;
    }
    
    /**
     * @param array   $origin_data
     * @param array   $now_data
     * @param integer $index
     *
     * @return The result data
     */
    private function checkSize( $origin_data, $now_data, $index = 0 )
    {
        $tmpFirst = $now_data;
        while ( 1 ) {
            if ( count( $origin_data ) > count( $tmpFirst ) ) {
                $result = $this->extendRowsInExcel( $tmpFirst, $index );
                $tmpFirst = $result[ 'result' ];
                $index = $result[ 'begin_index' ];
            } else {
                break;
            }
        }
        
        return $tmpFirst;
    }
    
    /**
     * To extend the excel row with 26 rows
     *
     * @param number    $excel_row The start extend row with default value 0
     * @param array|int $begin_index_row The excel row in the system
     *
     * @return int The next begin_index_row The system must save it for the next need
     * @author Josin
     */
    private function extendRowsInExcel( $excel_row, $begin_index_row = 0 )
    {
        if ( empty( $excel_row ) ) {
            $excel_row = range( 'A', 'Z' );
            
            return [
                'result'      => $excel_row,
                'begin_index' => ++$begin_index_row
            ];
        }
        $tmp = range( 'A', 'Z' );
        foreach ( $tmp as $k => $v ) {
            $tmp[ $k ] = $excel_row[ $begin_index_row ] . $v;
        }
        $result_array = array_merge( $excel_row, $tmp );
        
        return [
            'result'      => $result_array,
            'begin_index' => ++$begin_index_row
        ];
    }
    
    /**
     * @return int Get the size of the given data
     */
    private function getDataSize()
    {
        if ( !empty( $this->_header ) ) {
            return count( $this->_header );
        } elseif ( !empty( $this->_body ) ) {
            return count( $this->_body );
        }
    }
    
    /**
     * Render the Excel header of the file, Using the given data or attribute from user<br>
     *
     * @internal param dobule $columnWidth The column width of the excel
     */
    private function renderHeader()
    {
        if ( $this->_showTitle ) {
            $index = 2;
        } else {
            $index = 1;
        }
        if ( $this->_showHead ) {
            $defaultExcel = $this->getCompatibleArray();
            foreach ( $this->_header as $k => $v ) {
                if ( $this->_headColor !== FALSE ) {
                    self::$_excelObject->getActiveSheet()
                                       ->getStyle( $defaultExcel[ $k ] . $index )
                                       ->getFill()
                                       ->setFillType( \PHPExcel_Style_Fill::FILL_SOLID )
                                       ->getStartColor()
                                       ->setRGB( $this->_headColor );
                }
                self::$_excelObject->getActiveSheet()
                                   ->setCellValueExplicit( $defaultExcel[ $k ] . $index, $this->_header[ $k ] );
                self::$_excelObject->getActiveSheet()
                                   ->getStyle( $defaultExcel[ $k ] . $index )
                                   ->getFont()
                                   ->setBold( TRUE );
                self::$_excelObject->getActiveSheet()
                                   ->getStyle( $defaultExcel[ $k ] . $index )
                                   ->getAlignment()
                                   ->setHorizontal( \PHPExcel_Style_Alignment::HORIZONTAL_CENTER )
                                   ->setVertical( \PHPExcel_Style_Alignment::VERTICAL_CENTER );
                self::$_excelObject->getActiveSheet()
                                   ->getStyle( $defaultExcel[ $k ] )
                                   ->getNumberFormat()
                                   ->setFormatCode( \PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                self::$_excelObject->getActiveSheet()
                                   ->getColumnDimension( $defaultExcel[ $k ] )
                                   ->setWidth( $this->_columnWidth );
            }
        }
    }
    
    /**
     * Render the Body of the excel from the given data
     */
    private function renderBody()
    {
        $defaultExcel = $this->getCompatibleArray();
        if ( $this->_showTitle && $this->_showHead ) {
            $bodyStart = 3;
        } elseif ( $this->_showTitle && !$this->_showHead ) {
            $bodyStart = 2;
        } else {
            $bodyStart = 1;
        }
        foreach ( $this->_body as $v ) {
            $tmpi = 0;
            foreach ( $v as $vv ) {
                self::$_excelObject->getActiveSheet()
                                   ->setCellValueExplicit( $defaultExcel[ $tmpi ] . $bodyStart, $vv );
                if ( $this->_cellCenter ) {
                    self::$_excelObject->getActiveSheet()
                                       ->getStyle( $defaultExcel[ $tmpi ] . $bodyStart )
                                       ->getAlignment()
                                       ->setHorizontal( \PHPExcel_Style_Alignment::HORIZONTAL_CENTER )
                                       ->setVertical( \PHPExcel_Style_Alignment::VERTICAL_CENTER );
                }
                $tmpi++;
            }
            $bodyStart++;
        }
    }
    
    /**
     * @throws \PHPExcel_Exception
     * @internal param bool $renderBorder Render the excel border or not, Default is true
     */
    private function renderBorder()
    {
        if ( $this->_borderBold ) {
            self::$_excelObject->getActiveSheet()
                               ->getStyle( 'A1:' . self::$_excelObject->getActiveSheet()
                                                                      ->getHighestColumn() .
                                           self::$_excelObject->getActiveSheet()
                                                              ->getHighestRow() )
                               ->getBorders()
                               ->getAllBorders()
                               ->setBorderStyle( \PHPExcel_Style_Border::BORDER_THIN );
        }
    }
    
    /**
     * @param boolean $show Setting whether to render the title of the Excel or not, default is true
     */
    public function setTitleDisplay( $show )
    {
        if ( is_bool( $show ) ) {
            $this->_showTitle = $show;
        }
    }
    
    /**
     * @param string $color The Hexadecimal color string which to render the Excel header
     */
    public function setHeadColor( $color )
    {
        $this->_headColor = $color;
    }
    
    /**
     * @param array $header Setting the header of the Excel header
     */
    public function setHeader( $header )
    {
        if ( is_array( $header ) ) {
            $this->_header = $header;
        }
    }
    
    /**
     * @param array   $body Setting the excel body data
     * @param boolean $centerCell Setting the excel's body cell's alignment, default center each cell of the excel
     */
    public function setBody( $body, $centerCell = TRUE )
    {
        if ( is_array( $body ) ) {
            $this->_body = $body;
        }
        if ( is_bool( $centerCell ) ) {
            $this->_cellCenter = $centerCell;
        }
    }
    
    /**
     * Deny to clone the Excel object
     */
    private function __clone()
    {
        
    }
    
}
