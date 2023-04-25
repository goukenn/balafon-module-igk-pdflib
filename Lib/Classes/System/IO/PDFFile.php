<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFFile.php
// @date: 20230407 10:52:46
namespace igk\pdflib\System\IO;

use Exception;
use igk\pdflib\PDFConstants;
use igk\pdflib\PDFUtils;
use igk\pdflib\System\IO\PDF_2_0\PDFFileMetadata;
use igk\pdflib\System\IO\PDFFile as IOPDFFile;
use igk\pdflib\System\IO\Traits\PDFFileInfoManagementTrait;
use igk\pdflib\System\IO\Traits\PDFParsePngTrait;
use igk\pdflib\System\XMP\XMPDocument;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGKException;
use PSpell\Dictionary;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package igk\pdflib\System\IO
 */
class PDFFile
{
    use PDFFileInfoManagementTrait;
    use PDFParsePngTrait;
    var $version = PDFConstants::FILE_VERSION_2_0;
    // var $version = PDFConstants::FILE_VERSION_1_7;
    var $m_objects_catalogues = [];
    private $m_pdf_info;
    private $m_catalog; // catalog collection
    private $m_pages;   // root pages collection
    private $m_current_page;
    private $m_metadata; // metadata stream to store info in v2.0
    private $m_unit = 'pt';
    private $m_images = [];
    private $m_resource_dictionary;

    public function __debugInfo()
    {
        return [];
    }

    private function LoadDictionary($l)
    {
        return PDFUtils::ReadDictionary($l);
    }
    /**
     * 
     * @param string $file 
     * @return void 
     */
    public static function Open(string $file)
    {
        if (!@is_readable($file) ||  !($hfile = fopen($file, 'r+'))) {
            return null;
        }
        $objects = [];
        $bufferSize = 4096;
        $streaminfo = null;
        $size = 0;
        $info = 0;
        $tab = [];
        $id = 0;
        $mode = 0;
        $buffer = [];
        $fsize = filesize($file);
        $error = [];
        $er = '';
        while (!feof($hfile) && ($r = fread($hfile, $bufferSize))) {
            $lines = explode("\n", $r);
            if (!$info) {
                $pdf_version = array_shift($lines);
                $info = 1;
            }
            foreach ($lines as $k) {
                if ($mode > 0) {
                    //read content
                    switch ($mode) {
                        case 2:
                            $buffer[] = trim($k);
                            if ($k=='endstream'){
                                $st_start = array_shift($buffer);
                                $st_end = array_pop($buffer);
                                if ($st_start!='stream'){
                                    igk_die('stream not found');
                                }
                                $data = trim(implode("\n", $buffer));
                                $streaminfo->stream = $data;
                                $mode = 0; 
                                if ($size && (strlen($data)!=$size)){
                                    $er = ("not matching length ".$size. " vs ".strlen($data));
                                    $error[] = $er;
                                    error_log($er);
                                }
                                $size = 0;
                            } else  if ($k=='endobj'){
                                $mode = 0; 
                                $buffer = [];
                            }
                            break;
                        default:

                            if (strpos($g = trim($k), '<<') === 0) {
                                //read dictionary
                                //check for ready dictionary closed 
                                $ln = strlen($g);
                                $pos = 2;
                                $depth = 0;
                                while ($pos < $ln) {
                                    $min = strpos($g, '<<', $pos);
                                    $max = strpos($g, '>>', $pos);
                                    if ($min !== false) {
                                        $depth++;
                                        $pos = $min + 2;
                                    } else if ($max) {
                                        $depth--;
                                        $pos = $max + 2;
                                    }
                                    if ($depth == -1) {
                                        //close read dictionary
                                        break;
                                    }
                                }
                                if ($depth == -1) {
                                    $buffer[] = substr($g, 0, $pos);
                                    $dic = PDFUtils::ReadDictionary(implode("\n", $buffer));
                                    $streaminfo->dictionary = $dic;
                                    $buffer = [substr($g, $pos)];
                                    if (isset($streaminfo->dictionary['Length'])) {
                                        $size = $streaminfo->dictionary['Length'];
                                    }
                                    $mode = 2;
                                } else {
                                    $buffer[] = $g;
                                    $mode = 2;
                                }
                            }
                    }
                    continue;
                }
                if (preg_match("/^([0-9]+) ([0-9]+) obj$/", $k, $tab)) {
                    $id = $tab[1] . ' ' . $tab[2];
                    $streaminfo = (object)['id' => $id, 'buffer' => [], 'dictionary' => null, 'stream' => null];
                    $objects[$id] = $streaminfo;
                    $buffer = &$streaminfo->buffer;
                    $mode = 1;
                } else if (preg_match("/^endobj$/", $k, $tab)) {
                    $mode = 0;
                    $id = 0;
                    $streaminfo = null;
                } else {
                }
            }
        }

        if ($streaminfo){
            igk_wln_e("lkjdf");
        }
        fclose($hfile);

        foreach($objects as $obj)
        {
            if (isset($obj->dictionary['Image'])){
                continue;
            }
            if (isset($obj->dictionary['FlateDecode'])){
                if ($txt = @gzuncompress($obj->stream)){
                     igk_wln("data, ===========", ' ', $txt, ' ',' ');
                } else {
                    igk_wln("failed to uncompress ");
                }
            } else {
                igk_wln("data ::::: ".$obj->stream);
            }
        }

        $f = new static;

        return $f;
    }
    private function getUnit()
    {
        return $this->m_unit;
    }
    public function getScaleFactor()
    {
        $unit =  $this->getUnit();
        switch ($unit) {
            case 'cm':
                return  72 / 2.54;
            case 'mm':
                return  72 / 25.4;
            case 'in':
                return 72;
            case 'pt':
            default:
                return 1;
        }
    }

