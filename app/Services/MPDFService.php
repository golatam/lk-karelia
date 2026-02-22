<?php

declare(strict_types=1);

namespace App\Services;

use PhpOffice\PhpWord\Writer\PDF\MPDF;

final class MPDFService extends MPDF
{
    protected function createExternalWriterInstance()
    {
        $mPdfClass = $this->getMPdfClassName();

        return new $mPdfClass([
            'tempDir' => \PhpOffice\PhpWord\Settings::getTempDir(),
        ]);
    }

    private function getMPdfClassName(): string
    {
        if ($this->includeFile != null) {
            // MPDF version 5.*
            return '\mpdf';
        }

        // MPDF version > 6.*
        return '\Mpdf\Mpdf';
    }
}
