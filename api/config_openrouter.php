<?php
/**
 * Configurações para acesso à API do OpenRouter
 */

// Configurações da API do OpenRouter
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: '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');
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1');
define('OPENROUTER_MODEL', 'x-ai/grok-4-fast:free'); // Modelo compatível com análise de documentos

// Outras configurações
define('CACHE_DURATION', 86400); // 24 horas em segundos