    /**
     * @var PDFFileMetadata $metadata
     */
    private $m_filemetadata;

    /**
     * default page media box
     * @return mixed 
     */
    private $m_mediabox;
    /**
     * get current page
     * @return mixed 
     */
    public function getCurrentPage()
    {
        return $this->m_current_page;
    }
    public function getFileMetadata()
    {
        return $this->m_filemetadata;
    }
    public function setFileMetadata(?PDFFileMetadata $filemetadata = null)
    {
        $this->m_filemetadata = $filemetadata;
    }

    public function __construct()
    {
        $this->m_mediabox = new PDFTypeFileArrayType([0, 0, 200.0, 200.0]);
        $this->initialize();

        $v_rootobj = new PDFFileObject;
        $v_rootobj->numberId = $this->_getId();
        $this->m_objects_catalogues[] = $v_rootobj;

        $v_version = igk_current_module();
        $this->m_pdf_info = new PDFFileInfo();
        $this->_addObject($this->m_pdf_info);

        $version = $v_version->config('version');
        $author = $v_version->config('author') ?? IGK_AUTHOR;
        $this->m_pdf_info->setProducer(sprintf('(igk-pdflib %s)', $version));
        $this->m_pdf_info->setCreationDate(sprintf('(%s)', 'D:' . date('YmdHis') . "+'00'"));
        $this->m_pdf_info->setAuthor(sprintf('(%s)', $author));
        $this->m_pdf_info->setTitle(sprintf('(%s)', $author));
        $this->m_pdf_info->setCreator(sprintf('(%s)', $author));

        $m_pages = new PDFTypeFilePages;
        $m_catalog = new PDFFileCatalog;

        // + | metadata for file document
        $m_metadata = new PDFFileStream;
        $m_metadata->addName(PDFNames::Type);
        $m_metadata->addName(PDFNames::Metadata);
        $m_metadata->addName(PDFNames::Subtype);
        $m_metadata->addName(PDFNames::XML);

        $length = $m_metadata->addName(PDFNames::Length);
        $span = time();
        $time = date('Y-m-d', $span) . 'T' . date('H:i:s', $span);
        $doc = new XMPDocument;
        $rdf = $doc->add('rdf:RDF')
            ->setAttribute('xmlns:rdf', XMPDocument::RDF)
            ->setAttribute('xmlns:pdf', 'http://ns.adobe.com/pdf/1.3/');
        $desc = $rdf->add('rdf:Description');
        $desc->add('pdf:Producer')->setContent(sprintf('%s-%s', PDFConstants::CREATOR_TOOL_NAME, $version));
        $desc->add('pdf:Keywords')->Content = 'Sangoku';
        $desc->add('xmp:CreatorTool')->Content = ($this->m_filemetadata ? $this->m_filemetadata->CreatorTool : null) ??
            sprintf('%s-%s', PDFConstants::CREATOR_TOOL_NAME, $version);
        $desc->add('xmp:CreateDate')->Content = ($time);
        $desc->add('xmp:ModifyDate')->Content = ($time);
        $desc->add('dc:format')->Content = 'application/pdf';
        $desc->add('dc:title')->Content = ($this->m_filemetadata ? $this->m_filemetadata->Title : null) ?? 'document.pdf';
        $desc->add('dc:creator')->add('rdf:Seq')->add('rdf:li')->Content = ($this->m_filemetadata ? $this->m_filemetadata->Author : null) ?? $author;
        // $desc->add('dc:subject')->Content = 'subject'; // set as keywords if no pdf:Keywords
        $desc->add('dc:description')->Content = igk_getv($this->m_filemetadata, 'keywords', null);
        $desc->add('xmpMM:DocumentID')->Content = igk_create_guid();
        $desc->add('xmpMM:InstanceID')->Content = igk_create_guid();


        $m_metadata->value = $doc->render();
        $length->value = strlen($m_metadata->value);

        $m_catalog->addName(PDFNames::Metadata)->value = $m_metadata;

        $this->_addObject($m_catalog);
        $this->_addObject($m_pages);
        $this->_addObject($m_metadata);

        $this->m_catalog = $m_catalog;
        $m_catalog->addRefPages($m_pages);
        $this->m_pages = $m_pages;

        /*
        $this->newPage();
        // $this->newPage();
        // $this->newPage();

        $content = new PDFFileStream;
        $content->addName(PDFNames::Filter);
        $content->addName(PDFNames::FlateDecode);
        $len = $content->addName(PDFNames::Length);

        $ft = $this->addFont();

        $content->value = gzcompress(implode("\n", [
            'BT',
            '/F13 12 Tf',
            '0 0 Td',
            '(Basic data ) Tj',
            'ET'
        ]));
        $len->value = strlen($content->value);

        $this->m_current_page->addName(PDFNames::Contents)->value = $content;
        $this->_addObject($content);
        // + | add font collection with F13
        $g = new PDFFileNamedObject('F13');
        $g->value = $ft;
        $fdic = new PDFFileDictionary;
        $fdic->inline = true;
        $fdic->add($g);
        $this->m_current_page->getResources()->value->addName(PDFNames::Font)->value = $fdic;

        $this->newPage();
        $this->drawImageFromFile("");
        */
    }
    protected function beforeRender()
    {
    }
    public function setFont()
    {
    }
    /**
     * load image resources
     * @param string $file 
     * @return PDFFileImageResourceInfo 
     * @throws Exception 
     */
    public function loadImageFile(string $file)
    {
        $a = getimagesize($file);
        if (!$a) {
            throw new \Exception("PDFFile exception");
        }
        if (isset($this->m_images[$file])) {
            return $this->m_images[$file];
        }

        $stream = new PDFFileStream;
        $stream->addName(PDFNames::Type);
        $stream->addName(PDFNames::XObject);
        $stream->addName(PDFNames::Subtype);
        $stream->addName(PDFNames::Image);
        $stream->addName(PDFNames::BitsPerComponent)->value = $a['bits'];
        $stream->addName(PDFNames::Width)->value = $W = $a[0];
        $stream->addName(PDFNames::Height)->value = $H = $a[1];
        $stream->addName(PDFNames::ColorSpace)->value = new PDFFileNamedObject(PDFNames::DeviceRGB);
        $this->_addObject($stream);

        if ($a['mime'] == 'image/png') {
            $info = $this->_parsepng($file);
            if (isset($info['f'])) {
                $stream->addName(PDFNames::Filter)->value = new PDFFileNamedObject($info['f']);
            }
            if (isset($info['dp'])) {
                if ($c = preg_match_all("/\/(?P<name>[^ ]+) (?P<value>[^\/]+)/", $info['dp'], $tab)) {
                    for ($i = 0; $i  < $c; $i++) {
                        $stream->addName($tab['name'][$i])->value = $tab['value'][$i];
                    }
                }
            }
            $stream->value =  $info['data'];
        } else {
            $data = file_get_contents($file);
            $stream->value = $data;
            $stream->addName(PDFNames::Filter)->value = new PDFFileNamedObject(PDFNames::DCTDecode);
            $colorspace = ($sp = $a['channels'] == 3) ? PDFNames::DeviceRGB : ($sp == 4 ? PDFNames::DeviceCMYK :  PDFNames::DeviceGray);
            $stream->addName(PDFNames::ColorSpace)->value = new PDFFileNamedObject($colorspace);
        }

        $id = $this->_getImgId();
        $img = new PDFFileNamedObject($id);
        $img->value = $stream;

        $dic = new PDFFileDictionary;
        $dic->inline = true;
        $dic->add($img);


        $refobj = new PDFFileObject();
        $item = new PDFFileNamedObject(PDFNames::XObject);
        $item->value = $dic;
        $refobj->add($item);

        $this->_addObject($refobj);

        $info = new PDFFileImageResourceInfo();
        $info->H = $H;
        $info->W = $W;
        $info->res = $refobj;
        $info->id = $id;
        $this->m_images[$file] =  $info;

        // return a reference object
        return $info; // $refobj;
    }
    protected function _getImgId()
    {
        $c = count($this->m_images) +  1;
        return 'Im' . $c;
    }

