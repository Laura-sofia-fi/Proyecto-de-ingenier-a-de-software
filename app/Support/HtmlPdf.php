<?php

namespace App\Support;

use Symfony\Component\Process\Process;

class HtmlPdf
{
    public static function fromHtml(string $html, string $filename): ?array
    {
        $edgePath = env('EDGE_PATH', 'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe');

        if (! is_file($edgePath)) {
            return null;
        }

        $tempDir = storage_path('app/pdf-temp');

        if (! is_dir($tempDir) && ! mkdir($tempDir, 0777, true) && ! is_dir($tempDir)) {
            return null;
        }

        $token = uniqid('invoice_', true);
        $htmlPath = $tempDir.DIRECTORY_SEPARATOR.$token.'.html';
        $pdfPath = $tempDir.DIRECTORY_SEPARATOR.$token.'.pdf';
        $profileDir = $tempDir.DIRECTORY_SEPARATOR.'edge-profile';

        file_put_contents($htmlPath, $html);

        if (! is_dir($profileDir)) {
            mkdir($profileDir, 0777, true);
        }

        $fileUrl = 'file:///'.str_replace('\\', '/', $htmlPath);

        $process = new Process([
            $edgePath,
            '--headless=new',
            '--disable-gpu',
            '--no-first-run',
            '--disable-crash-reporter',
            '--disable-breakpad',
            '--user-data-dir='.$profileDir,
            '--no-pdf-header-footer',
            '--print-to-pdf='.$pdfPath,
            $fileUrl,
        ]);

        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful() || ! is_file($pdfPath)) {
            @unlink($htmlPath);
            @unlink($pdfPath);

            return null;
        }

        $contents = file_get_contents($pdfPath);

        @unlink($htmlPath);
        @unlink($pdfPath);

        if ($contents === false) {
            return null;
        }

        return [
            'filename' => $filename,
            'contents' => $contents,
        ];
    }
}
