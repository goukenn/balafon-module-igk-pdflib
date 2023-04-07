<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;

class PDFPageNode extends PDFNodeBase{
    
    protected $tagname = PDFConstants::NS_PREFIX."page-marker";
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag(){
        return false;
    }
    
    public function RenderPDF($pdf, $options=null){
        $pdf->addPage();
    }
}