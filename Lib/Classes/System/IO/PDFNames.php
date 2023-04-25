<?php
// @author: C.A.D. BONDJE DOUE
// @file: PDFNames.php
// @date: 20230407 11:04:35
namespace igk\pdflib\System\IO;


///<summary></summary>
/**
* 
* @package igk\pdflib\System\IO
*/
abstract class PDFNames{
    const xref = 'xref';
    const Type='Type';
    const Type1='Type1';
    const Type2='Type2';
    const TrueType='TrueType';
    const Font='Font';
    const Length='Length'; 
    const F='F';
    const Filter='Filter';
    const FFilter='FFilter';
    const FDecodeParms='FDecodeParms';
    const DL='DL';
    const Page='Page';
    const Parent='Parent';
    const Resources='Resources';
    const Kids='Kids';
    const Count='Count';
    
    // box definition names
    const MediaBox='MediaBox';
    const CropBox='CropBox';
    const BlendBox='BlendBox';
    const TrimBox='TrimBox';
    const ArtBox='ArtBox';

    const BaseType='BaseType';
    const BBox='BBox';
    const Subtype='Subtype';


    const EndOfLine = 'EndOfLine';

    //trailer
    const trailer='trailer';
    const startxref='startxref';
    const Size='Size';
    const Root='Root';
    const Catalog='Catalog';
    const Pages='Pages';
    const Contents='Contents';
    const BaseFont='BaseFont';
    const Group='Group';
    const F1='F1';
    const F2='F2';
    const F3='F3';
    const F4='F4';
    const F5='F5';
    const F13='F13';
    const WinAnsiEncoding='WinAnsiEncoding';
    const Encoding='Encoding';
    const XObject='XObject';
    const Image='Image';
    const ImageB='ImageB';
    const PDF='PDF';
    const ProcSet='ProcSet';
    const Width='Width';
    const Height='Height';
    const ColorSpace='ColorSpace';
    const DeviceRGB='DeviceRGB';
    const DeviceCMYK='DeviceCMYK';
    const DeviceGray='DeviceGray';
    const BitsPerComponent='BitsPerComponent';
    const Colors='Colors';
    const Predictor='Prdictor';
    const Columns='Columns'; 

    const ENDFILE = '%%EOF';
    const obj = 'obj';
    const endobj = 'endobj';

    const R='R';
    
    const Producer = 'Producer';
    const CreationDate = 'CreationDate';
    const ModDate = 'ModDate';
    const Author = 'Author';
    const Trapped = 'Trapped';
    const Subject = 'Subject';
    const Keywords = 'Keywords';
    const Creator = 'Creator';
    const Title = 'Title';
    const Info = 'Info';
    const Metadata = 'Metadata';

    const endstream = 'endstream';
    const stream = 'stream';

    const XML = 'XML';

    // + | FILTER
    const ASCIIHexDecode = 'ASCIIHexDecode';
    const ASCII85Decode  = 'ASCII85Decode';
    const LZWDecode  = 'LZWDecode'; 
    const FlateDecode  = 'FlateDecode';
    const RunLengthDecode  = 'RunLengthDecode';
    const CCITTFaxDecode  = 'CCITTFaxDecode';
    const JBIG2Decode  = 'JBIG2Decode';
    const DCTDecode  = 'DCTDecode';
    const JPXDecode  = 'JPXDecode';
    const Crypt  = 'Crypt';
    const DecodeParms  = 'DecodeParms';


    const FT_HELVETICA = 'Helvetica';

    const Transparency = 'Transparency';
    const S = 'S';
    const CS = 'CS';
}