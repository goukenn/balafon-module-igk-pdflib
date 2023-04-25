# igk/pdflib
 
@C.A.D.BONDJEDOUE


module that will help to create pdf document 

> usage 

```php
igk_require_module(igk\pdflib::class);
```

after require we can get the `FpdfPrinterService` provide by the module.


```php
use igk\pdflib\System\Services\Printing\FpdfPrinterService;
// ....
$srv = new FpdfPrinterService;
// .... build pdf document 
$srv->output(); 
```


# loading font to page
```php
$ft = $file->addFont();
$this->m_current_page->addName(PDFNames::Contents)->value = $content;
$g = new PDFFileNamedObject('F13');
$g->value = $ft; 
$fdic = new PDFFileDictionary;
$fdic->inline = true;
$fdic->add($g);
$file->currentPage()->getResources()->value->addName(PDFNames::Font)->value = $fdic;
``

