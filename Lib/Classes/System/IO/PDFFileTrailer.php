<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileTrailer.php
// @date: 20230407 11:20:39
namespace igk\pdflib\System\IO;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFileTrailer{
    private $m_data = [];
    private $m_refsize; 
    public function setSize(int $size){
        if (is_null($this->m_refsize)){
            $this->m_refsize = new PDFFileNamedObject(PDFNames::Size);
            $this->m_data[] = $this->m_refsize;
        }
        $this->m_refsize->value = $size;
    }
    public function render():string{
        $v_d = new StringBuilder;
        foreach($this->m_data as $data){
            $v_d->appendLine($data);
        }
        return sprintf(implode(PHP_EOL, ['<<', '%s', '>>']), rtrim($v_d));
    }
    public function add(PDFFileObject $data){
        if (!($data instanceof PDFFileRefValue)){
            $data = new PDFFileRefValue($data);
        }
        $this->m_data[] = $data; 
    }
    public function addRef(string $name, $data){
        $info = new PDFFileNamedObject($name);
        $info->value =new PDFFileRefValue($data);
        $this->m_data[] = $info;
    }
}