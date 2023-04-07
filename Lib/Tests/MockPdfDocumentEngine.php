<?php

namespace igk\pdflib\Tests;

use igk\pdflib\PDFDOC;
use igk\pdflib\PDFMeasureInfo;
use igk\pdflib\PDFRenderer;

class MockPdfDocumentEngine extends PDFRenderer{
    protected $document;
    private $_pdf;

    public function __construct($document)
    {
        $this->document = $document;
        $this->_pdf = new PDFDOC($document); 
    }
   
    public function output($engine=null){
        $s = "data presentation and sample wath else";
        $s = " ";
        return json_encode(
            [
                "string"=>$s,
                "definition"=>$this->_pdf->GetStringWidth($s)]
        );
    }

    /**
     * return array measure info
     * @param mixed $string 
     * @param mixed $width bound width
     * @param mixed $height line height 
     * @return array 
     */
    public function MesureInfo($string, $width, $lineHeight){
        $pos = 0;
        $out = []; 
        $s =  "";
        $x = 0;
        $y = 0;
        array_map(function($g)use(& $s,  & $out){
            switch ($g) {
                case ' ': 
                    if (!empty($s)){
                        $out[$s] = new PDFMeasureInfo( 
                            $this->_pdf->GetStringWidth($s),
                            $s, 
                        );
                    } else {
                        $out[] = "=space";
                    }
                    $s = $g;
                    break;
                case "\n":
                        $out[] = "=ln";
                        break;
                default:
                    # code...
                    $s.= $g;
                    break;
            }
         }
         ,str_split($string)); 
        return $out;
    }
} 