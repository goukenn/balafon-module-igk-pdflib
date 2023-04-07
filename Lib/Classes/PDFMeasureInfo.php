<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFMeasureInfo.php
// @date: 20220309 16:13:01
// @desc: measure pdf entries info

namespace igk\pdflib;


/**
 * 
 * @package igk\pdflib
 */
class PDFMeasureInfo{
    /**
     * set font size
     * @var int
     */
    var $fontSize;
    /**
     * text to display
     * @var mixed
     */
    var $text;
    
    
    var $fontStyle;
    /**
     * 
     * @var float to contruct
     */
    var $width;

    public function __construct($width, $text, $fontStyle='', $fontSize=12)
    {
        $this->width = $width;
        $this->text  = $text;
        $this->fontStyle = $fontStyle;
        $this->fontSize = $fontSize; 
    }

    public function __toString()
    {
        return json_encode((array)$this);
    }
}