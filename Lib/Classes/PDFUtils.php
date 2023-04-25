<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFUtils.php
// @date: 20220310 10:48:27
// @desc: pdf utility

namespace igk\pdflib;

use IGK\Helper\StringUtility;
use IGK\System\Drawing\Color;
use IGK\System\Html\Css\CssParser;

class PDFUtils{

    private static function _ReadArray(string $str, &$pos){
        $len  = strlen($str);
        $depth = 1;
        $v = '';
        while ($pos < $len) {
            $ch = $str[$pos];
            switch($ch){
                case '[':
                    $depth++;
                    break;
                case ']':
                    $depth--;
                    if ($depth==0){
                        return $v;
                    }
                    break;

            }
            $v.=$ch;
            $pos++;
        }
        return $v;
    }
    /**
     * 
     */
    public static function ReadDictionary(string $str){
        $rdic = [];
        $n = 0;
        $v = '';
        $pos = 0; 
        $depth = 0;
        $buffer = [];
       

        while ($pos < strlen($str)) {
            $ch = $str[$pos];
            if (($ch!='0') && empty(trim($ch)))
                $ch = ' ';

            $pos++;
            switch ($v) {
                case '<<':
                    $mode = 1;
                    $depth++;
                    $v = '';
                    if ($n) {
                        // push direction 
                        $ctab = [];
                        $rdic[$n] = $ctab;
                        array_unshift($buffer, $rdic);
                        $rdic = $ctab;
                        $n = '';
                    }
    
    
                    break;
                case '>>':
                    $depth--;
                    $v = '';
                    if ($depth == 0) {
                        $mode = 0;
                        $end = true;
                    }
                    $qdic = array_shift($buffer);
                    if ($qdic) {
                        $key = array_key_last($qdic);
                        $qdic[$key] = $rdic;
                        $rdic = $qdic;
                    }
                    break;
                case '/':
                    // if (!empty($rv = trim($v))){
                    //     $rdic[$n] = $rv;
                    // }
                    //read name
                    $n = $ch . StringUtility::ReadIdentifier($str, $pos); // _read_name()
                    $v = '';
                    $ch = '';
                    break;
                default:
                    switch ($ch) {
                        case '(':
                            $pos--;
                            $rv = igk_str_read_brank($str, $pos, ')', '(');
                            if (!empty($n)){
                                $rdic[$n] = $rv;
                                $ch = '';
                                $v = '';
                                $n = '';
                            }
                            $pos++;
                            
                            break;
                        case '[':    
                            $g = self::_ReadArray($str, $pos);                        
                            $g = preg_replace("/\s+/", " ",  trim($g));
                            
                            $g = array_map(function($a){ 
                                return $a;
                            }, array_filter(explode (' ', $g)));
                            if ($n){
                                $rdic[$n] = $g;
                                $v ="";
                                $ch='';
                                $n = '';
                            }
                            $pos++;
                            break;
                        default:
    
                            if (($v != '<') && ($v != '>')) {
                                //'/ <>[]'
                                if (strpos('/<>[]', $ch) !== false) {
                                    if (!empty($rv = trim($v))) {
                                        $rdic[$n] = is_numeric($rv) ? floatval($rv) : $rv;
                                        $v = '';
                                        $n = '';
                                    } else {
                                        $v = $rv;
                                        if ($ch == '/') {
                                            if (!empty($n)) {
                                                $rdic[$n] = true;
                                            }
                                        }
                                    }
                                }
                            }
                    }
                    break;
            }
            $v .= $ch;
        }
        if (!empty($n)) {
            $prv = true;
            if (!empty($rv = trim($v))) {
                $prv = is_numeric($rv) ? floatval($rv) : $rv;
            }
            $rdic[$n] = $prv;
        }
        return $rdic;
    }


    public static function GetStyleInfo($node, $document){
        $prop = new PDFCssProperties();
        if ($css = $node["style"]){
            $g = CssParser::Parse($css.'');
            $prop->fontColor = $g["color"] ?? self::_inherit($node, "color", $document);
            $prop->fillColor = $g["background-color"] ?? self::_inherit($node, "background-color", $document);
            $prop->drawColor = $g["fill"] ?? self::_inherit($node, "fill", $document);
            $prop->fontSize =  PDFUtils::ParseUnit($g["font-size"] ?? self::_inherit($node, "font-size", $document),$document);
            $margin = $g->margin();
            $padding = $g->padding();
            $position = $g->position();
            $prop->align = implode("", [
                //igk_getv(["left"=>"", "right"=>"R", "center"=>"C", "justify"=>"J"], $g["text-align"] ?? '', ''),
                igk_getv(["", "top"=>"T", "bottom"=>"B"], $g["vertical-align"] ?? '', '')
            ]);
            $prop->lineHeight = PDFUtils::ParseUnit($g["line-height"] ?? $prop->lineHeight, $document);
            $prop->top =  PDFUtils::ParseUnit($position[0], $document);
            $prop->right = PDFUtils::ParseUnit($position[1], $document);
            $prop->bottom = PDFUtils::ParseUnit($position[2], $document);
            $prop->left = PDFUtils::ParseUnit($position[3], $document);
            $prop->width = PDFUtils::ParseUnit($g["width"], $document, 'w');
            $prop->height = PDFUtils::ParseUnit($g["height"], $document, 'h');
            $border = $g->border();
            $prop->borderStyle = implode("", array_filter([
                isset($border->left) ? "L" : null,
                isset($border->right )? "R" : null,
                isset($border->top )? "T" : null,
                isset($border->bottom )? "B" : null,
            ]));
            $prop->borderSize = PDFUtils::ParseUnit($g["border-width"], $document);
            $prop->borderColor = $g["border-color"] ?? self::_inherit($node, "border-color", $document);
        }
        return $prop;
    }
    private static function _inherit($node, $prop, $document){
        $i = 0;
        $q = $node;
        while($q = $q->getParentNode()){
           //igk_wln_e("kd".$q, $q->getParentNode());
            if ($css = $q["style"]){
                $g = CssParser::Parse($css.'');
                if ($c = $g[$prop]){
                    return $c;
                }
            }
            $i++;
        }
    }
    public static function GetColor($document, $color){
        $r = $g = $b = 0;
        $s = $color;
        $theme = $document->theme;
        $tcl = $theme ? $theme->cl : [];
        if (igk_css_is_webknowncolor($s, $tcl)) {
            $s = igk_css_get_color_value($s, $tcl);
        } 
        $cl = Color::FromString($s);
        $r = $cl->getR();
        $g = $cl->getG();
        $b = $cl->getB();  
        return [$r, $g, $b];
    }

    public static function ParseUnit($value, $document, $type='w'){
        // $value = "10px";
        if ($value === null)
            return null;
        if (preg_match_all("/^(?P<value>[\-0-9\.]+)(?P<type>(px|pt|cm|mm|%))?$/i", trim($value), $tab)){
            $v = $tab["value"][0];
            $t = $tab["type"][0];
            $f = 1.0;
            switch($t){
                case '%':
                    break;
                case 'cm':
                    $f = 25.4 / $document->getFactor();
                    break;
                case 'px':
                    $f = 0.75;
                    break;
                case 'mm':
                    $f = 2.54 / $document->getFactor();
                    break;
                case 'pt':
                    $f = 1.0; // $document->getFactor();
                    break;
            }           
            if (is_numeric($v)){
                return floatval($v) * $f;
            }
        }
        if ($value=='auto') {
            return null;
        }
        return 0;
    }
}