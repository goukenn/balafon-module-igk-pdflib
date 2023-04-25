<?php

namespace igk\pdflib\System\Decoder;

abstract class PDFDecoderBase{
    public abstract function decode(string $data):string;
}