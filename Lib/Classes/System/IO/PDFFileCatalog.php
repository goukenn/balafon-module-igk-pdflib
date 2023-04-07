<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileCatalog.php
// @date: 20230407 16:03:29
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFileCatalog extends PDFFileObject{
    
    private $m_pages;
    protected function initialize()
    {
        parent::initialize(); 
        $this->m_dictionary->addName(PDFNames::Type);
        $this->m_dictionary->addName(PDFNames::Catalog);
        $this->m_pages = $this->m_dictionary->addName(PDFNames::Pages); 
    }
    public function render():string{
        return parent::render();
    }
    /**
     * add file pages
     * @param PDFTypeFilePages $page 
     * @return void 
     */
    public function addRefPages(PDFTypeFilePages $page){
        $ref = new PDFFileRefValue($page);
        if ($this->m_pages->value === null){
            $this->m_pages->value = $ref;
        }
        else {
            if (!($this->m_pages->value instanceof PDFTypeFileArrayType)){
                $arr = new PDFTypeFileArrayType;
                $arr->append($this->m_pages->value);
                $this->m_pages->value = $arr;
            }
            $this->m_pages->value->append($ref);
        }  
    }
}