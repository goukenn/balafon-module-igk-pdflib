<?php

// @author: C.A.D. BONDJE DOUE
// @filename: XMPElementBase.php
// @date: 20230407 19:35:09
// @desc:
namespace igk\pdflib\System\XMP; 

use IGK\System\Html\XML\XmlNode;

abstract class XMPElementBase extends XmlNode{
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }
    protected function initialize(){
        
    }
}
