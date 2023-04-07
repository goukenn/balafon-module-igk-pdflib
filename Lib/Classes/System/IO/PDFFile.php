<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFile.php
// @date: 20230407 10:52:46
namespace igk\pdflib\System\IO;

use igk\pdflib\PDFConstants;
use igk\pdflib\System\IO\Traits\PDFFileInfoManagementTrait;
use igk\pdflib\System\XMP\XMPDocument; 
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGKException;
use ReflectionException; 

///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
class PDFFile{
    use PDFFileInfoManagementTrait;
    // var $version = PDFConstants::FILE_VERSION_2_0;
    var $version = PDFConstants::FILE_VERSION_1_7;
    var $m_objects_catalogues = [];
    private $m_pdf_info;
    private $m_catalog; // catalog collection
    private $m_pages;   // root pages collection
    private $m_current_page;
    private $m_metadata; // metadata stream to store info in v2.0

    public function __construct()
    {
        $this->initialize();
        $v_rootobj = new PDFFileObject;
        $v_rootobj->numberId = $this->_getId();
        $this->m_objects_catalogues[] = $v_rootobj;

        $v_version = igk_current_module();
        $this->m_pdf_info = new PDFFileInfo();
        $this->_addObject($this->m_pdf_info);

        $version = $v_version->config('version');
        $author = $v_version->config('author') ?? IGK_AUTHOR;

        $this->m_pdf_info->setProducer(sprintf('(igk-pdflib %s)', $version ));
        $this->m_pdf_info->setCreationDate(sprintf('(%s)', 'D:'.date('YmdHis')));
        $this->m_pdf_info->setAuthor(sprintf('(%s)', $author)); 
        $this->m_pdf_info->setTitle(sprintf('(%s)', $author)); 
        $this->m_pdf_info->setCreator(sprintf('(%s)', $author)); 

        $m_pages = new PDFTypeFilePages;
        $m_catalog = new PDFFileCatalog;
        $m_metadata = new PDFFileStream;
        $m_metadata->addName(PDFNames::Type);
        // $m_metadata->addName(PDFNames::Streamdata);
        $length = $m_metadata->addName(PDFNames::Length);

        $doc = new XMPDocument;
        $m_metadata->value = $doc->render();
        $length->value = strlen($m_metadata->value);

        $size = new PDFTypeFileArrayType([0, 0 ,200 ,200]);
        $m_catalog->addName(PDFNames::MediaBox)->value = $size;
        $m_catalog->addName(PDFNames::Metadata)->value = $m_metadata;

        $this->_addObject($m_catalog);
        $this->_addObject($m_pages);
        $this->_addObject($m_metadata);

        $this->m_catalog = $m_catalog;
        $m_catalog->addRefPages($m_pages);
        $this->m_pages = $m_pages;

        $this->m_current_page = PDFFileFactory::CreateType(PDFNames::Page);

        $this->_addObject($this->m_current_page);

        $this->m_pages->addPage($this->m_current_page);
        $this->newPage();
    }

    public function newPage(){
        $this->m_current_page = PDFFileFactory::CreateType(PDFNames::Page);
        $this->_addObject($this->m_current_page);
        $this->m_pages->addPage($this->m_current_page);
    }
  
    protected function _addObject($obj){
        $obj->numberId = $this->_getId();
        $this->m_objects_catalogues[] = $obj;
    }
    protected function _getId(){
        return count($this->m_objects_catalogues) + 1;
    }
    protected function initialize(){

    }
    /**
     * output files 
     * @param bool $render 
     * @return null|string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function output($render=true, ?string $file=null):?string{
        $sb = new StringBuilder;
        // + | render version 
        $sb->appendLine('%'.sprintf('%s-%s', PDFNames::PDF, $this->version));

        $v_objcount = count($this->m_objects_catalogues);
        $v_offset = [];
        // + | render object catalogs
        for($i = 0; $i < $v_objcount; $i++){
            $v_offset[$i] = $sb->length();
            $sb->appendLine($this->m_objects_catalogues[$i]->render());
        }
        $v_size = $sb->length();
        // + | xref
        $sb->appendLine(PDFNames::xref);
        $sb->appendLine(sprintf("%s %s", 0, $v_objcount));
        $sb->appendLine('0000000000 65535 f ');

        // + | object definition 
        for($i = 0; $i < $v_objcount; $i++){
            $sb->appendLine(sprintf('%s %s %s', 
            str_pad($v_offset[$i], 10,'0',  STR_PAD_LEFT),
            str_pad(0, 5,'0', STR_PAD_LEFT),
            'n'));
        }
        $v_trailer = new PDFFileTrailer;
        $v_trailer->setSize($v_objcount);
        // if (version_compare($this->version, '2.0', '<')){
            $v_trailer->addRef(PDFNames::Info, $this->m_pdf_info);
        //} 
        $v_trailer->addRef(PDFNames::Root, $this->m_catalog);

        $sb->appendLine(PDFNames::trailer);
        $sb->appendLine($v_trailer->render());
        $sb->appendLine(PDFNames::startxref);
        $sb->appendLine($v_size);
        $sb->append(PDFNames::ENDFILE);

        $o = $sb.'';
        if ($file){
            igk_io_w2file($file, $o);
        }
        if ($render){
            header('Content-Type: application/pdf');
            echo $o;
            igk_exit();
        }

        return $o;
    }
}