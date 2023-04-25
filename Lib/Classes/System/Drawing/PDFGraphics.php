<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFGraphics.php
// @date: 20230411 11:07:01
namespace igk\pdflib\System\Drawing;

use igk\pdflib\System\IO\PDFFileImageResourceInfo;
use igk\pdflib\System\IO\PDFTypeFilePage;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
 * PDF Command line args
 * @package igk\pdflib\System\Drawing
 */
class PDFGraphics
{
    private $m_page;
    private $m_sb;
    private $m_fillColor;
    private $m_strokeColor;
    private $m_fillCMYKColor;
    private $m_strokeCMYKColor;
    private $m_matrix = [1, 0, 0, 1, 0, 0];
    private $m_font; // current font name
    private $m_fontSize = 12;

    private function __construct()
    {
    }
    public static function Create(PDFTypeFilePage $page)
    {

        $sb = new StringBuilder($page->getContents()->value->value);
        $p = new static;
        $p->m_page = $page;
        $p->m_sb = $sb;
        return $p;
    }
    /**
     * clear the buffer
     * @return void 
     */
    public function clear()
    {
        $this->m_sb->clear();
    }
    protected function _appendLine($command)
    {
        $this->m_sb->appendLine($command);
    }
    public function drawImage($name)
    {
        $this->_appendLine(sprintf("/Im% Do", $name));
    }
    public function drawLine($name)
    {
        $this->_appendLine(sprintf("/Im% Do", $name));
    }

    public function start()
    {
        $this->_appendLine("q");
    }
    public function flush()
    {
        $this->_appendLine("Q");
    }
    public function pushState()
    {
        $this->_appendLine("q");
    }
    public function popState()
    {
        $this->_appendLine("Q");
    }
    private function _getColor($color)
    {
        if (is_float($color) || is_numeric($color)) {
            $color = array_fill(0, 3, $color);
        }
        return $color;
    }
    /**
     * set stroke color
     * @param mixed $color 
     * @return void 
     */
    public function setStrokeColorf($color)
    {
        $cl = $this->_getColor($color);
        $this->m_strokeColor = $cl;
        $this->_appendLine(sprintf('%s RG', implode(" ", $cl)));
    }
    public function setFillColorf($color)
    {
        $cl = $this->_getColor($color);
        $this->m_fillColor = $cl;
        $this->_appendLine(sprintf('%s rg', implode(" ", $cl)));
    }

    public function setStrokeCMYKColorf($color)
    {
        $cl = $this->_getColor($color);
        $this->m_strokeCMYKColor = $cl;
        $this->_appendLine(sprintf('%s K', implode(" ", $cl)));
    }
    public function setFillCMYKColorf($color)
    {
        $cl = $this->_getColor($color);
        $this->m_fillCMYKColor = $cl;
        $this->_appendLine(sprintf('%s k', implode(" ", $cl)));
    }
    /**
     * draw rectangle
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @param int $operatioon type 
     * 
     * @return void 
     */
    public function drawRect(float $x, float $y, float $width, float $height, $op_type = 0)
    {
        // $this->_appendLine("1 0 0 1 10 30 cm");
        $this->_appendLine("0.5 G");
        $this->_appendLine("1.0 0.0 0.0 rg");
        $this->_appendLine(sprintf('%.2F %.2F %.2F %.2F re', $x, $y, $width, $height));
        $op = 'B';
        if ($op_type == 1) {
            $op = 's';
        } else if ($op_type == 2) {
            $op = 'f';
        }
        // $this->_appendLine('f'); // fill
        $this->_appendLine($op);
    }
    public function drawText(string $text, $x, $y)
    {
        $this->_appendLine('BT');
        $this->_appendLine(sprintf("/%s %s Tf", $this->m_font, $this->m_fontSize));
        $tq = explode("\n", $this->_escape($text));
        $h = 10;
        $this->_appendLine("q");
        $this->_appendLine("1 0 0 -1 0 100 cm");
        while (count($tq)) {
            $txt = array_shift($tq);
            $this->_appendLine(sprintf("%.2F %.2F Td", $x, $y));
            $this->_appendLine(sprintf("(%s) Tj", $txt));   // -> encode text 
            $y += $h;
        }
        $this->_appendLine("Q");
        $this->_appendLine('ET');
    }
    protected function _escape($s)
    {
        // Escape special characters
        if (strpos($s, '(') !== false || strpos($s, ')') !== false || strpos($s, '\\') !== false || strpos($s, "\r") !== false)
            return str_replace(array('\\', '(', ')', "\r"), array('\\\\', '\\(', '\\)', '\\r'), $s);
        else
            return $s;
    }
    /**
     * array of 6 elements [a c b d dx dy]
     * @param array|float[] $matrix 
     * @return void 
     */
    public function setMatrix(array $matrix)
    {
        $this->_appendLine(sprintf("%s cm", implode(" ", $matrix)));
        $this->m_matrix = $matrix;
    }
    /**
     * get current matrix
     * @return float[] 
     */
    public function getMatrix()
    {
        return $this->m_matrix;
    }
    /**
     * 
     * @param PDFFileImageResourceInfo $res 
     * @param mixed $x 
     * @param mixed $y 
     * @param mixed $w 
     * @param mixed $h 
     * @return void 
     */
    public function drawImageRes(PDFFileImageResourceInfo $res, $x=null, $y=null, $w=null, $h=null){         
        // $this->drawRect(0,0,100,100);
        // $this->_appendLine(sprintf("1 0 0 -1 0 200 cm"));
        $x = $x ?? 0;
        $x = $y ?? 0;
        $w = $w ?? $res->W;
        $h = $h ?? $res->H;
        $this->pushState();
        $this->_appendLine(sprintf("%.2F 0 0 %.2F %.2F %.2F cm",$w, $h, $x, $y));
        $this->_appendLine(sprintf("/%s Do", $res->id)); 
        $this->popState();
    }
   
}
