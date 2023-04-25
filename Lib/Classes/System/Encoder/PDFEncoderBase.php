<?php

namespace igk\pdflib\System\Encoder;

abstract class PDFEncoderBase{
    public abstract function encode(string $data):string;
}