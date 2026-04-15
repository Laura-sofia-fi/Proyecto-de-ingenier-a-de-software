<?php

namespace App\Support;

class SimplePdf
{
    public static function download(string $title, array $lines, string $filename)
    {
        $content = static::build($title, $lines);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => strlen($content),
        ]);
    }

    public static function build(string $title, array $lines): string
    {
        $streamLines = array_merge([$title, ''], $lines);
        $y = 800;
        $content = "BT\n/F1 16 Tf\n50 {$y} Td\n(".static::escape($title).") Tj\n";
        $y -= 28;
        $content .= "/F1 11 Tf\n";

        foreach ($streamLines as $line) {
            if ($line === $title) {
                continue;
            }

            foreach (static::wrap($line, 95) as $wrapped) {
                $content .= "1 0 0 1 50 {$y} Tm\n(".static::escape($wrapped).") Tj\n";
                $y -= 16;
            }

            if ($y < 60) {
                break;
            }
        }

        $content .= "ET";

        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[] = "<< /Length ".strlen($content)." >>\nstream\n{$content}\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    protected static function wrap(string $line, int $length): array
    {
        if ($line === '') {
            return [' '];
        }

        return str_split(wordwrap($line, $length, "\n", true), $length + 1);
    }

    protected static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $text);
    }
}
