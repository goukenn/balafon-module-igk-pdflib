<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileType.php
// @date: 20230407 16:15:26
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
abstract class PDFFileObjectType extends PDFFileObject{
    protected $type;
    protected function initialize()
    {
        parent::initialize(); 
        $this->m_dictionary->addName(PDFNames::Type); 
        $this->m_dictionary->addName($this->type); 
    }
   
} 