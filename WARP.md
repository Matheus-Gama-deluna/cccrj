# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is the CCCRJ (Centro de Comércio do Café do Rio de Janeiro) web system - a modern platform for coffee commerce information, historical content, and institutional resources. The system combines contemporary web development with rich historical content preservation.

## Architecture

### Technology Stack
- **Frontend**: HTML5, Tailwind CSS, vanilla JavaScript (ES6+)
- **Backend**: PHP 8+ (pure PHP, no frameworks)
- **Data Storage**: JSON files (no database required)
- **Web Server**: Apache via XAMPP
- **Architecture Pattern**: Component-based SPA with modular JavaScript components

### Core Design Patterns
- **Component-Based Architecture**: Each feature is a self-contained JavaScript class
- **JSON-First Data**: All content stored as JSON files instead of database
- **API-Driven**: RESTful PHP endpoints serving JSON data
- **Responsive Design**: Mobile-first approach with Tailwind CSS

### Directory Structure
```
cccrj/
├── api/                    # Backend API endpoints
│   ├── config/            # Database config (legacy)
│   ├── models_json/       # JSON data models
│   ├── legacy_models/     # Legacy database models
│   └── json/              # JSON API endpoints
├── assets/
│   ├── css/              # Stylesheets
│   └── js/
│       └── components/    # Modular JS components
├── data/                  # JSON data files
├── scraping_cccrj/        # Scraped historical content
├── cache/                 # System cache
├── uploads/               # File uploads
├── remove/                # Legacy/deprecated files
└── docs/                  # Project documentation
```

## Core Components

### JavaScript Components (assets/js/components/)
- `news.js` - News management with real-time updates
- `reports.js` - PDF reports with FTP integration
- `archive.js` - Digital archive with pagination and search
- `clipping.js` - Historical clipping content
- `publications.js` - Magazines and bulletins
- `history.js` - Interactive timeline
- `about.js` - Institutional information
- `crmc.js` - Coffee Reference and Memory Center
- `calculator.js` - Coffee conversion calculator
- `quotes.js` - Real-time coffee quotes

Each component follows this pattern:
```javascript
class ComponentManager {
    constructor() {
        this.apiUrl = 'api/json/component/endpoint.php';
        this.init();
    }
    
    async loadData() { /* API calls */ }
    render() { /* DOM manipulation */ }
    bindEvents() { /* Event handling */ }
}
```

### API Structure (api/json/)
- RESTful endpoints organized by feature
- JSON responses with consistent structure
- Error handling and validation
- Cache integration for performance

### Data Architecture
- JSON files in `data/` directory for content
- Metadata files for indexing and search
- Historical content preserved from original site
- No database dependencies - pure file-based storage

## Development Commands

### Environment Setup
```bash
# Start XAMPP services (Apache and MySQL if needed)
# Access XAMPP control panel or:
net start apache2.4
net start mysql80

# Navigate to project
http://localhost/cccrj/
```

### Development Server
```bash
# The system runs on Apache via XAMPP
# Main application: http://localhost/cccrj/
# Admin panel: http://localhost/cccrj/admin.html
# Login page: http://localhost/cccrj/login.html
```

### Content Management
```bash
# Extract directory structure
php extrair_diretorias.php

# Content is managed via JSON files in data/ directory
# No database setup required
```

### Testing and Validation
```bash
# Test PHP configuration
php api/test_php.php

# Validate JSON data structure
php -f api/json/validate_structure.php

# Check file permissions and paths
dir /s *.json
dir /s *.php
```

### File Operations
```bash
# Common file operations for this project
# View JSON data files
type data\news.json
type data\content\clipping.json

# Check logs
type cache\last_update.txt
type logs\*.log

# Backup important data
xcopy data data_backup /E /I
xcopy api api_backup /E /I
```

## Key Architectural Patterns

### Component Loading Strategy
Components are conditionally loaded based on DOM presence:
```javascript
// In main.js
if (document.getElementById('news-container')) {
    new NewsManager();
}
```

### API Response Pattern
All APIs return consistent JSON structure:
```json
{
    "success": true,
    "data": [...],
    "hasMore": boolean,
    "total": number,
    "message": "string"
}
```

### Data Flow Architecture
1. **Frontend Component** requests data via fetch()
2. **PHP API Endpoint** reads JSON files
3. **JSON Data Files** serve as data source
4. **Cache Layer** optimizes repeated requests
5. **Response** formatted as JSON back to component

### Historical Content Integration
- Original CCCRJ website content scraped and preserved in `scraping_cccrj/`
- Content transformed to structured JSON format
- Maintains historical integrity while enabling modern features

## Content Types

### Primary Content Areas
- **Institutional**: About CCCRJ, history, statutes
- **Publications**: Magazines, bulletins, technical reports
- **News**: Current coffee industry news
- **Archive**: Historical documents and photos
- **CRMC**: Coffee Reference and Memory Center content
- **Services**: Quotes, calculator, reports

### Data Organization
- Each content type has dedicated JSON files
- Metadata separation for efficient querying
- Hierarchical organization for complex content
- Cross-references maintained between related content

## Development Notes

### No Database Required
This system intentionally avoids database complexity:
- All data stored as JSON files
- Faster deployment and maintenance
- No SQL server dependencies
- Version control friendly data format

### Component Independence
Each JavaScript component is self-contained:
- No external dependencies (except Tailwind CSS)
- Modular loading based on page requirements
- Independent API endpoints per component
- Isolated error handling

### Historical Content Preservation
Special attention to maintaining historical accuracy:
- Original content structure preserved
- Metadata for provenance tracking
- Multiple content formats supported
- Cultural heritage considerations

### Performance Considerations
- Lazy loading of components
- JSON file caching strategies
- Pagination for large datasets
- Optimized asset loading

This architecture enables rapid development while preserving the rich historical content of CCCRJ in a modern, maintainable web platform.