# DevContainer-specific Dockerfile extending the development image
FROM career-center:dev AS devcontainer

# Install additional development tools for DevContainer
USER root

# Install additional system packages for development
RUN apk add --no-cache \
    bash \
    zsh \
    git \
    curl \
    wget \
    nano \
    vim \
    htop \
    tree \
    jq \
    make \
    openssh-client \
    rsync \
    shadow \
    sudo

# Configure sudo for www-data user
RUN echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Install Oh My Zsh for the www-data user
USER www-data

# Set up Oh My Zsh
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended || true

# Configure zsh with useful plugins and aliases
RUN if [ -d ~/.oh-my-zsh ]; then \
        sed -i 's/plugins=(git)/plugins=(git composer laravel docker docker-compose npm node)/g' ~/.zshrc; \
        echo '' >> ~/.zshrc; \
        echo '# Career Center Development Aliases' >> ~/.zshrc; \
        echo 'alias art="php artisan"' >> ~/.zshrc; \
        echo 'alias tinker="php artisan tinker"' >> ~/.zshrc; \
        echo 'alias migrate="php artisan migrate"' >> ~/.zshrc; \
        echo 'alias seed="php artisan db:seed"' >> ~/.zshrc; \
        echo 'alias fresh="php artisan migrate:fresh --seed"' >> ~/.zshrc; \
        echo 'alias serve="php artisan serve --host=0.0.0.0"' >> ~/.zshrc; \
        echo 'alias test="php artisan test"' >> ~/.zshrc; \
        echo 'alias pest="./vendor/bin/pest"' >> ~/.zshrc; \
        echo 'alias pint="./vendor/bin/pint"' >> ~/.zshrc; \
        echo 'alias stan="./vendor/bin/phpstan analyse"' >> ~/.zshrc; \
        echo '' >> ~/.zshrc; \
        echo '# Docker aliases' >> ~/.zshrc; \
        echo 'alias dc="docker-compose"' >> ~/.zshrc; \
        echo 'alias dcd="docker-compose -f docker-compose.dev.yml"' >> ~/.zshrc; \
        echo 'alias dcl="docker-compose logs -f"' >> ~/.zshrc; \
        echo 'alias dcp="docker-compose ps"' >> ~/.zshrc; \
        echo '' >> ~/.zshrc; \
        echo '# NPM aliases' >> ~/.zshrc; \
        echo 'alias nrd="npm run dev"' >> ~/.zshrc; \
        echo 'alias nrb="npm run build"' >> ~/.zshrc; \
        echo 'alias nrw="npm run watch"' >> ~/.zshrc; \
        echo '' >> ~/.zshrc; \
        echo '# Git aliases' >> ~/.zshrc; \
        echo 'alias gs="git status"' >> ~/.zshrc; \
        echo 'alias ga="git add"' >> ~/.zshrc; \
        echo 'alias gc="git commit"' >> ~/.zshrc; \
        echo 'alias gp="git push"' >> ~/.zshrc; \
        echo 'alias gl="git pull"' >> ~/.zshrc; \
        echo 'alias gb="git branch"' >> ~/.zshrc; \
        echo 'alias gco="git checkout"' >> ~/.zshrc; \
        echo '' >> ~/.zshrc; \
        echo '# Path additions' >> ~/.zshrc; \
        echo 'export PATH="$PATH:./vendor/bin"' >> ~/.zshrc; \
        echo 'export PATH="$PATH:./node_modules/.bin"' >> ~/.zshrc; \
    fi

# Also set up bash with the same aliases
RUN echo '' >> ~/.bashrc && \
    echo '# Career Center Development Aliases' >> ~/.bashrc && \
    echo 'alias art="php artisan"' >> ~/.bashrc && \
    echo 'alias tinker="php artisan tinker"' >> ~/.bashrc && \
    echo 'alias migrate="php artisan migrate"' >> ~/.bashrc && \
    echo 'alias seed="php artisan db:seed"' >> ~/.bashrc && \
    echo 'alias fresh="php artisan migrate:fresh --seed"' >> ~/.bashrc && \
    echo 'alias serve="php artisan serve --host=0.0.0.0"' >> ~/.bashrc && \
    echo 'alias test="php artisan test"' >> ~/.bashrc && \
    echo 'alias pest="./vendor/bin/pest"' >> ~/.bashrc && \
    echo 'alias pint="./vendor/bin/pint"' >> ~/.bashrc && \
    echo 'alias stan="./vendor/bin/phpstan analyse"' >> ~/.bashrc && \
    echo 'export PATH="$PATH:./vendor/bin"' >> ~/.bashrc && \
    echo 'export PATH="$PATH:./node_modules/.bin"' >> ~/.bashrc

# Set up development environment
USER root

# Create development directories
RUN mkdir -p /workspace/.devcontainer \
    && chown -R www-data:www-data /workspace

# Configure Git safe directory
RUN git config --global --add safe.directory /var/www/html && \
    git config --global --add safe.directory '*'

# Change www-data shell to zsh
RUN sed -i 's|/bin/false|/bin/zsh|g' /etc/passwd

# Create a custom entrypoint for DevContainer
COPY .devcontainer/devcontainer-entrypoint.sh /usr/local/bin/devcontainer-entrypoint.sh
RUN chmod +x /usr/local/bin/devcontainer-entrypoint.sh

# Create useful development scripts
RUN echo '#!/bin/bash' > /usr/local/bin/laravel-setup && \
    echo 'cd /var/www/html' >> /usr/local/bin/laravel-setup && \
    echo 'composer install' >> /usr/local/bin/laravel-setup && \
    echo 'npm install' >> /usr/local/bin/laravel-setup && \
    echo 'php artisan key:generate' >> /usr/local/bin/laravel-setup && \
    echo 'php artisan migrate' >> /usr/local/bin/laravel-setup && \
    echo 'php artisan storage:link' >> /usr/local/bin/laravel-setup && \
    echo 'npm run build' >> /usr/local/bin/laravel-setup && \
    chmod +x /usr/local/bin/laravel-setup

USER www-data
WORKDIR /var/www/html

# Override the default command for DevContainer usage
CMD ["/usr/local/bin/devcontainer-entrypoint.sh"]
