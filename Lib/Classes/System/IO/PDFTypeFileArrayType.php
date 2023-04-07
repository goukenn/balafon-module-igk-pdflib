<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileArrayType.php
// @date: 20230407 15:06:32
namespace igk\pdflib\System\IO;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFTypeFileArrayType extends PDFFileObject{
  
    private $m_data = [];
    public function __construct(?array $data=null)
    {
        parent::__construct();
        if (!is_null($data)){
            $this->m_data = $data;
        }
    }
    protected function _createCollection()
    {
        return null;
    }
    public function append($n){
        $this->m_data[] = $n;
    }
    public function getRef():string{
        return $this->render();
    }
    public function render(): string
    {
        $sb = new StringBuilder;
        $sb->append('[');
        if ($this->m_data) {
            $ch = '';
            foreach ($this->m_data as $n) {          
                $s = '';
                if ($n instanceof PDFFileObject){
                    $s = $n->getRef();
                }else {
                    $s = $n.'';
                }
                $sb->append($ch.$s);
                $ch = ' ';
            }
        }
        $sb->append(']');
        return $sb;
    }
    public function count(){
        return count($this->m_data);
    }
    public function __toString()
    {
        return $this->render();
    }
}