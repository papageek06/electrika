#!/bin/bash

# Activer le mode strict pour arrêter en cas d'erreur
set -e

# Définir le dossier du projet (où le script est exécuté)
PROJECT_DIR="$(pwd)"
GIT_REPO="https://github.com/papageek06/electrika.git"
COMPOSER_PATH="$PROJECT_DIR/../composer.phar"

echo "📍 Répertoire du projet : $PROJECT_DIR"

# Vérifier si le dossier contient déjà un dépôt Git
if [ -d "$PROJECT_DIR/.git" ]; then
    echo "📥 Le projet existe déjà, mise à jour avec Git pull..."
    cd "$PROJECT_DIR"
    git pull origin main
else
    echo "🆕 Le dossier existe mais n'est pas un repo Git."

    # Option 1 : Supprimer et re-cloner (⚠️ SUPPRIME TOUT)
    # echo "⚠️ Suppression du dossier existant et clonage du projet..."
    # rm -rf "$PROJECT_DIR"
    # git clone "$GIT_REPO" "$PROJECT_DIR"
    
    # Option 2 : Ajouter Git si nécessaire
    echo "📦 Initialisation Git et récupération du dépôt..."
    cd "$PROJECT_DIR"
    git init
    git remote add origin "$GIT_REPO"
    git fetch origin
    git checkout -t origin/main
fi

# Mise à jour des dépendances PHP
echo "📦 Mise à jour des dépendances PHP avec Composer..."
php "$COMPOSER_PATH" update --no-interaction --optimize-autoloader

# Construire les assets avec NPM
echo "⚙️  Construction des assets avec NPM..."
npm install
npm run build



# Exécuter les migrations
echo "🗄️  Migration de la base de données..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "✅ Déploiement terminé avec succès !"