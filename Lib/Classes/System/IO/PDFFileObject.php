<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileOjbect.php
// @date: 20230407 11:30:08
namespace igk\pdflib\System\IO;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * 
 * @package igk\pdflib\System\IO
 */
class PDFFileObject extends PDFFileRefObject
{
    var $numberId = 0;
    var $buildId = 0;
    /**
     * 
     * @var array|PDFFileDictionary
     */
    protected $m_dictionary;
    var $inline;
    public function __construct(){
        $this->initialize();
    }
    protected function initialize(){
        $this->m_dictionary = $this->_createCollection(); 
    }
    protected function _createCollection(){
        return new PDFFileDictionary;
    }
    /**
     * append to dictionary 
     * @param mixed $data 
     * @return mixed 
     */
    protected function append($data)
    {
        $this->m_dictionary->add($data);
        return $data;
    }
    /**
     * 
     * @param string $name 
     * @return PDFFileNamedObject 
     */
    public function addName(string $name){
        $n = new PDFFileNamedObject($name);
        $this->m_dictionary->add($n);
        return $n;
    }
    public function getDictionaryEntry(string $name){
        if ($this->m_dictionary instanceof PDFFileDictionary){
            return $this->m_dictionary->getDictionaryEntry($name);
        }
       return null;
    }
    /**
     * add item to dictionary
     */
    public function add($item){
        $this->m_dictionary->add($item);
        return $item;
    }
    public function render(): string
    {
        $sb = new StringBuilder;
        $sb->appendLine(sprintf("%s %s %s", $this->numberId, $this->buildId, PDFNames::obj));
        if ($this->m_dictionary && ($this->m_dictionary->count()> 0)){
            if (!empty($g = trim($this->m_dictionary->render() ?? ''))){
                $sb->appendLine($g);
            }
        }
        if ($this instanceof IPDFFileStream){
            $sb->appendLine($this->streamDefinition());
        }
        $sb->append(sprintf("%s", PDFNames::endobj));
        return $sb;
    }

    public function getRef(): string
    {
        return sprintf("%s %s %s", $this->numberId, $this->buildId, PDFNames::R);
    }
}
