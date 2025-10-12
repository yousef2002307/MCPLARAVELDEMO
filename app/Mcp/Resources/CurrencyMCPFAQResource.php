<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class CurrencyMCPFAQResource extends Resource
{
    /**
     * The resource's description.
     */
    protected string $description = <<<'MARKDOWN'
    This resource is designed to answer questions related to the Currencies MCP, providing detailed insights, explanations, and examples about how the currency exchange and analysis module operates and integrates within the system.
    MARKDOWN;

    /**
     * The resource's URI.
     */
    protected string $uri = 'currencies://resources/faq';

    /**
     * The resource's MIME type.
     */
    protected string $mimeType = 'text/markdown';

    /**
     * Handle the resource request.
     */
    public function handle(): Response
    {
        return Response::blob(file_get_contents(storage_path('currencies_mcp_fqa.md')));
    }
}
