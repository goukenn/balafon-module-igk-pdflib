<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PDFFileFactory.php
// @date: 20230407 16:08:29
// @desc: 

namespace igk\pdflib\System\IO;

/**
 * pdf object factory
 * @package igk\pdflib\System\IO
 */
abstract class PDFFileFactory {
    static $sm_def;
    private static function & _GetDef(){
        if (is_null(self::$sm_def)){
            self::$sm_def = [];
        } 
        return self::$sm_def;
    }
    public static function Create(string $name, ...$args){
        $def = & self::_GetDef();
        
        if (!($cl = igk_getv($def, $name))){
            $cl = __NAMESPACE__.'\\PDFFile'.ucfirst($name);
            if (class_exists($cl) && is_subclass_of($cl, PDFFileObject::class) && !igk_sys_reflect_class($cl)->isAbstract()){
                $def[$name]=$cl;
            }
            else $cl = null;
        }
        if ($cl)
            return new $cl(...$args);
        return null;
    }
    public static function CreateType(string $name){
        $def = & self::_GetDef();
        
        if (!($cl = igk_getv($def, $name))){
            $cl = __NAMESPACE__.'\\PDFTypeFile'.ucfirst($name);
            if (class_exists($cl) && is_subclass_of($cl, PDFFileObjectType::class) && !igk_sys_reflect_class($cl)->isAbstract()){
                $def[$name]=$cl;
            }
            else $cl = null;
        }
        if ($cl)
            return new $cl();
        return null;
    }
}