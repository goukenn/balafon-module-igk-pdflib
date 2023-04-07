<?php
namespace igk\pdflib\Tests;

use \IGK\Tests\BaseTestCase;

class PDFDocumentTest extends BaseTestCase{
    static function setUpBeforeClass(): void
    {
        igk_current_module()->autoload_register();
    }
    public function setup():void{
        require_once igk_io_packagesdir()."/Lib/fpdf/fpdf.php";
    }
    public function test_render_document(){
        $doc = new \igk\pdflib\PDFDocument();
        $this->assertEquals(
            "<igk:pdflib-document><igk:pdflib-header></igk:pdflib-header><igk:pdflib-footer></igk:pdflib-footer></igk:pdflib-document>",
            $doc->render()
        );       
    }
    public function test_render_document_load_host(){
        $doc = new \igk\pdflib\PDFDocument();        
        $n = $doc->host(function(){
        });
        
        $this->assertEquals(
            \igk\pdflib\PDFHostNode::class,
            get_class($n) 
        );       
    }

    public function test_render_document_cell(){
        $doc = new \igk\pdflib\PDFDocument();        
        $doc->cell(30)->setStyle("+/border-left-width: 3px; border-right-width: 4px;")->Content = "present";        
        ob_start();
        $doc->output();
        $c = ob_get_clean(); 
        $g = preg_match("/^%PDF-/", $c);
        $this->assertTrue($g == 1);
          
    }

    public function test_render_pdf(){
        $doc = new \igk\pdflib\PDFDocument();                
        ob_start();
        $doc->output();
        $c = ob_get_clean();
        $this->assertTrue(preg_match("/^%PDF/", $c) == 1);        
    }
    public function test_render_pdf_2(){
        $doc = new \igk\pdflib\PDFDocument();    
        $doc->p()
        ->setStyle("color: red; margin-left:10px")
        ->Content = "Info";
        ob_start();
        $doc->output();
        $c = ob_get_clean();
        $this->assertTrue(preg_match("/^%PDF/", $c) == 1);        
    }
  

    public function test_render_measures()
    {
        $doc = new \igk\pdflib\PDFDocument(); 
        $doc->h1()->Content = "Bonjour Tout le monde";
        $engine = new MockPdfDocumentEngine($doc);
        
        // var_dump(
        //     $engine->MesureInfo("La    vie est belle \npour tout le mond", 300, 10)
        // ); 

        $this->assertEquals(
            '{"string":" ","definition":1.1768666666666663}',
            $engine->output()
        ) ;    
    }

}