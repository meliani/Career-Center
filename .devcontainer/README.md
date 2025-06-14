# Career Center DevContainer

A fancy development container configuration for the Career Center Laravel application, inspired by your existing Docker setup.

## 🌟 Features

### 🐘 PHP Development Stack
- **PHP 8.3** with all necessary extensions
- **Composer** for dependency management
- **Xdebug** configured for debugging
- **Laravel Artisan** integration

### 🗄️ Database & Cache
- **MySQL 8.0** database
- **Redis** for caching and sessions
- **MailHog** for email testing

### 🎨 Frontend Development
- **Node.js 20** with NPM
- **Vite** for asset building
- **Tailwind CSS** support

### 🛠️ Development Tools
- **Oh My Zsh** with useful aliases
- **Git** with GitHub CLI
- **Docker-in-Docker** support
- **VS Code** extensions pre-installed

## 🚀 Quick Start

1. **Open in DevContainer**
   - Install the "Dev Containers" extension in VS Code
   - Open Command Palette (`Ctrl+Shift+P`)
   - Run "Dev Containers: Reopen in Container"

2. **Wait for Setup**
   - The container will build and configure automatically
   - Initial setup may take 5-10 minutes
   - Watch the terminal for progress

3. **Start Developing**
   - Your Laravel app will be available at `http://localhost`
   - MailHog interface at `http://localhost:8025`
   - All services are automatically started

## 📦 Included Extensions

### PHP & Laravel
- **Intelephense** - PHP language server
- **PHP Debug** - Xdebug integration
- **PHP Sniffer** - Code standards
- **PHPStan** - Static analysis
- **Laravel Blade** - Template support
- **Laravel Artisan** - Command integration

### Frontend & Assets
- **Tailwind CSS IntelliSense**
- **Prettier** - Code formatting
- **Auto Rename Tag**
- **Path Intellisense**

### Database
- **SQLTools** - Database management
- **MySQL Driver** - Database connectivity
- **Redis Client** - Cache management

### Productivity
- **GitLens** - Git supercharged
- **Error Lens** - Inline error display
- **Better Comments** - Enhanced comments
- **Thunder Client** - API testing

## 🔧 Configuration

### Environment Variables
The devcontainer automatically configures:
- `APP_ENV=local`
- `APP_DEBUG=true`
- `XDEBUG_MODE=develop,debug`
- Full database and Redis connections

### Port Forwarding
| Port | Service | Description |
|------|---------|-------------|
| 80 | Nginx | Laravel Application |
| 3306 | MySQL | Database |
| 6379 | Redis | Cache |
| 8025 | MailHog | Email Interface |
| 9003 | Xdebug | Debugging |

### VS Code Tasks
Pre-configured tasks available via `Ctrl+Shift+P` → "Tasks: Run Task":
- **Laravel: Serve** - Start development server
- **Laravel: Migration** - Run migrations
- **Laravel: Fresh Migration with Seed** - Reset database
- **Laravel: Clear Cache** - Clear all caches
- **Laravel: Run Tests** - Execute test suite
- **NPM: Development Build** - Build assets
- **NPM: Watch** - Watch for changes
- **Laravel Pint: Format Code** - Code formatting
- **PHPStan: Analyze** - Static analysis

## 🎯 Debugging

### Xdebug Setup
1. Set breakpoints in your PHP code
2. Press `F5` or go to "Run and Debug"
3. Select "Listen for Xdebug"
4. Access your Laravel application
5. Debugging will start automatically

### Debug Configuration
- **Port**: 9003
- **Path Mapping**: `/var/www/html` ↔ `${workspaceFolder}`
- **IDE Key**: `laravel`

## 🔑 Useful Aliases

The devcontainer includes helpful shell aliases:

### Laravel
- `art` - `php artisan`
- `tinker` - `php artisan tinker`
- `migrate` - `php artisan migrate` 
- `fresh` - `php artisan migrate:fresh --seed`
- `test` - `php artisan test`
- `pint` - `./vendor/bin/pint`
- `stan` - `./vendor/bin/phpstan analyse`

### Docker
- `dc` - `docker-compose`
- `dcd` - `docker-compose -f docker-compose.dev.yml`
- `dcl` - `docker-compose logs -f`

### Git
- `gs` - `git status`
- `ga` - `git add`
- `gc` - `git commit`
- `gp` - `git push`

### NPM
- `nrd` - `npm run dev`
- `nrb` - `npm run build`
- `nrw` - `npm run watch`

## 🔍 Troubleshooting

### Container Won't Start
1. Check Docker Desktop is running
2. Ensure no port conflicts (80, 3306, 6379)
3. Try rebuilding: "Dev Containers: Rebuild Container"

### Database Connection Issues
1. Wait for services to fully start (30-60 seconds)
2. Check if database container is running
3. Verify environment variables in `.env`

### Permission Issues
1. The container runs as `www-data` user
2. Files are automatically chown'd during setup
3. Use `sudo` for system-level changes if needed

### Xdebug Not Working
1. Ensure Xdebug extension is loaded: `php -m | grep xdebug`
2. Check VS Code is listening on port 9003
3. Verify path mappings in launch.json

## 📚 Documentation

- [VS Code Dev Containers](https://code.visualstudio.com/docs/remote/containers)
- [Laravel Documentation](https://laravel.com/docs)
- [Docker Compose Reference](https://docs.docker.com/compose/)

## 🤝 Contributing

This devcontainer configuration is part of the Career Center project. For issues or improvements:

1. Check existing issues in the project repository
2. Create detailed bug reports or feature requests
3. Submit pull requests with clear descriptions

## 📝 Notes

- The devcontainer uses your existing `docker-compose.dev.yml` as the base
- Additional services and overrides are defined in `docker-compose.devcontainer.yml`
- Post-create and post-start scripts handle automatic setup
- All configurations respect your existing Docker architecture

---

**Happy coding with your fancy DevContainer! 🎉**
