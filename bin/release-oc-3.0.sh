#!/usr/bin/env bash

# Exit if any command fails
set -eo pipefail

RELEASE_VERSION=$1

FILENAME_PREFIX="MultiSafepay_For_OpenCart_3.0.X_Release_"
RELEASE_FOLDER=".dist"
FOLDER_PREFIX="upload"

# If tag is not supplied, latest tag is used
if [ -z "$RELEASE_VERSION" ]
then
  RELEASE_VERSION=$(git describe --tags --abbrev=0)
fi

# Remove old folder
rm -rf "$RELEASE_FOLDER" &&

# Create release
mkdir "$RELEASE_FOLDER" &&
git archive --format zip -9 --output "$RELEASE_FOLDER"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER" &&
unzip "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
rm "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
composer install --no-dev &&

# Rename src to upload folder
mv src "$FOLDER_PREFIX" &&

# Remove unwanted filed in admin
rm "$FOLDER_PREFIX"/admin/view/template/extension/payment/multisafepay.tpl &&
rm "$FOLDER_PREFIX"/admin/view/template/extension/payment/multisafepay_order.tpl &&
rm -rf "$FOLDER_PREFIX"/admin/view/template/payment &&
rm -rf "$FOLDER_PREFIX"/admin/model/payment &&
rm -rf "$FOLDER_PREFIX"/admin/controller/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/de-de/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/es/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl/payment &&

# Remove unwanted filed in catalog
rm "$FOLDER_PREFIX"/catalog/view/theme/default/template/extension/payment/multisafepay.tpl &&
rm -rf "$FOLDER_PREFIX"/catalog/view/theme/default/template/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/model/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/controller/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it/payment &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl/payment &&

# Remove lines from admin and model that extend classes
cp upload/admin/controller/extension/payment/multisafepay.php upload/admin/controller/extension/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/controller/extension/payment/multisafepay.php.tmp > upload/admin/controller/extension/payment/multisafepay.php &&
rm -f upload/admin/controller/extension/payment/multisafepay.php.tmp

cp upload/admin/model/extension/payment/multisafepay.php upload/admin/model/extension/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/model/extension/payment/multisafepay.php.tmp > upload/admin/model/extension/payment/multisafepay.php &&
rm -f upload/admin/model/extension/payment/multisafepay.php.tmp

cp upload/catalog/model/extension/payment/multisafepay.php upload/catalog/model/extension/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/model/extension/payment/multisafepay.php.tmp > upload/catalog/model/extension/payment/multisafepay.php &&
rm -f upload/catalog/model/extension/payment/multisafepay.php.tmp

cp upload/catalog/controller/extension/payment/multisafepay.php upload/catalog/controller/extension/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/controller/extension/payment/multisafepay.php.tmp > upload/catalog/controller/extension/payment/multisafepay.php &&
rm -f upload/catalog/controller/extension/payment/multisafepay.php.tmp

# Remove remaining admin language folders
rm -rf "$FOLDER_PREFIX"/admin/language/deutsch &&
rm -rf "$FOLDER_PREFIX"/admin/language/english &&
rm -rf "$FOLDER_PREFIX"/admin/language/spanish &&
rm -rf "$FOLDER_PREFIX"/admin/language/italian &&
rm -rf "$FOLDER_PREFIX"/admin/language/dutch &&

# Remove remaining catalog language folders
rm -rf "$FOLDER_PREFIX"/catalog/language/deutsch &&
rm -rf "$FOLDER_PREFIX"/catalog/language/english &&
rm -rf "$FOLDER_PREFIX"/catalog/language/spanish &&
rm -rf "$FOLDER_PREFIX"/catalog/language/italian &&
rm -rf "$FOLDER_PREFIX"/catalog/language/dutch &&

# The extension is clean for OC-3.0.X; zip everything
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX"