<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;

class PDFHostNode extends PDFNodeBase{
    private $m_callable;
    protected $tagname = PDFConstants::NS_PREFIX."host";
    public function __construct(callable $callable )
    {
        parent::__construct();
        $this->m_callable = $callable;
    }
    public function RenderPDF($pdf, $options=null){
        $fc = $this->m_callable;
        $fc = $fc->bindTo($this);
        $fc($pdf, $options);
    }
}