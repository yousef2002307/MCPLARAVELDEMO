<?php

use App\Mcp\Servers\CurrencyServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::oauthRoutes();

Mcp::web('/currency', CurrencyServer::class);

// Mcp::web('/currency', CurrencyServer::class)->middleware(['auth:api']);