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
    protected function _createCollection(){
        return [];
    }
    public function addName(string $name){
        $n = new PDFFileNamedObject($name);
        $this->m_dictionary[] = $n;
        return $n;
    }
    public function add($item){
        $this->m_dictionary[] = $item;
        return $item;
    }
    public function render(): string
    {
        $sb = new StringBuilder;
        if ($this->m_dictionary) {
            $sb->appendLine('<<');            
            $ch = '';
            $t = 0;
            foreach ($this->m_dictionary as $n) {
                if (!empty($s = $n->render())){
                    $sb->append($ch.$s);
                    $ch = ' ';
                    $t = 1;
                }               
            }
            if ($t){
                $sb->appendLine('');
            }
            $sb->append('>>');
        }
        return $sb;
    }
  
}
