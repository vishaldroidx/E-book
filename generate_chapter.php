<?php
require_once 'config.php';
require_once 'novel_generator.php';
require_once 'github_handler.php';

try {
    // Create chapters directory if it doesn't exist
    if (!file_exists(CHAPTER_DIRECTORY)) {
        mkdir(CHAPTER_DIRECTORY, 0777, true);
    }
    
    // Get the current chapter number
    $chapterFiles = glob(CHAPTER_DIRECTORY . '/chapter_*.md');
    $chapterNumber = count($chapterFiles) + 1;
    
    // Generate new chapter
    $generator = new NovelGenerator();
    $content = $generator->generateChapter($chapterNumber);
    
    // Push to GitHub
    $github = new GitHubHandler();
    $result = $github->pushChapter($chapterNumber, $content);
    
    echo "Successfully generated and pushed Chapter $chapterNumber\n";
    
} catch (Exception $e) {
    error_log("Error generating chapter: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
