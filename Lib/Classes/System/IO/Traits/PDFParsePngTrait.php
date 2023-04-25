<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFParsePngTrait.php
// @date: 20230411 13:32:04
namespace igk\pdflib\System\IO\Traits;


///<summary></summary>
/**
* from fpdf 
* @package igk\pdflib\System\IO\Traits
*/
trait PDFParsePngTrait{

    protected function _parsepng($file)
    {
        // Extract info from a PNG file
        $f = fopen($file,'rb');
        if(!$f)
            $this->Error('Can\'t open image file: '.$file);
        $info = $this->_parsepngstream($f,$file);
        fclose($f);
        return $info;
    }
    
    protected function _parsepngstream($f, $file)
    {
        // Check signature
        if($this->_readstream($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
            $this->Error('Not a PNG file: '.$file);
    
        // Read header chunk
        $this->_readstream($f,4);
        if($this->_readstream($f,4)!='IHDR')
            $this->Error('Incorrect PNG file: '.$file);
        $w = $this->_readint($f);
        $h = $this->_readint($f);
        $bpc = ord($this->_readstream($f,1));
        if($bpc>8)
            $this->Error('16-bit depth not supported: '.$file);
        $ct = ord($this->_readstream($f,1));
        if($ct==0 || $ct==4)
            $colspace = 'DeviceGray';
        elseif($ct==2 || $ct==6)
            $colspace = 'DeviceRGB';
        elseif($ct==3)
            $colspace = 'Indexed';
        else
            $this->Error('Unknown color type: '.$file);
        if(ord($this->_readstream($f,1))!=0)
            $this->Error('Unknown compression method: '.$file);
        if(ord($this->_readstream($f,1))!=0)
            $this->Error('Unknown filter method: '.$file);
        if(ord($this->_readstream($f,1))!=0)
            $this->Error('Interlacing not supported: '.$file);
        $this->_readstream($f,4);
        $dp = '/Predictor 15 /Colors '.($colspace=='DeviceRGB' ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w;
    
        // Scan chunks looking for palette, transparency and image data
        $pal = '';
        $trns = '';
        $data = '';
        do
        {
            $n = $this->_readint($f);
            $type = $this->_readstream($f,4);
            if($type=='PLTE')
            {
                // Read palette
                $pal = $this->_readstream($f,$n);
                $this->_readstream($f,4);
            }
            elseif($type=='tRNS')
            {
                // Read transparency info
                $t = $this->_readstream($f,$n);
                if($ct==0)
                    $trns = array(ord(substr($t,1,1)));
                elseif($ct==2)
                    $trns = array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
                else
                {
                    $pos = strpos($t,chr(0));
                    if($pos!==false)
                        $trns = array($pos);
                }
                $this->_readstream($f,4);
            }
            elseif($type=='IDAT')
            {
                // Read image data block
                $data .= $this->_readstream($f,$n);
                $this->_readstream($f,4);
            }
            elseif($type=='IEND')
                break;
            else
                $this->_readstream($f,$n+4);
        }
        while($n);
    
        if($colspace=='Indexed' && empty($pal))
            $this->Error('Missing palette in '.$file);
        $info = array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'dp'=>$dp, 'pal'=>$pal, 'trns'=>$trns);
        if($ct>=4)
        {
            // Extract alpha channel
            if(!function_exists('gzuncompress'))
                $this->Error('Zlib not available, can\'t handle alpha channel: '.$file);
            $data = gzuncompress($data);
            $color = '';
            $alpha = '';
            if($ct==4)
            {
                // Gray image
                $len = 2*$w;
                for($i=0;$i<$h;$i++)
                {
                    $pos = (1+$len)*$i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = substr($data,$pos+1,$len);
                    $color .= preg_replace('/(.)./s','$1',$line);
                    $alpha .= preg_replace('/.(.)/s','$1',$line);
                }
            }
            else
            {
                // RGB image
                $len = 4*$w;
                for($i=0;$i<$h;$i++)
                {
                    $pos = (1+$len)*$i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = substr($data,$pos+1,$len);
                    $color .= preg_replace('/(.{3})./s','$1',$line);
                    $alpha .= preg_replace('/.{3}(.)/s','$1',$line);
                }
            }
            unset($data);
            $data = gzcompress($color);
            $info['smask'] = gzcompress($alpha);
            $this->WithAlpha = true;
            if($this->PDFVersion<'1.4')
                $this->PDFVersion = '1.4';
        }
        $info['data'] = $data;
        return $info;
    }
    
    protected function _readstream($f, $n)
    {
        // Read n bytes from stream
        $res = '';
        while($n>0 && !feof($f))
        {
            $s = fread($f,$n);
            if($s===false)
                $this->Error('Error while reading stream');
            $n -= strlen($s);
            $res .= $s;
        }
        if($n>0)
            $this->Error('Unexpected end of stream');
        return $res;
    }
    
    protected function _readint($f)
    {
        // Read a 4-byte integer from stream
        $a = unpack('Ni',$this->_readstream($f,4));
        return $a['i'];
    }
}