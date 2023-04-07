<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlContext;

class PDFCellNode extends PDFNodeBase implements IPDFRenderer{
    private $m_callable;
    protected $tagname = PDFConstants::NS_PREFIX."cell";
    private $ln;
     
    public function getTagName($options=null){
        if ($options->Context == HtmlContext::Html){
            return "td";
        }
        return parent::getTagName(); 
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function setLn(bool $ln){
        $this->ln = $ln;
        return $this;
    }
    public function __construct($width, $height=10, $ln= false)
    {
        parent::__construct();
        $this->setStyle("width:".$width."; height: ".$height); 
        $this->ln = $ln;
    }
    public function getCanRenderTag($options=null){
        if (igk_getv($options, "Context")== \IGK\System\Html\HtmlContext::Html ){
            return true;
        }
        return false;
    }     
    public function RenderPDF($pdf, $options=null){
        $content = $this->getContent();
        $s = "";
        $_w = 0;
        $_h = 0;
        $info = PDFUtils::GetStyleInfo($this, $options->document);
        if (!empty($content)){
            if (is_object($content)){
                $s .= HtmlRenderer::GetValue($content, $options);
            }else{
                if (is_array($content)){
                    $s .= json_encode($content, JSON_UNESCAPED_SLASHES);
                }else 
                    $s .= $content;
            }        
        }
        $_w = $info->width;
        $_h = $info->height; 
        $fill  = false; 
        
        $pdf->pushState();
        PDFRenderer::BindInfo($pdf, $info, $options);       
        $pdf->Cell($_w, $_h, $s, $info->borderStyle, $this->ln, $info->align, $fill);
        $pdf->popState();
    }
}