# AI Novel Generator

An automated novel writing system that generates a new chapter every day using AI and automatically pushes it to GitHub.

## Features

- Daily chapter generation using OpenAI's GPT-3.5 Turbo
- Automatic GitHub integration for version control
- Rate limit handling and error recovery
- Scheduled execution via cron jobs

## Setup

1. Install dependencies:
```bash
composer install
```

2. Configure environment variables in `.env`:
- OpenAI API key
- GitHub token
- GitHub repository details

3. Set up cron job to run daily:
```bash
0 9 * * * /usr/bin/php /path/to/generate_chapter.php
```

## Directory Structure

- `generate_chapter.php` - Main script that generates and pushes chapters
- `novel_generator.php` - Handles AI text generation
- `github_handler.php` - Manages GitHub integration
- `config.php` - Configuration and environment setup
- `chapters/` - Generated chapter files

## Requirements

- PHP 7.4 or higher
- Composer
- OpenAI API key
- GitHub account and personal access token
