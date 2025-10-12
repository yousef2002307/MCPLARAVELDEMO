<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\AnalysisCurrencyPrompt;
use App\Mcp\Resources\CurrencyMCPFAQResource;
use App\Mcp\Tools\CurrencyTool;
use App\Mcp\Tools\StreamTool;
use Laravel\Mcp\Server;

class CurrencyServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Currency Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        The MCP Server retrieves real-time currency exchange rates.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        CurrencyTool::class,
        StreamTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        CurrencyMCPFAQResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        AnalysisCurrencyPrompt::class,
    ];
}
