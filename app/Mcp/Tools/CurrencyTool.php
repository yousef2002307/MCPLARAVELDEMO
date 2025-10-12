<?php

namespace App\Mcp\Tools;

use App\Services\CurrencyService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;

#[IsOpenWorld]
class CurrencyTool extends Tool
{
    protected string $name = 'get-currency-exchange-rate';

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Retrieves real-time currency exchange rates to convert values such as XAU (gold ounce) to the Egyptian Pound (EGP).
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, CurrencyService $currencyService): Response
    {
        $validated = $request->validate([
            'fromCurrency' => 'required|string|min:3|max:4',
            'toCurrency' => 'required|string|min:3|max:4',
            'amount' => 'required|numeric|min:0',
        ]);
        try {
            $exchangeRate = $currencyService->getExchangeRate(strtolower($validated['fromCurrency']), strtolower($validated['toCurrency']));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
        $convertedAmount = number_format($validated['amount'] * $exchangeRate, 4);

        return Response::text("{$validated['amount']} {$validated['fromCurrency']} is equal to {$convertedAmount} {$validated['toCurrency']}.");
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            // amount
            'amount' => $schema->number()->description('The amount of the currency to convert.'),
            // fromCurrency
            'fromCurrency' => $schema->string()->description('The currency code to convert from, e.g., XAU for gold ounce.'),
            // toCurrency
            'toCurrency' => $schema->string()->description('The currency code to convert to, e.g., EGP for Egyptian Pound.'),
        ];
    }

    public function shouldRegister(Request $request): bool
    {
        return true;
    }
}
