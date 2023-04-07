<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFDocument.php
// @date: 20220309 13:29:45
// @desc: pdf document

namespace igk\pdflib;
use FPDF;


if (!class_exists("FPDF")){
    die("FPDF library required");
}

/**
 * 
 * @package igk\pdflib
 */
class PDFDOC extends FPDF{
    protected $document;
    protected $_states = [];
    /**
     * special measure data
     * @var array
     */
    protected $measures = []; 
    protected $_font;
    public function getDocument(){
        return $this->document;
    }
    public function __construct(PDFDocument $document){
        $this->document = $document;
        parent::__construct();//$document->getPDFType(), $document->getPDFUnit(), $document->getPaperSize());
        $this->SetFont( 
            $document->getDefaultFont() ?? "Arial", "", 
            $document->getDefaultFontSize()
        );
        $this->SetFont( $document->getDefaultFont() ?? "Arial");
        $space = ' ';
        $this->measures[$this->_font] = [
            $space=> new PDFMeasureInfo(
                $this->GetStringWidth($space), $space 
                )]; 

    }
    public function SetFont($font, $style='', $size=null){
        parent::SetFont($font, $style, $size);
        if (!empty($ft))
         $this->_font = $ft;
    }
    public function getFont(){
        return $this->_font;
    }

    public function setColor($color){
        
        $this->setTextColor(...PDFUtils::GetColor($this->document, $color)); 
    }
 
    /**
     * get factor
     * @return mixed 
     */
    public function getFactor(){
        return $this->k;
    }


    public function header(){
        $h = $this->document->getHeader();
        $w = $this->getPageWidth();
       //  $this->Cell($w, $h, $h->render());
        $x = $this->getX();
        $y = $this->getY();
        PDFRenderer::RenderNode($h, $this,  $this->document->getRenderOptions());
        $this->SetXY($x,$y);
    }
    public function footer(){
        $footer = $this->document->getFooter();
        $w = $this->getPageWidth();
        $x = $this->getX();
        $y = $this->getY();
        $option = null;   
        PDFRenderer::RenderNode($footer, $this,  $this->document->getRenderOptions());
        $this->SetXY($x,$y);
            
    }
    public function pushState(){
        $this->_states[] = [
            $this->DrawColor,
            $this->FillColor,
            $this->TextColor,
            $this->FontSizePt,
        ];
    }
    public function popState(){
        if ($g = array_pop($this->_states)){
            $this->DrawColor = $g[0];
            $this->FillColor = $g[1];
            $this->TextColor = $g[2]; 
            $this->SetFontSize($g[3]);
        }
    }
    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
    {
        $txt = $this->_treat_text($txt);
        parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
     
    private function _treat_text($txt){
        $txt = str_replace("%current_page%", $this->PageNo(), $txt);
    
    
        return $txt;
    }
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $text = $this->_treat_text($txt);
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    function _putpage($n){
        $cpage = $this->page;
        if ($this->AliasNbPages){
            $this->pages[$n] = str_replace($this->AliasNbPages,str_pad($this->page, strlen($this->AliasNbPages) ,' '),$this->pages[$n]);
        }
        $this->page = $cpage;
        parent::_putpage($n);
    }
}