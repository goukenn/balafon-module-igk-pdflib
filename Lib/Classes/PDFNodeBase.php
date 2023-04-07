<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;

abstract class PDFNodeBase extends HtmlNode{
    public static function CreateWebNode($node, $attributes=null, $args=null){
        if (class_exists($n = __NAMESPACE__."\\PDF".ucfirst($node)."Node")){            
            $c = new $n(...$args);
            if ($attributes){
                $c->setAttributes($attributes);
            }
            return $c;
        } 
        return parent::CreateWebNode($node, $attributes, $args);
    }
}