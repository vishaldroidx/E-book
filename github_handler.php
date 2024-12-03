<?php
require_once 'config.php';

class GitHubHandler {
    private $token;
    private $owner;
    private $repo;
    
    public function __construct() {
        $this->token = GITHUB_TOKEN;
        $this->owner = GITHUB_USERNAME;
        $this->repo = GITHUB_REPO;
    }
    
    public function pushChapter($chapterNumber, $content) {
        $filename = sprintf("chapter_%03d.md", $chapterNumber);
        $path = CHAPTER_DIRECTORY . '/' . $filename;
        
        // Format content with markdown
        $formattedContent = "# Chapter " . $chapterNumber . "\n\n" . $content;
        
        $data = [
            'message' => "Add chapter $chapterNumber",
            'content' => base64_encode($formattedContent)
        ];
        
        // Check if file exists to get SHA
        $existingFile = $this->getFileContent($path);
        if ($existingFile && isset($existingFile['sha'])) {
            $data['sha'] = $existingFile['sha'];
        }
        
        $ch = curl_init("https://api.github.com/repos/{$this->owner}/{$this->repo}/contents/{$path}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: token ' . $this->token,
            'User-Agent: PHP Script',
            'Accept: application/vnd.github.v3+json'
        ]);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Failed to push to GitHub: ' . curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    private function getFileContent($path) {
        $ch = curl_init("https://api.github.com/repos/{$this->owner}/{$this->repo}/contents/{$path}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: token ' . $this->token,
            'User-Agent: PHP Script',
            'Accept: application/vnd.github.v3+json'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
