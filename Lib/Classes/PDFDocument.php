<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFDocument.php
// @date: 20220309 13:29:45
// @desc: pdf document

namespace igk\pdflib;

use IGK\System\Html\HtmlRenderer;
use IGK\System\Html\Dom\HtmlDocTheme;

/**
 * pdflib document
 * @package igk\pdflib
 */
class PDFDocument extends PDFNodeBase{
    protected $tagname = PDFConstants::NS_PREFIX."document";
    protected $m_footer; 
    protected $m_header; 
    protected $m_defaultFont = "Arial";
    protected $m_defaultFontSize = 12;
    protected $m_unit = "cm";
    protected $m_paperFormat = "A4";
    protected $m_pdftype = "P";
    protected $m_theme;
    protected $m_aliasNbPage;
    protected $m_paperWidth;
    protected $m_paperHeight;
    protected $m_renderOptions;
    private $_pdf; 
    public function getRenderOptions(){
        return $this->m_renderOptions;
    }
    public function getPDFUnit(){
        return $this->m_unit ?? "mm";
    }
    public function getPDFType(){
        return $this->m_pdftype ?? "P";
    }
    ///<summary>id require to identifie the theme</summary>
    public function getId(){
        return "_pdflib:".spl_object_hash($this);
    }
    public function getTheme(){
        return $this->m_theme; 
    }
    public function getDefaultFont(){
        return $this->m_defaultFont;
    }
    public function getDefaultFontSize(){
        return $this->m_defaultFontSize;
    }
    public function getFooter(){
        return $this->m_footer;
    }
    public function getHeader(){
        return $this->m_header;
    }
    public function setAliasNbPages($v){
        $this->m_aliasNbPage = $v;
        return $this;
    }
     /**
     * register theme color
     * @param mixed $colorName 
     * @param mixed $value 
     * @return void 
     */
    public function regColor($colorName, $value){
        $cl = & $this->getTheme()->getCl();
        $cl[$colorName] = $value;        
    }
    public function setPaperSize($width, $height){
        if (is_string($width) && is_string($height)){
            preg_match_all("/([0-9]+)(?P<unit>.+)/", $width, $wtab); 
            preg_match_all("/([0-9]+)(?P<unit>.+)/", $height, $htab); 
            if ($htab["unit"][0] = $wtab["unit"][0]){
                $u = $htab["unit"][0];
                if(in_array($u, ["cm", "mm"])){
                    $this->m_unit = $u; 
                }else{
                    $this->m_unit = "cm";
                }
            }
            $width = $wtab[1][0];
            $height = $htab[1][0];
        } 
        $this->m_paperWidth = $width;
        $this->m_paperHeight = $height;
    }
    public function getPaperSize(){
        if (( $this->m_paperWidth > 0 ) && ($this->m_paperHeight> 0))
            return [$this->m_paperWidth, $this->m_paperHeight];
        return $this->m_paperFormat;
    }
    protected function initialize(){
        parent::initialize();
        $this->m_footer = new PDFFooter();
        $this->m_header = new PDFHeader();
        $this->m_theme = new HtmlDocTheme($this, "pdflib-theme");  

        $this->regColor("black", "#222");
        $this->regColor("red", "#F00");
        $this->regColor("indigo", "#F0F");
    }
    public function getRenderedChilds($options=null){
        $t = parent::getRenderedChilds();
        if (!$options || !isset($options->pdfRendering)){
            array_unshift($t, $this->m_header);
            array_push($t, $this->m_footer);
        }

        return $t;
    }
    /**
     * render output - passing the engine
     * @param mixed $engine 
     * @return void 
     */
    public function output($engine = null){    

        $pdf = new PDFDOC($this); 
        $pdf->AliasNbPages($this->m_aliasNbPage);
        $this->_pdf = $pdf;
        ob_start();
        $this->m_renderOptions = (object)[
            "Engine"=>new PDFRenderer($pdf),
            "document"=>$this
        ];
         
        HtmlRenderer::Render($this, $this->m_renderOptions);
        $c = ob_get_contents();
        ob_end_clean();
        $pdf->output();
    }
    public function getFactor(){
        return $this->_pdf->getFactor();
    }
}