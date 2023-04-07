<?php
namespace igk\pdflib\System\XMP; 

use igk\pdflib\System\XMP\XMPElementBase;

class XMPDocument extends XMPElementBase{
    protected $tagname = 'x:xmpmeta';

    const NS = "adobe:ns:meta/";
    const XMPTK = "Adobe XMP Core 6.0.0";
    const RDF = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";

    protected function initialize(){
        parent::initialize();
        $this['xmlns:x'] = self::NS;
        $this['x:xmptk'] = self::XMPTK;
    }

}