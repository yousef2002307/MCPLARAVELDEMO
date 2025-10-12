# 🚀 Laravel MCP Demo

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![MCP](https://img.shields.io/badge/MCP-0.3.0-blue?style=flat)](https://modelcontextprotocol.io)

A practical demonstration of building and integrating an **MCP Server** (Model Context Protocol) in Laravel 12. This project shows how to create an API for AI agents, enabling your Laravel application to provide tools, resources, and context that AI systems can intelligently interact with.

Think of it as building an API — but specifically designed for AI agents to understand and use your application's capabilities.

---

## 📖 What is MCP?

The **Model Context Protocol (MCP)** is a new standard that allows AI agents to connect with your applications. Instead of just building traditional REST APIs, MCP lets you expose:

- **Tools** - Actions the AI can perform (like currency conversion)
- **Resources** - Data the AI can access (like FAQ documents)
- **Prompts** - Pre-defined workflows for complex tasks (like currency analysis)

This demo implements a **Currency Exchange MCP Server** that provides real-time exchange rates and analytical insights to AI agents.

---

## ✨ Features

### 🛠️ MCP Tools
- **Currency Exchange Tool** - Real-time conversion between currencies (e.g., XAU to EGP)
- **Stream Tool** - Demonstrates streaming responses

### 📚 MCP Resources
- **Currency FAQ Resource** - Comprehensive Q&A document about currency exchange operations
- Accessible via URI: `currencies://resources/faq`

### 💡 MCP Prompts
- **Analysis Currency Assistant** - Analyzes 10-day historical trends
- Provides insights on uptrends, downtrends, and market movements
- Generates forecasts based on historical data

---

## 🏗️ Architecture

```
app/
├── Mcp/
│   ├── Servers/
│   │   └── CurrencyServer.php       # Main MCP server definition
│   ├── Tools/
│   │   ├── CurrencyTool.php         # Currency conversion tool
│   │   └── StreamTool.php           # Streaming response demo
│   ├── Resources/
│   │   └── CurrencyMCPFAQResource.php  # FAQ resource
│   └── Prompts/
│       └── AnalysisCurrencyPrompt.php  # Currency analysis prompt
├── Services/
│   └── CurrencyService.php          # Core currency logic
```

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or your preferred database)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-mcp-demo
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations**
   ```bash
   php artisan migrate
   ```

5. **Install Passport (OAuth for MCP authentication)**
   ```bash
   php artisan passport:install
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

---

## 🔧 Usage

### Accessing the MCP Server

The Currency MCP Server is registered at:
```
GET /mcp/currency
```

**Authentication**: Requires OAuth authentication via Laravel Passport (`auth:api` middleware)

### Example Tool: Currency Conversion

The `get-currency-exchange-rate` tool accepts:

```json
{
  "fromCurrency": "XAU",
  "toCurrency": "EGP",
  "amount": 1
}
```

Response:
```
"1 XAU is equal to 85,234.5678 EGP."
```

### Example Prompt: Currency Analysis

The `analysis-currency-assistant` prompt analyzes historical trends:

```json
{
  "firstCurrency": "USD",
  "secondCurrency": "EGP"
}
```

Returns a detailed analysis with:
- Trend identification (uptrend/downtrend/sideways)
- Market insights
- Short-term forecast

### Example Resource: FAQ

Access comprehensive FAQ documentation:
```
URI: currencies://resources/faq
```

---

## 🎯 How It Works

### 1. **Currency Service**
The `CurrencyService` class handles all currency operations:
- Fetches real-time rates from [Fawaz Ahmed Currency API](https://github.com/fawazahmed0/currency-api)
- Caches results for 30 minutes to optimize performance
- Provides historical rate analysis (7-10 day trends)

### 2. **MCP Server Registration**
In `routes/ai.php`:
```php
Mcp::web('/currency', CurrencyServer::class)
    ->middleware(['auth:api']);
```

### 3. **Tool Implementation**
Each tool extends `Laravel\Mcp\Server\Tool` and defines:
- **Name**: Unique identifier
- **Description**: What the tool does
- **Schema**: Input parameters (with validation)
- **Handle**: Business logic

### 4. **OAuth Security**
MCP routes are protected with Laravel Passport OAuth, ensuring only authenticated AI agents can access your tools.

---

## 📝 Key Concepts

### Tools vs Resources vs Prompts

| Component | Purpose | Example |
|-----------|---------|---------|
| **Tool** | Executable actions | Currency conversion, data updates |
| **Resource** | Static/dynamic data | FAQ docs, configuration files |
| **Prompt** | AI workflows | Analysis assistants, report generators |

### Caching Strategy

The service uses Laravel's caching to:
- Store exchange rates for 30 minutes
- Reduce external API calls
- Improve response times

```php
Cache::remember('currencies_egp_latest', 30, function() {
    return Http::get($this->getURL())->json();
});
```

---

## 🔐 Authentication

This demo uses **Laravel Passport** for OAuth2 authentication. MCP clients need to:

1. Register as an OAuth client
2. Obtain an access token
3. Include token in `Authorization: Bearer <token>` header

---

## 🛣️ Routing

MCP routes are defined in `routes/ai.php`:

```php
// OAuth routes for client registration
Mcp::oauthRoutes();

// Currency MCP Server
Mcp::web('/currency', CurrencyServer::class)
    ->middleware(['auth:api']);
```

---

## 📊 Tech Stack

- **Laravel 12** - Framework
- **Laravel MCP** - Model Context Protocol implementation
- **Laravel Passport** - OAuth2 authentication
- **Laravel Jetstream** - Application scaffolding
- **Livewire** - Dynamic interfaces
- **Tailwind CSS** - Styling
- **SQLite** - Database

---

## 📚 Learn More

- [Model Context Protocol Documentation](https://modelcontextprotocol.io)
- [Laravel MCP Package](https://github.com/laravel/mcp)
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Fawaz Ahmed Currency API](https://github.com/fawazahmed0/currency-api)

---

## 🤝 Contributing

Contributions are welcome! This is a demo project meant for learning and experimentation.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Acknowledgments

- Laravel team for the amazing framework and MCP package
- Fawaz Ahmed for the free currency API
- The MCP community for driving AI integration standards

---

## 💬 Questions?

Check out the included FAQ resource at `storage/currencies_mcp_fqa.md` for detailed answers about:
- MCP architecture and design patterns
- Currency exchange implementation details
- Caching strategies and performance optimization
- AI integration and forecasting capabilities

---

**Happy Building! 🎉**

*This demo shows how easy it is to build AI-ready applications with Laravel 12 and MCP. Start exposing your application's capabilities to AI agents today!*
