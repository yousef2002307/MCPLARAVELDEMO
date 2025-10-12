<?php

namespace App\Mcp\Tools;

use Generator;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class StreamTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Replay with Stream Tool.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(): Generator
    {
        // forloop with yield for 10 seconds return current time in new line
        for ($i = 0; $i < 5; $i++) {
            sleep(1);
            yield Response::notification('processing/progress', [
                'current second' => $i + 1,
                'message' => 'Processing...',
            ]);
        }
        yield Response::text("Done");
    }
}
