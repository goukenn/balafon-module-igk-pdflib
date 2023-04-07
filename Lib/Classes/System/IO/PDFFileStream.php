<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileStream.php
// @date: 20230407 14:41:31
namespace igk\pdflib\System\IO;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * object that content stream definition 
 * @package igk\pdflib\System\IO
 */
class PDFFileStream extends PDFFileObject implements IPDFFileStream
{
    var $value;
    public function streamDefinition(): string{     
        $sb = new StringBuilder;
        $sb->appendLine(PDFNames::stream);
        $sb->appendLine($this->value);
        $sb->append(PDFNames::endstream);
        return $sb;
    }
    /**
     * expose append function 
     * @param mixed $d 
     * @return mixed 
     */
    public function append($d){
        return parent::append($d);
    }
}
