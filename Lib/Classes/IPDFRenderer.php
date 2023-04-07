<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IPDFRenderer.php
// @date: 20220310 17:36:30
// @desc: pdf prefix

namespace igk\pdflib;


interface IPDFRenderer{
   function RenderPDF($pdf, $options=null);
}