    // public function drawImageFromFile(string $file){
    //     $W = 48;
    //     $H = 48;
    //     $stream = new PDFFileStream;
    //     $stream->addName(PDFNames::Type);
    //     $stream->addName(PDFNames::XObject);
    //     $stream->addName(PDFNames::Subtype);
    //     $stream->addName(PDFNames::Image);
    //     $stream->addName(PDFNames::BitsPerComponent)->value = 8;
    //     $stream->addName(PDFNames::Width)->value = $W;
    //     $stream->addName(PDFNames::Height)->value = $H; 
    //     $stream->addName(PDFNames::ColorSpace)->value = new PDFFileNamedObject(PDFNames::DeviceRGB);

    //     $data = '';//[];
    //     for($i=0; $i< $W;$i++)
    //         for($j=0; $j< $H;$j++){
    //             //build image data
    //             $data .= [ 
    //                 "\x00\x00\xFF",
    //                 "\xFF\x00\xFF",
    //                 "\x00\xFF\xFF",
    //             ][rand(0, 2)];
    //             // $offset = ($i * $j);
    //             // $data[$offset]  = 0;
    //             // $data[$offset+1]  = 0;
    //             // $data[$offset+2]  = 0;
    //         }
    //     $stream->value = $data; //implode("", $data);

