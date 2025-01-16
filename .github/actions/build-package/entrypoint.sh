#!/usr/bin/env bash

set -ex

TAG="$1"
[ -z $TAG ]  && echo "[!] Please Specific the TAG as argument #1" && exit 255

TMP=$(mktemp -d)
echo "[i] Creating Temp Workspace at $TMP"
mkdir -p $TMP
cd $TMP

echo "[i] Configuring Remote"
git init
git remote add -f origin https://github.com/bentideswell/magento2-wordpress-integration.git

echo "[i] Configuring Sparse Checkout"
git config core.sparseCheckout true
echo "wptheme/" >> .git/info/sparse-checkout

echo "[i] Pull Remote"
git pull origin tags/$TAG

echo "[i] Generating Theme Hash"
HASHES=""
for file in $(find wptheme -type f | sort); do
    FILE_NAME_HASH=$(echo -n $file | sed -E 's#wptheme/##' | sed -E 's#.sample##' | md5 -q)
    FILE_HASH=$(md5 -q $file)
    HASHES+="$FILE_NAME_HASH::$FILE_HASH"
    echo "$file => $FILE_NAME_HASH::$FILE_HASH"
done
BUILT_HASH=$(echo -n $HASHES | md5)
echo "hash=$BUILT_HASH" >> $GITHUB_OUTPUT

echo "[i] Updating embedded hash in files"
for file in $(grep -rl "{REMOTE_HASH}" wptheme); do
  echo "  - $file"
  sed -i "s/{REMOTE_HASH}/$(echo -n $BUILT_HASH)/g" $file
done

echo "[i] Moving Sample Files"
for file in $(find wptheme -type f -name '*.sample'); do
  mv $file $(echo $file | sed 's/.sample//')
done

echo "[i] Write composer.json"
jq ".version = \"$TAG\" | ." /composer.sample.json > wptheme/composer.json

echo "[i] Moving built files to Github Workspace"
rsync -avz --exclude '.git' --exclude '.github' $TMP/wptheme/ $GITHUB_WORKSPACE
