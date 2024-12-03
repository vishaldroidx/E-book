<?php
require_once 'config.php';

class NovelGenerator {
    private $api_key;
    private $retry_count = 0;
    
    public function __construct() {
        $this->api_key = OPENAI_API_KEY;
    }
    
    public function generateChapter($chapterNumber) {
        $prompt = $this->generateChapterPrompt($chapterNumber);
        return $this->makeApiRequestWithRetry($prompt);
    }

    private function generateChapterPrompt($chapterNumber) {
        return [
            [
                'role' => 'system',
                'content' => 'You are a creative novel writer specializing in fantasy fiction. Write engaging, well-structured chapters that advance the overall story.'
            ],
            [
                'role' => 'user',
                'content' => "Write chapter {$chapterNumber} of an engaging fantasy novel. The chapter should be approximately " . 
                            WORDS_PER_CHAPTER . " words long. Include character development, vivid descriptions, and advance the plot."
            ]
        ];
    }
    
    private function makeApiRequestWithRetry($messages) {
        while ($this->retry_count < MAX_RETRIES) {
            try {
                return $this->makeApiRequest($messages);
            } catch (Exception $e) {
                $this->retry_count++;
                $this->handleApiError($e);
            }
        }
        throw new Exception("Failed to generate chapter after " . MAX_RETRIES . " attempts");
    }
    
    private function makeApiRequest($messages) {
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 2500,
            'temperature' => 0.8
        ];
        
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        if ($http_code !== 200) {
            $error = json_decode($response, true);
            throw new Exception('API Error: ' . ($error['error']['message'] ?? 'Unknown error'));
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'];
    }
    
    private function handleApiError($error) {
        $message = $error->getMessage();
        error_log("API Error: " . $message);
        
        if (stripos($message, 'rate limit') !== false) {
            error_log("Rate limit hit, waiting " . RETRY_DELAY . " seconds before retry");
            sleep(RETRY_DELAY);
        } else {
            throw $error;
        }
    }
}
