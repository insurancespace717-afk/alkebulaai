<?php
/**
 * AIProvider.php - Centralized LLM API calls for Alkebulan AI
 * Supports Grok/xAI (OpenAI-compatible endpoint) and fallback
 * Uses cURL for zero dependencies
 */

class AIProvider {
    private static $apiKey = null;
    private static $baseUrl = 'https://api.x.ai/v1';
    private static $defaultModel = 'grok-beta'; // or 'grok-4.20-beta-latest' etc. - check https://docs.x.ai
    private static $temperature = 0.7;
    private static $maxTokens = 1024;

    /**
     * Load config once (from DB, OSSN setting, or hardcoded for testing)
     */
    private static function loadConfig() {
        if (self::$apiKey !== null) return;

        // Preferred: Load from OSSN plugin setting or alk eb ulan_config table
        self::$apiKey = ossn_get_plugin_setting('grok_api_key', 'Alkebulan');
        
        // Fallback for testing - REMOVE BEFORE COMMIT/PRODUCTION!
        // self::$apiKey = 'gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        if (empty(self::$apiKey)) {
            throw new Exception("Grok API key not configured. Set it in admin settings.");
        }
    }

    /**
     * Make a chat completion request
     * @param array $messages Array of ['role' => 'system/user/assistant', 'content' => '...']
     * @param string $model Optional model override
     * @return string Generated response
     */
    public static function chat(array $messages, $model = null, $temperature = null, $maxTokens = null) {
        self::loadConfig();

        $payload = [
            'model' => $model ?? self::$defaultModel,
            'messages' => $messages,
            'temperature' => $temperature ?? self::$temperature,
            'max_tokens' => $maxTokens ?? self::$maxTokens,
        ];

        $ch = curl_init(self::$baseUrl . '/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::$apiKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Grok API error: HTTP $httpCode - $response");
            throw new Exception("AI service error (code $httpCode)");
        }

        $data = json_decode($response, true);
        return trim($data['choices'][0]['message']['content'] ?? 'No response received.');
    }

    /**
     * Quick helper: Generate text with a system prompt
     */
    public static function generate($prompt, $system = "You are Alkebulan AI – helpful, truthful, focused on African knowledge, creativity and empowerment.") {
        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $prompt]
        ];
        return self::chat($messages);
    }
}
