<?php
namespace igk\pdflib\System\IO\Traits;

trait PDFFileInfoManagementTrait{
    public function setProducer(string $producer){
        $this->m_pdf_info->setProducer($producer);
        return $this;
    }
    public function setCreationDate(string $producer){
        $this->m_pdf_info->setCreationDate($producer);
        return $this;
    }
}