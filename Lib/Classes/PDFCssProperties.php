<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PDFCssProperties.php
// @date: 20220310 10:48:58
// @desc: pdf styling properties
namespace igk\pdflib;

class PDFCssProperties{
    var $font;

    var $fontStyle = '';
    var $fontSize = 12;

    var $borderStyle = '';
    var $borderSize = '';
    var $borderColor = '';

    var $fontColor = '#000';
    var $fillColor = '';
    var $drawColor = '';

    // margin 
    var $marginLeft;
    var $marginRight;
    var $marginTop;
    var $marginBottom;

    // position
    var $left;
    var $top;
    var $bottom;
    var $right;

    // line height
    var $lineHeight = 10;

    /**
     * C=center, L|''= left , R=right , J justify
     * @var string
     */
    var $align = '';

    var $width;
    var $height;
}

