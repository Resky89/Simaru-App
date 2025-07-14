<?php

namespace App\Http\Controllers\Traits;

use Barryvdh\DomPDF\Facade\Pdf;

trait ExportableToPdf
{
    /**
     * Generate PDF from view
     *
     * @param string $view Path to view template
     * @param array $data Data to pass to view
     * @param string $filename Name of the file to download
     * @param string $orientation Page orientation: 'portrait' or 'landscape'
     * @param string $paperSize Paper size, default 'a4'
     * @return mixed PDF download response
     */
    protected function generatePdf($view, $data, $filename, $orientation = 'portrait', $paperSize = 'a4')
    {
        $pdf = PDF::loadView($view, $data)
            ->setPaper($paperSize, $orientation);

        return $pdf->download($filename);
    }

    /**
     * Generate PDF from view and stream it
     *
     * @param string $view Path to view template
     * @param array $data Data to pass to view
     * @param string $filename Name of the file to stream
     * @param string $orientation Page orientation: 'portrait' or 'landscape'
     * @param string $paperSize Paper size, default 'a4'
     * @return mixed PDF stream response
     */
    protected function streamPdf($view, $data, $filename, $orientation = 'portrait', $paperSize = 'a4')
    {
        $pdf = PDF::loadView($view, $data)
            ->setPaper($paperSize, $orientation);

        return $pdf->stream($filename);
    }

    /**
     * Generate barcode as PDF
     *
     * @param string $view Path to barcode view template
     * @param array $data Data for barcode
     * @param string $filename Name of the file to download
     * @param string $orientation Page orientation
     * @return mixed PDF download response
     */
    protected function generateBarcodePdf($view, $data, $filename, $orientation = 'portrait')
    {
        $pdf = PDF::loadView($view, $data)
            ->setPaper([0, 0, 226.77, 85.04]) // 8x3 cm dimensions in points (1 cm = 28.3465 points)
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        return $pdf->download($filename);
    }

    /**
     * Generate QR code PDF with multiple items
     *
     * @param string $view Path to QR code view template
     * @param array $data Data for QR codes
     * @param string $filename Name of the file to download
     * @param string $orientation Page orientation
     * @param string $paperSize Paper size, default 'a4'
     * @return mixed PDF download response
     */
    protected function generateQrCodePdf($view, $data, $filename, $orientation = 'portrait', $paperSize = 'a4')
    {
        $pdf = PDF::loadView($view, $data)
            ->setPaper($paperSize, $orientation)
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        return $pdf->download($filename);
    }
}
