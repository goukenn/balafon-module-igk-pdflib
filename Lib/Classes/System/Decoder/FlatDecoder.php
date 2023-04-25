<?php
namespace igk\pdflib\System\Decoder;

class FlatDecoder extends PDFDecoderBase{

    public function decode(string $data): string { 
        return gzuncompress($data);
        //return gzdeflate($data);
    }

}