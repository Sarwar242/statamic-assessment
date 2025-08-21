# Statamic Project Guide

## 1. Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- Web server (Apache/Nginx)
- Database (Optional - Statamic can work with flat files)

## 2. Installation Steps
1. Create a new Statamic project:
```bash
composer create-project statamic/statamic your-project-name
```

2. Set up environment:
- Copy .env.example to .env
- Generate application key
```bash
php artisan key:generate
```

3. Install dependencies:
```bash
composer install
npm install
```

## 3. Project Structure Explained

```
📁 Root
├── 📁 app/                    # Laravel application code
├── 📁 content/               # Statamic content files
│   ├── 📁 collections/      # Content collections (blog posts, etc.)
│   ├── 📁 globals/         # Global variables
│   ├── 📁 taxonomies/      # Classification systems
│   └── 📁 trees/           # Navigation structures
│
├── 📁 config/               # Configuration files
│   └── 📁 statamic/        # Statamic-specific configs
│
├── 📁 resources/           # Frontend assets
│   ├── 📁 blueprints/     # Content structure definitions
│   ├── 📁 views/          # Antlers templates
│   ├── 📁 css/           # Stylesheets
│   └── 📁 js/            # JavaScript files
│
├── 📁 public/              # Web root
├── 📁 storage/             # Cache and logs
└── 📁 users/               # User accounts
```

## 4. Key Concepts

1. **Collections**
- Located in collections
- Represent groups of related content (blog posts, products, etc.)
- Configured via YAML files

2. **Blueprints**
- Located in blueprints
- Define content structure and fields
- Control Panel form builders

3. **Templates**
- Located in views
- Use Antlers templating language
- Define how content is displayed

4. **Assets**
- Located in assets
- Manage media files
- Configurable image manipulation

## 5. Common Tasks

1. **Creating Collections**
```bash
php please make:collection my_collection
```

2. **Creating Templates**
```bash
php please make:template template_name
```

3. **Creating Blueprints**
```bash
php please make:blueprint blueprint_name
```

## 6. Development Workflow

1. Start the development server:
```bash
php artisan serve
```

2. Watch for asset changes:
```bash
npm run dev
```

3. Access the Control Panel:
- Visit: `http://localhost:8000/cp`
- create user to login: 
```bash
php artisan statamic:make:user
```

## 7. Best Practices

1. **Content Organization**
- Use collections for related content
- Implement taxonomies for classification
- Utilize globals for site-wide content

2. **Development**
- Keep templates modular
- Use partials for reusable components
- Follow Laravel conventions for custom functionality

3. **Performance**
- Enable static caching when possible
- Optimize assets
- Use image transformations

## 8. Deployment Checklist

1. Environment Setup
- Configure .env for production
- Set up proper permissions
- Configure web server

2. Performance Optimization
- Enable static caching
- Optimize assets
- Configure CDN if needed

3. Security
- Change default admin credentials
- Set up SSL
- Configure backup strategy

## 9. Useful Commands

```bash
# Clear cache
php artisan cache:clear

# Update system
composer update

# Generate assets
npm run build

# List all routes
php artisan route:list
```

This guide should give you a solid foundation for working with Statamic projects. The key is understanding that Statamic combines Laravel's powerful backend with a flexible flat-file CMS system. Remember that:

1. Content lives in the content directory as YAML/Markdown files
2. Templates go in views
3. Custom functionality can be added through Laravel's standard app structure
4. The Control Panel (`/cp`) is your friend for content management
