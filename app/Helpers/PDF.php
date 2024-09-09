<?php

namespace App\Helpers;

use Mpdf\Mpdf;

class PDF extends \Mpdf\Mpdf
{
    public $mpdf;
    public function __construct()
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'muol' => [
                    'R' => 'Moul/Moul-Regular.ttf',
                    'useOTL' => 0xFF
                ],

                'reap' => [
                    'R' => 'Siemreap/KhmerOSSiemreap.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75
                ],
            ],
            // 'default_font' => 'khmerOS'
            'default_font' => 'reap'
        ]);
        //
        $stylesheet = file_get_contents(public_path('css' . DIRECTORY_SEPARATOR . 'pdf.css'));
        $this->mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
    }
    public function generate($doc, $watermark = false, $watermark_text = null)
    {
        if ($watermark) {
            $this->mpdf->showWatermarkText = true;
            $this->mpdf->SetWatermarkText($watermark_text);
        }
        $this->mpdf->WriteHTML($doc, \Mpdf\HTMLParserMode::HTML_BODY);
        //        $this->mpdf->SetHTMLFooter('
        //                                            <table width="100%">
        //                                                <tr>
        //                                                    <td width="33%">{DATE j-m-Y}</td>
        //                                                    <td width="33%" align="center">{PAGENO}/{nbpg}</td>
        //                                                    <td width="33%" style="text-align: right;">IDEV-GROUP</td>
        //                                                </tr>
        //                                            </table>
        //        ');
        return $this->mpdf->output();
    }
}
