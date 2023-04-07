<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;
use IGKValidator;

/**
 * pdf picture node
 * @package igk\pdflib
 */
class PDFPictureNode extends PDFNodeBase implements IPDFRenderer{
    private $file;
    private $m_link;
    private $m_type;
    protected $tagname = PDFConstants::NS_PREFIX."picture";

    /**
     * get the reflink
     * @param mixed $refLink 
     * @return $this 
     */
    public function setLink($refLink){
        $this->m_link = $refLink;
        return $this;
    }
    public function setType($type = "JPEG"){
        $this->m_type = strtoupper($type);
        return $this;
    }
    public function __construct($file)
    {
        parent::__construct();
        $this->file = $file;
    }

    public function RenderPDF($pdf, $options = null) { 
        if (!IGKValidator::IsUri($this->file)){
            if (!file_exists($this->file))
                return;
        }
        $info = PDFUtils::GetStyleInfo($this, $options->document);
        if ($_y = $this["top"] ?? $info->top){
            $_y = PDFUtils::ParseUnit($_y , $options->document, "h");
        }
        if ($_x = $this["left"] ?? $info->left){
            $_x = PDFUtils::ParseUnit($_x , $options->document, "w");
        }
        if ($_w = $this["width"] ?? $info->width){
            $_w = PDFUtils::ParseUnit($_w , $options->document, "w");
        }
        if ($_h = $this["height"] ?? $info->height){
            $_h = PDFUtils::ParseUnit($_w , $options->document, "h");
        }
        $pdf->Image($this->file, $_x, $_y, $_w, $_h, $this->m_type, $this->m_link);
    }
}