<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PDFRenderer.php
// @date: 20220309 15:30:49
// @desc: base renderer engine

namespace igk\pdflib;

class PDFRenderer
{
    private $_pdf;
    public function __construct($pdf)
    {
        $this->_pdf = $pdf;
        $this->_pdf->addPage();
    }

    public static function BindInfo($pdf, $info, $options){
        if ($info->fillColor){
            $pdf->SetFillColor( ...PDFUtils::GetColor($options->document, $info->fillColor));
            $fill = true;
        } 
        if ($info->fontColor){
            $pdf->SetTextColor( ...PDFUtils::GetColor($options->document, $info->fontColor));            
        } 
        if (!empty($info->borderStyle)){
            $pdf->SetLineWidth($info->borderSize);
            $pdf->SetDrawColor(...PDFUtils::GetColor($options->document, $info->borderColor));
        }
        if ($info->fontSize){
            $pdf->SetFontSize($info->fontSize); 
        }
    }
    public static function RenderNode($node, $pdf, $options=null){
        $tab = [
            ["item" => $node, "close" => false]
        ];
        $x = 0;
        $y = 0;
        $w = 0;
        $h = 0;
        if (!$options){
            \IGK\System\Html\HtmlRenderer::DefOptions($options);
        }
        $options->pdfRendering = 1;
        while (($q = array_pop($tab)) && !$options->Stop) {
       
            $tag = null;
            $i = null;
            if (is_array($q))
                $i = $q["item"];
            else {
                $i = $q;
                $q = ["item" => $i, "close" => false];
            }
            if (!$q["close"]) {
                if (method_exists($i, "RenderPDF")){
                    $i->RenderPDF($pdf, $options); 
                    continue;
                }
                $s = $content = $i->getContent();
                $childs = $i->getRenderedChilds($options);
                $have_childs = (count($childs) > 0);
                $have_content = $have_childs || !empty($content);   
                $q["close_tag"] =  $have_content || $i->closeTag();
                $q["close"] = true;
                $q["tag"] = $tag;
                $q["have_childs"]=$have_childs;
                if (!empty($content)) {
                    if (is_object($content)) {
                        $s = HtmlRenderer::GetValue($content, $options);
                    } else {
                        if (is_array($content)) {
                            $s = json_encode($content, JSON_UNESCAPED_SLASHES);
                        }
                    }
                }
                if (!empty($s)){
                    // render content 
                    $info = PDFUtils::GetStyleInfo($i, $pdf->getDocument());                  
                    $pdf->pushState();
                   // igk_wln_e($info->left, $info->top);
                   if ($info->top !== null){
                        $pdf->setY($info->top);
                   }
                    if ($info->left !== null){
                        $pdf->setX($info->left); 
                    }
                    self::BindInfo($pdf, $info, $options); 
                    $h = $info->lineHeight;
                    $w = 0;
                    if ($info->width!==null )
                        $w = $info->width;
                  //   igk_wln_e("cell height ",$h, $w);
                    $pdf->MultiCell($w, $h, $s,  $info->borderStyle, $info->align);
                    $pdf->popState();
                }
                if ($have_childs) {
                    array_push($tab, $q);
                    $childs = array_reverse($childs);
                    $tab = array_merge($tab, $childs); 
                    continue;
                }
            }
        }
        unset($options->pdfRendering);
    }
    public function render($n, $options)
    {
        self::RenderNode($n, $this->_pdf, $options);        
        return null;
    }
}
