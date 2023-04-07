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


