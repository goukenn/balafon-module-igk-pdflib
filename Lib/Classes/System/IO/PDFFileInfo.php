<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileInfo.php
// @date: 20230407 13:27:33
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFileInfo extends PDFFileObject{
    private $m_producer;
    private $m_create_date;
    private $m_author;
    private $m_subject;
    private $m_title;
    private $m_keywords;
    private $m_creator;
    private $m_modDate;
    private $m_trapped; // 'True|False|Unknown
   
    public function setProducer(string $value){
        $p = $this->m_producer ?? $this->append($this->m_producer = new PDFFileNamedObject(PDFNames::Producer));
        $p->value = $value;
        return $this;
    }   
    public function setCreationDate(string $value){
        $p = $this->m_create_date ?? $this->append($this->m_create_date = new PDFFileNamedObject(PDFNames::CreationDate));
        $p->value = $value;
        return $this;
    }   
    public function setAuthor(string $value){
        $p = $this->m_author ?? $this->append($this->m_author = new PDFFileNamedObject(PDFNames::Author));
        $p->value = $value;
        return $this;
    }   
    public function setCreator(string $value){
        $p = $this->m_creator ?? $this->append($this->m_creator = new PDFFileNamedObject(PDFNames::Creator));
        $p->value = $value;
        return $this;
    }   
    public function setKeywords(string $value){
        $p = $this->m_keywords ?? $this->append($this->m_keywords = new PDFFileNamedObject(PDFNames::Keywords));
        $p->value = $value;
        return $this;
    }   
    /**
     * Note: deprecated in pdf-2.0 use metadata stream to set information 
     */
    public function setSubject(string $value){
        $p = $this->m_subject ?? $this->append($this->m_subject = new PDFFileNamedObject(PDFNames::Subject));
        $p->value = $value;
        return $this;
    }   
    public function setTitle(string $value){
        $p = $this->m_title ?? $this->append($this->m_title = new PDFFileNamedObject(PDFNames::Title));
        $p->value = $value;
        return $this;
    } 
    public function setTrapped(string $value){
        $p = $this->m_trapped ?? $this->append($this->m_trapped = new PDFFileNamedObject(PDFNames::Title));
        $p->value = $value;
        return $this;
    } 
    public function setModDate(string $value){
        $p = $this->m_modDate ?? $this->append($this->m_modDate = new PDFFileNamedObject(PDFNames::Title));
        $p->value = $value;
        return $this;
    } 
    public function __get($n){
        $n = 'm_'.$n;
        if (property_exists($this, $n)){
            return $this->$n->value;
        }
        return null;
    }
}