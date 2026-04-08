<?php

namespace Illimi\Academics\Support;

use Illimi\Academics\Models\Transcript;
use Illuminate\Support\Facades\Storage;

class TranscriptPdfGenerator
{
    public function generate(Transcript $transcript): string
    {
        $path = 'transcripts/'.$transcript->id.'.pdf';

        $payload = json_encode($transcript->payload, JSON_PRETTY_PRINT);

        Storage::disk('local')->put($path, $payload ?: '');

        return $path;
    }
}
