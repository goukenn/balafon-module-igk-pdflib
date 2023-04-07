<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFilePages.php
// @date: 20230407 17:10:25
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFTypeFilePages extends PDFFileObjectType{
    protected $type = PDFNames::Pages;
    var $kids;
    var $count; // 
   
    protected function initialize()
    {
        parent::initialize();
        $this->kids = $this->addName(PDFNames::Kids);
        $this->kids->value = new PDFTypeFileArrayType;
        $this->count = $this->addName(PDFNames::Count);
        $this->count->value = function(){
            if ($this->kids)
                return $this->kids->value->count();
            return '0';
        };
    }
    public function addPage(PDFTypeFilePage $page){
        $page->addName(PDFNames::Parent)->value = $this; 
        // new PDFFileRefValue($this);
        // $this->kids->value->append(new PDFFileRefValue($page));
        $this->kids->value->append($page);
    }
}