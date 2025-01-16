#!/usr/bin/env bash

set -e

downstream="SamJUK/wp-theme-fishpig"
upstream="bentideswell/magento2-wordpress-integration"

echo "[i] Configure Git"
git config user.name "github-actions[bot]"
git config user.email "github-actions[bot]@users.noreply.github.com"

echo "[i] Pull latest base"
git pull origin master

echo "[i] Building docker image"
docker build -t samjuk/fishpig-theme-builder -f .github/actions/build-package/Dockerfile .github/actions/build-package

fetch_tags() { gh api -H "Accept: application/vnd.github+json" -H "X-GitHub-Api-Version: 2022-11-28" /repos/$1/tags --slurp --paginate | jq -r '.[] | .[] | .name' | sort -V; }
for tag in $(grep -vxf <(fetch_tags $downstream) <(fetch_tags $upstream)); do
    git switch -C $tag
    git reset --hard origin/master
    docker run -e GITHUB_OUTPUT=/github/workspace/hash -v .:/github/workspace samjuk/fishpig-theme-builder "$tag"
    git add .
    git commit -m "bot: released build artifacts for tag $tag ($(awk -F= '$1 == "hash" {print $2}' ./hash))"
    git tag $tag 
    git push origin tag $tag
    sleep 15 # To allow the releaser workflow, to run the releases in the correct order
done

echo "[i] Reset GIT"
git config --unset user.name
git config --unset user.email
git switch master