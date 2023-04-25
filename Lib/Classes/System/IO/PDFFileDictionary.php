<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileDictionary.php
// @date: 20230407 14:39:27
namespace igk\pdflib\System\IO;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * 
 * @package igk\pdflib\System\IO
 */
class PDFFileDictionary extends PDFFileObject
{ 
    /**
     * inline dictionary
     * @var ?bool
     */
    var $inline;

    protected function _createCollection(){
        return [];
    }
    public function addName(string $name){
        $n = new PDFFileNamedObject($name);
        $this->m_dictionary[$name] = $n;
        return $n;
    }
    public function add( $item){

        if ($item instanceof PDFFileNamedObject)
            $this->m_dictionary[$item->name] = $item;
        else if (($item instanceof PDFFileRefObject) && !($item instanceof PDFFileRefValue))
            $this->m_dictionary[] = new PDFFileRefValue($item);
        else 
             $this->m_dictionary[] = $item;
        return $item;
    }
    /**
     * get dictionary entry
     * @param string $name 
     * @return mixed 
     */
    public function getDictionaryEntry(string $name){
        foreach($this->m_dictionary as $e){
            if ($e->name == $name){
                return $e;
            }
        }
        return null;
    }
    public function render(): string
    {
        $sb = new StringBuilder;           
        if ($this->m_dictionary) {
            // $sb->appendLine();
            $ch = '';
            // $t = 0;
            foreach ($this->m_dictionary as $n) {
                if (!empty($s = $n->render())){
                    $sb->append(trim($ch.$s));
                    // $sb->append( $ch.$s);
                    $ch = ' '; 
                }               
            } 
        }
        $sb->trim();
        return sprintf('<< %s >>', $sb);
    }
    public function getRef(): string
    {
        if ($this->inline){
            return $this->render();
        }
        return parent::getRef();
    }
    public function count(){
        return count($this->m_dictionary);
    }
  
}
