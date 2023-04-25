<?php
namespace igk\pdflib\System\XMP; 

use igk\pdflib\System\XMP\XMPElementBase;

class XMPDocument extends XMPElementBase{
    protected $tagname = 'x:xmpmeta';

    const NS = "adobe:ns:meta/";
    const XMPTK = "Adobe XMP Core 6.0.0";
    const RDF = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
    const XMP = "http://ns.adobe.com/xap/1.0/";
    const DC_NS = "http://purl.org/dc/elements/1.1/";

    protected function initialize(){
        parent::initialize();
        $this['xmlns:x'] = self::NS;
        $this['x:xmptk'] = self::XMPTK;
        $this['xmlns:xmp'] = self::XMP;
        $this['xmlns:dc'] = self::DC_NS;
    }

}