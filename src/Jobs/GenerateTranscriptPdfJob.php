<?php

namespace Illimi\Academics\Jobs;

use Illimi\Academics\Events\TranscriptGenerated;
use Illimi\Academics\Models\Transcript;
use Illimi\Academics\Support\TranscriptPdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

class GenerateTranscriptPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $transcriptId)
    {
    }

    public function handle(TranscriptPdfGenerator $generator): void
    {
        $transcript = Transcript::find($this->transcriptId);

        if (! $transcript) {
            return;
        }

        $filePath = $generator->generate($transcript);

        $transcript->update([
            'file_path' => $filePath,
            'generated_at' => now(),
        ]);

        Event::dispatch(new TranscriptGenerated($transcript));
    }
}
