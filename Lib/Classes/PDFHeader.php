<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFDocument.php
// @date: 20220309 13:29:45
// @desc: pdf document

namespace igk\pdflib;


class PDFHeader extends PDFNodeBase{
    protected $tagname = PDFConstants::NS_PREFIX."header";
    public function __AcceptRender($options = null):bool{
        return count($this->childs)>0;
    }
}