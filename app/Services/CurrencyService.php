<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected string $baseCurrency = 'egp';
    protected int $cacheDuration = 30;
    protected string $date = 'latest';

    public function getCurrencies(): array
    {
        return Cache::remember('currencies_' . $this->baseCurrency."_". $this->date, now()->addMinutes($this->cacheDuration), function () {
            $response = Http::get($this->getURL());

            if ($response->failed()) {
                throw new \Exception("Failed to fetch \"" . strtoupper($this->baseCurrency) . "\" currency data");
            }

            return $response->json();
        });
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): void
    {
        $this->baseCurrency = $baseCurrency;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getURL(): string
    {
        return "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@{$this->date}/v1/currencies/{$this->baseCurrency}.json";
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency, string $date = "latest"): float
    {
        $this->setDate($date);
        $this->setBaseCurrency($fromCurrency);

        $currencies = $this->getCurrencies();
        if (!isset($currencies[$fromCurrency]) || !isset($currencies[$fromCurrency][$toCurrency])) {
            throw new \Exception("Exchange rate from {$fromCurrency} to {$toCurrency} not found.");
        }

        return (float) $currencies[$fromCurrency][$toCurrency];
    }

    public function getHistoricalRates(string $fromCurrency, string $toCurrency, int $days = 7): array
    {
        $rates = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            try {
                $rate = $this->getExchangeRate($fromCurrency, $toCurrency, $date);
                $rates[$date] = $rate;
            } catch (\Exception $e) {
                $rates[$date] = null; // or handle the error as needed
            }
        }

        return $rates;
    }
}
