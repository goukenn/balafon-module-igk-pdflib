<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileRefValue.php
// @date: 20230407 13:35:47
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFileRefValue{
    var $value;
    public function __toString()
    {
        return $this->value->getRef();
    }
    public function __construct(PDFFileObject $obj)
    {
        $this->value = $obj;
    }
    public function render(){
        return $this->value->getRef();
    }
}