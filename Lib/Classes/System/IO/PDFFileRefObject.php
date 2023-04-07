<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFileRefObject.php
// @date: 20230407 13:14:44
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
abstract class PDFFileRefObject{
    public abstract function getRef():string;
}