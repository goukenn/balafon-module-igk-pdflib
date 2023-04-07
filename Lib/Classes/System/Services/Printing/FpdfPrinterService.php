<?php

namespace igk\pdflib\System\Services\Printing;

use FPDF;
use IGK\IService;
use IGK\System\IO\Printer\IPrinterService;

/**
 * printer service data
 * @package com\igkdev\app\llvGStock\Printing
 * @method void setFillColor($r)
 * @method void setTextColor($r)
 * @method void setDrawColor($r)
 * @method void setFont(string $name, string $info='', int $size=12)
 */
class FpdfPrinterService implements IService, IPrinterService{
    var $pdf;
    var $size = 'A4';
    var $view = 'P';
    var $dimension = 'mm';

    public function __construct(){
        $this->initialize();
    }
    public function resetDevice(){
        $this->initialize();
    }
    protected function initialize(){
        $_size = strpos($this->size, 'x') === false ? $this->size :  explode('x', $this->size);
        $this->pdf = new FPDF($this->view, $this->dimension, $_size); 
        $this->pdf->AddPage();
        $this->pdf->SetFont("Helvetica", '', 11);
        $this->pdf->SetFontSize(10); 
        $this->size = $_size;
    }

    /**
     * write text 
     * @param string $text 
     * @param float $h 
     * @return void 
     * @throws Exception 
     */
    public function write(string $text, float $h) {
        $this->pdf->Write($h, utf8_encode($text));
    }

    public function setFontStyle($style) {
        $this->pdf->SetFont("", $style);
    }
    public function text($text, $x=0, $y=0) { 
        $this->pdf->Text($x, $y, $text);
    }

    public function rect($x, $y, $w, $h) { }

    public function printPdf() { 
        $this->pdf->output(); 
    }

    public function init(): bool {
        return true;
    }
    public function cell(string $text, $x, $y, $w, $h=0, $options=null){
        $align = ($options ? igk_getv($options, "align") : null) ?? 'L';
        $fill = ($options ? igk_getv($options, "fill") : false);
        $border = ($options ? igk_getv($options, "border", 1) : 0);
        $ln = ($options ? igk_getv($options, "ln", 1) : 0);
        $url = ($options ? igk_getv($options, "url") : null);
        $this->pdf->SetXY($x, $y);
        $this->pdf->Cell($w, $h, utf8_decode($text), $border, $ln, $align, $fill, $url); 
    }
    /**
     * print paragraph
     * @param string $text 
     * @param mixed $w with
     * @param mixed $h line height 
     * @return void 
     * @throws Exception 
     */
    public function p(string $text, $w, $h){
        $this->pdf->MultiCell($w, $h, $text);
    }
    public function __call($name, $arguments){
        return $this->pdf->$name(...$arguments);
    }

    public function addPage()
    {
        $this->pdf->AddPage(); 
    } 
}