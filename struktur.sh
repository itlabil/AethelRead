#!/bin/bash

# Array daftar folder yang akan dibuat
folders=(
    ".github/ISSUE_TEMPLATE"
    ".github/workflows"
    "ai"
    "android"
    "backend"
    "docker"
    "docs/core"
    "docs/architecture"
    "docs/platform"
    "docs/development"
    "docs/assets"
    "resources/fonts"
    "resources/icons"
    "resources/logos"
    "resources/references"
    "resources/screenshots"
    "scripts"
)

# Array daftar file yang akan dibuat
files=(
    ".github/PULL_REQUEST_TEMPLATE.md"
    "ai/CLAUDE.md"
    "ai/CHATGPT.md"
    "ai/Backend.md"
    "ai/Android.md"
    "ai/Admin.md"
    ".editorconfig"
    ".env.example"
    ".gitattributes"
    ".gitignore"
    "LICENSE"
    "README.md"
)

echo "📂 Mulai membuat struktur folder..."
for folder in "${folders[@]}"; do
    # mkdir -p otomatis skip jika folder sudah ada
    mkdir -p "$folder"
done

echo "📄 Mulai membuat file kosong..."
for file in "${files[@]}"; do
    # Cek apakah file sudah ada, jika belum baru dibuat
    if [ ! -f "$file" ]; then
        touch "$file"
        echo "   [Dibuat] $file"
    else
        echo "   [Skip]   $file (Sudah ada)"
    fi
done

echo "✅ Selesai! Struktur aethel-read berhasil disiapkan."