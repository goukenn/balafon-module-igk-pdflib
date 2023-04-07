<?php 
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;

use IGK\System\Html\Dom\HtmlNode;

class PDFInfo{
    /**
     * 
     * @var string
     */
    var $subject;
    /**
     * 
     * @var string
     */
    var $creator;

    /**
     * 
     * @var string
     */
    var $author;
    /**
     * 
     * @var string
     */
    var $title;
    /**
     * is utf8
     * @var mixed
     */
    var $utf8;
    /**
     * 
     * @var string
     */
    var $keywords;
}