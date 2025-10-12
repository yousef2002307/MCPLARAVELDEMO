<?php

namespace App\Mcp\Prompts;

use App\Services\CurrencyService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class AnalysisCurrencyPrompt extends Prompt
{

    /**
     * The prompt's name.
     */
    protected string $name = 'analysis-currency-assistant';

    /**
     * The prompt's title.
     */
    protected string $title = 'Analysis Currency Assistant Prompt';

    /**
     * The prompt's description.
     */
    protected string $description = <<<'MARKDOWN'
    Analysis Currency exchange rates such as XAU (gold ounce) to the Egyptian Pound (EGP).
    MARKDOWN;

    /**
     * Handle the prompt request.
     */
    public function handle(Request $request, CurrencyService $currencyService): array
    {
        $validated = $request->validate([
            'firstCurrency' => 'required|string|min:3|max:4',
            'secondCurrency' => 'required|string|min:3|max:4',
        ]);

        $firstCurrency = $validated['firstCurrency'];
        $secondCurrency = $validated['secondCurrency'];

        $historicalRates = $currencyService->getHistoricalRates($firstCurrency, $secondCurrency, 10);

        $prompt = "Analyze the exchange rate trend between {$firstCurrency} and {$secondCurrency} over the past 10 days based on the following data. Identify whether the currency is showing an uptrend, downtrend, or sideways movement, and provide insights into possible reasons for this movement (such as market sentiment, macroeconomic factors, or technical behavior). Finally, give a short forecast for the next few days.\nExchange rates for the last 10 days:";
        foreach ($historicalRates as $date => $rate) {
            $prompt .= "\n{$date}: 1 {$firstCurrency} = {$rate} {$secondCurrency}";
        }
        return [
            Response::text("You are a financial market analyst.")->asAssistant(),
            Response::text($prompt),
        ];
    }

    /**
     * Get the prompt's arguments.
     *
     * @return array<int, \Laravel\Mcp\Server\Prompts\Argument>
     */
    public function arguments(): array
    {
        return [
            // first currency
            new Argument(name: 'firstCurrency', description: 'The currency code to convert from, e.g., XAU for gold ounce.', required: true),
            // second currency
            new Argument(name: 'secondCurrency', description: 'The currency code to convert to, e.g., EGP for Egyptian Pound.', required: true),
        ];
    }
}
