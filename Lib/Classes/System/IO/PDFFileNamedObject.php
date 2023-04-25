<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileNamedOjbect.php
// @date: 20230407 12:55:34
namespace igk\pdflib\System\IO;

use Closure;

///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFileNamedObject{
    var $value;
    var $name;
    public function __construct($name){
        $this->name = $name;
    }

    public function render(){
        $s = $this->value;
        if ($s instanceof Closure){
            $s = $s();
        } else if ($s instanceof PDFFileObject){
            $s = $s->getRef();
        }
        if ($s = trim($s.'')){
            if (strpos('(<{/[', $s[0]) ===false){
                $s = ' '.$s;
            } 
            // else {
            //     $s = ' ----- '.$s;
            // }
        }
        return trim(sprintf('/%s%s', $this->name, $s));
    }
    public function __toString(){
        return $this->render();
    }
}