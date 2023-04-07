<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PDFNodeBase.php
// @date: 20220309 13:29:29
// @desc: base pdf node

namespace igk\pdflib;
 
use IGK\Helper\Activator;

class PDFInfoNode extends PDFNodeBase{
    
    protected $tagname = PDFConstants::NS_PREFIX."page-info";
    private $data;
    public function __construct(array $data){
        $this->data = $data;
        parent::__construct();

    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag(){
        return false;
    }
    
    public function RenderPDF($pdf, $options=null){
        /**
         * @var PDFInfo $info
         */
        $info = Activator::CreateNewInstance(PDFInfo::class, $this->data);
        $pdf->SetSubject($info->subject, $info->utf8);
        $pdf->SetTitle($info->title, $info->utf8);
        $pdf->SetCreator($info->creator, $info->utf8);
        $pdf->SetAuthor($info->author, $info->utf8);
        $pdf->SetKeyWords($info->keywords, $info->utf8);
    }

}