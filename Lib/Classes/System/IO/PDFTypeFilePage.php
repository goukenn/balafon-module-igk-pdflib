<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFTypeFilePage.php
// @date: 20230407 15:55:14
namespace igk\pdflib\System\IO;

use igk\pdflib\System\Drawing\PDFGraphics;

///<summary></summary>
/**
* represent pdf single page
* @package igk\pdflib\System\IO
*/
class PDFTypeFilePage extends PDFFileObjectType{
    protected $type = PDFNames::Page;    
    private $m_resources;
    /**
     * get resources pages
     * @return mixed 
     */
    public function getResources(){
        return $this->m_resources;
    }
    public function getContents(){
        return $this->m_dictionary->getDictionaryEntry(PDFNames::Contents);
    }
    protected function initialize()
    {
        parent::initialize();
        $this->m_resources = $this->addName(PDFNames::Resources);
        $this->m_resources->value = new PDFFileDictionary;
        $this->m_resources->value->inline = true;
    }
 
    /**
     * get pdf graphics to build 
     * @return PDFGraphics 
     */
    public function getGraphics(){
        $g = PDFGraphics::Create($this);
        return $g;
    }

    public function addResource(PDFFileObject $obj){
        if (!$this->m_resources){

        }
        // $dic = new PDFFileDictionary;
        // $dic->addName('Im1')->value = '6 0 R';
        // $dic->inline = true;
        $this->m_resources->value->add($obj); // Name(PDFNames::XObject)->value = $dic;// = $obj;
        // $this->m_resources->value = $obj;
    }
    public function setResource(PDFFileObject $obj){
        if (!$this->m_resources){

        }
        // $dic = new PDFFileDictionary;
        // $dic->addName('Im1')->value = '6 0 R';
        // $dic->inline = true;
        $this->m_resources->value = $obj;
    }
}