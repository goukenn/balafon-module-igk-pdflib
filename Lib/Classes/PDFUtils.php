<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFUtils.php
// @date: 20220310 10:48:27
// @desc: pdf utility

namespace igk\pdflib;
use IGK\System\Drawing\Color;
use IGK\System\Html\Css\CssParser;

class PDFUtils{

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