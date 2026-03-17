<?php
/**
 * GrokConfig.php - Configuration constants for external AI
 */

class GrokConfig {
    const API_BASE_URL = 'https://api.x.ai/v1';
    const DEFAULT_MODEL = 'grok-beta'; // Update when new models release
    const TEMPERATURE = 0.7;
    const MAX_TOKENS = 1024;

    // You can add more: proxy, timeout, etc.
}
