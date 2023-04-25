<?php

namespace igk\pdflib\System\Encoder;

/**
 * use to encode with flat compression
 * @package igk\pdflib\System\Encoder
 */
class FlatEncoder extends PDFEncoderBase{
    /**
     * encode data
     * @param string $data 
     * @return string 
     */
    public function encode(string $data):string{
        return gzcompress($data);
    }
}