    //     $this->_addObject($stream);
    //     // define object reference 
    //     $img = new PDFFileNamedObject('Im1');
    //     $img->value = $stream;

    //     $dic = new PDFFileDictionary;
    //     $dic->inline = true;
    //     $dic->add($img);
    //     $refobj = new PDFFileObject();
    //     $item = new PDFFileNamedObject(PDFNames::XObject);
    //     $item->value = $dic;
    //     // $item->value->addName("Img1")->value = $stream;
    //     $refobj->add($item);



    //     $this->_addObject($refobj);

    //     // object ref
    //     $content = $this->getCurrentPage()->getContents();
    //     $content->value->value = implode("\n", [
    //         "q",
    //         "96 0 0 96 20 100 cm",
    //         "/Im1 Do",
    //         "Q",
    //     ]); // "q 1 0 0 1 0 210 cm /Im1 Do Q";

    //     $this->m_current_page->getResources()->value = new PDFFileRefValue($refobj);
    // }

    /**
     * create and add selected page
     * @return PDFTypeFilePage
     * @throws IGKException 
     */
    public function newPage($mediabox = null)
    {
        $mediabox = $mediabox ?? $this->m_mediabox;
        $content = new PDFFileStream;
        $this->_addObject($content);
        $page = PDFFileFactory::CreateType(PDFNames::Page);
        $this->_addObject($page);
        $this->m_pages->addPage($page);
        $page->addName(PDFNames::MediaBox)->value = $mediabox;
        $page->addName(PDFNames::CropBox)->value = $mediabox;
        $page->addName(PDFNames::Contents)->value = $content;

        $v_transparency = new PDFFileDictionary;
        // /Group /S /Transparency /CS /DeviceRGB
        $v_transparency->addName(PDFNames::Type);
        $v_transparency->addName(PDFNames::Group);
        $v_transparency->addName(PDFNames::S);
        $v_transparency->addName(PDFNames::Transparency);
        $v_transparency->addName(PDFNames::CS);
        $v_transparency->addName(PDFNames::DeviceRGB);
        $v_transparency->inline = true;
        $page->addName(PDFNames::Group)->value = $v_transparency;

        // $this->m_current_page->getResources()->addName(PDFNames::Font)->value = $this->getFont('F13');


        $content->value = implode("\n", [
            'q',
            'BT',
            '/F13 56 Tf',
            '0 0 Td',
            '(Page) Tj',
            'ET',
            'Q'
        ]);

        $this->m_current_page = $page;
        return $page;
    }
    /**
     * add object to container 
     * @param mixed $obj 
     * @return void 
     */
    protected function _addObject($obj)
    {
        $obj->numberId = $this->_getId();
        $this->m_objects_catalogues[] = $obj;
    }
    protected function _getId()
    {
        return count($this->m_objects_catalogues) + 1;
    }
    protected function initialize()
    {
    }
    /**
     * output files 
     * @param bool $render 
     * @return null|string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function output($render = true, ?string $file = null): ?string
    {
        $sb = new StringBuilder;
        // + | render version 
        $sb->appendLine('%' . sprintf('%s-%s', PDFNames::PDF, $this->version));

        $v_objcount = count($this->m_objects_catalogues);
        $v_offset = [];
        // + | render object catalogs
        for ($i = 0; $i < $v_objcount; $i++) {
            $v_offset[$i] = $sb->length();
            $sb->appendLine($this->m_objects_catalogues[$i]->render());
        }
        $v_size = $sb->length();
        // + | xref
        $sb->appendLine(PDFNames::xref);
        $sb->appendLine(sprintf("%s %s", 0, $v_objcount));
        $sb->appendLine('0000000000 65535 f ');

        // + | update file object definition 
        $this->_update();

        // + | object definition 
        for ($i = 0; $i < $v_objcount; $i++) {
            $sb->appendLine(sprintf(
                '%s %s %s',
                str_pad($v_offset[$i], 10, '0',  STR_PAD_LEFT),
                str_pad(0, 5, '0', STR_PAD_LEFT),
                'n'
            ));
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

        $o = $sb . '';
        if ($file) {
            igk_io_w2file($file, $o);
        }
        if ($render) {
            header('Content-Type: application/pdf');
            echo $o;
            igk_exit();
        }

        return $o;
    }
    /**
     * update object before render
     */
    protected function _update()
    {
    }
    /**
     * register helvetica font
     * @return PDFFileObject 
     */
    public function addFont()
    {
        $font = new PDFFileObject;
        $font->addName(PDFNames::Type);
        $font->addName(PDFNames::Font);
        $font->addName(PDFNames::Subtype);
        $font->addName(PDFNames::Type1);
        $font->addName(PDFNames::BaseFont);
        $font->addName(PDFNames::FT_HELVETICA);
        $this->_addObject($font);
        return $font;
    }
}
