#!/usr/bin/env bash

##############################
# RELEASE FOR OPENCART 2.0.X #
##############################

# Exit if any command fails
set -eo pipefail

RELEASE_VERSION=$1
FILENAME_PREFIX="MultiSafepay_For_OpenCart_2.0.X_Release_"
OPENCART_VERSION="2.0.X"
RELEASE_FOLDER=".dist"
RELEASE_FOLDER_SUB_DIRECTORY="2.0.X"
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
mkdir "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
git archive --format zip -9 --output "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
unzip "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
rm "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
composer install --no-dev &&


# Rename src to upload folder
mv src "$FOLDER_PREFIX" &&

# Remove unwanted filed in admin
rm -rf "$FOLDER_PREFIX"/admin/view/template/extension &&
rm -rf "$FOLDER_PREFIX"/admin/model/extension &&
rm -rf "$FOLDER_PREFIX"/admin/controller/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl/extension &&

# Remove unwanted filed in catalog
rm -rf "$FOLDER_PREFIX"/catalog/view/theme/default/template/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/model/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/controller/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl/extension &&

# Remove lines from admin and model that extend classes
cp upload/admin/controller/payment/multisafepay.php upload/admin/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/controller/payment/multisafepay.php.tmp > upload/admin/controller/payment/multisafepay.php &&
rm -f upload/admin/controller/payment/multisafepay.php.tmp

cp upload/admin/model/payment/multisafepay.php upload/admin/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/model/payment/multisafepay.php.tmp > upload/admin/model/payment/multisafepay.php &&
rm -f upload/admin/model/payment/multisafepay.php.tmp

cp upload/catalog/model/payment/multisafepay.php upload/catalog/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/model/payment/multisafepay.php.tmp > upload/catalog/model/payment/multisafepay.php &&
rm -f upload/catalog/model/payment/multisafepay.php.tmp

cp upload/catalog/controller/payment/multisafepay.php upload/catalog/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/controller/payment/multisafepay.php.tmp > upload/catalog/controller/payment/multisafepay.php &&
rm -f upload/catalog/controller/payment/multisafepay.php.tmp

# Rename classes to meet OC 2.0 directive.
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/admin/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/admin/model/payment/multisafepay.php
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/catalog/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/catalog/model/payment/multisafepay.php

# Remove remaining admin language folders
rm -rf "$FOLDER_PREFIX"/admin/language/de-de &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb &&
rm -rf "$FOLDER_PREFIX"/admin/language/es &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl &&

# Remove remaining catalog language folders
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl &&

# The extension is clean.
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX" install.xml &&
mv "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip ../ &&
cd ../../ &&


##############################
# RELEASE FOR OPENCART 2.1.X #
##############################

FILENAME_PREFIX="MultiSafepay_For_OpenCart_2.1.X_Release_"
OPENCART_VERSION="2.1.X"
RELEASE_FOLDER_SUB_DIRECTORY="2.1.X"

# Remove old folder
if [[ ! -d "$RELEASE_FOLDER_SUB_DIRECTORY" ]];
  then
    mkdir "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY";
  else
    rm -rf "${RELEASE_FOLDER:?}/${RELEASE_FOLDER_SUB_DIRECTORY:?}" &&
    mkdir "$RELEASE_FOLDER_SUB_DIRECTORY";
fi

git archive --format zip -9 --output "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
unzip "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
rm "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
composer install --no-dev &&


# Rename src to upload folder
mv src "$FOLDER_PREFIX" &&

# Remove unwanted filed in admin
rm -rf "$FOLDER_PREFIX"/admin/view/template/extension &&
rm -rf "$FOLDER_PREFIX"/admin/model/extension &&
rm -rf "$FOLDER_PREFIX"/admin/controller/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl/extension &&

# Remove unwanted filed in catalog
rm -rf "$FOLDER_PREFIX"/catalog/view/theme/default/template/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/model/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/controller/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl/extension &&

# Remove lines from admin and model that extend classes
cp upload/admin/controller/payment/multisafepay.php upload/admin/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/controller/payment/multisafepay.php.tmp > upload/admin/controller/payment/multisafepay.php &&
rm -f upload/admin/controller/payment/multisafepay.php.tmp

cp upload/admin/model/payment/multisafepay.php upload/admin/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/model/payment/multisafepay.php.tmp > upload/admin/model/payment/multisafepay.php &&
rm -f upload/admin/model/payment/multisafepay.php.tmp

cp upload/catalog/model/payment/multisafepay.php upload/catalog/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/model/payment/multisafepay.php.tmp > upload/catalog/model/payment/multisafepay.php &&
rm -f upload/catalog/model/payment/multisafepay.php.tmp

cp upload/catalog/controller/payment/multisafepay.php upload/catalog/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/controller/payment/multisafepay.php.tmp > upload/catalog/controller/payment/multisafepay.php &&
rm -f upload/catalog/controller/payment/multisafepay.php.tmp

# Rename classes to meet OC 2.1 directive.
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/admin/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/admin/model/payment/multisafepay.php
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/catalog/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/catalog/model/payment/multisafepay.php

# Remove remaining admin language folders
rm -rf "$FOLDER_PREFIX"/admin/language/de-de &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb &&
rm -rf "$FOLDER_PREFIX"/admin/language/es &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl &&

# Remove remaining catalog language folders
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl &&

# The extension is clean.
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX" install.xml
mv "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip ../ &&
cd ../../ &&

##############################
# RELEASE FOR OPENCART 2.2.X #
##############################

FILENAME_PREFIX="MultiSafepay_For_OpenCart_2.2.X_Release_"
OPENCART_VERSION="2.2.X"
RELEASE_FOLDER_SUB_DIRECTORY="2.2.X"

# Remove old folder
if [[ ! -d "$RELEASE_FOLDER_SUB_DIRECTORY" ]];
  then
    mkdir "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY";
  else
    rm -rf "${RELEASE_FOLDER:?}/${RELEASE_FOLDER_SUB_DIRECTORY:?}" &&
    mkdir "$RELEASE_FOLDER_SUB_DIRECTORY";
fi

git archive --format zip -9 --output "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
unzip "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
rm "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
composer install --no-dev &&

# Rename src to upload folder
mv src "$FOLDER_PREFIX" &&

# Remove unwanted filed in admin
rm -rf "$FOLDER_PREFIX"/admin/view/template/extension &&
rm -rf "$FOLDER_PREFIX"/admin/model/extension &&
rm -rf "$FOLDER_PREFIX"/admin/controller/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl/extension &&

# Remove unwanted filed in catalog
rm -rf "$FOLDER_PREFIX"/catalog/view/theme/default/template/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/model/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/controller/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/de-de/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/en-gb/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/es/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/it-it/extension &&
rm -rf "$FOLDER_PREFIX"/catalog/language/nl-nl/extension &&

# Remove lines from admin and model that extend classes
cp upload/admin/controller/payment/multisafepay.php upload/admin/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/controller/payment/multisafepay.php.tmp > upload/admin/controller/payment/multisafepay.php &&
rm -f upload/admin/controller/payment/multisafepay.php.tmp

cp upload/admin/model/payment/multisafepay.php upload/admin/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/admin/model/payment/multisafepay.php.tmp > upload/admin/model/payment/multisafepay.php &&
rm -f upload/admin/model/payment/multisafepay.php.tmp

cp upload/catalog/model/payment/multisafepay.php upload/catalog/model/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/model/payment/multisafepay.php.tmp > upload/catalog/model/payment/multisafepay.php &&
rm -f upload/catalog/model/payment/multisafepay.php.tmp

cp upload/catalog/controller/payment/multisafepay.php upload/catalog/controller/payment/multisafepay.php.tmp &&
sed '$ d' upload/catalog/controller/payment/multisafepay.php.tmp > upload/catalog/controller/payment/multisafepay.php &&
rm -f upload/catalog/controller/payment/multisafepay.php.tmp

# Rename classes to meet OC 2.2 directive.
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/admin/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/admin/model/payment/multisafepay.php
sed -i 's/ControllerExtensionPaymentMultiSafePay/ControllerPaymentMultiSafePay/g' upload/catalog/controller/payment/multisafepay.php
sed -i 's/ModelExtensionPaymentMultiSafePay/ModelPaymentMultiSafePay/g' upload/catalog/model/payment/multisafepay.php

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

# The extension is clean for OC-2.2.X; zip everything
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX"
mv "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip ../ &&
cd ../../ &&

##############################
# RELEASE FOR OPENCART 2.3.X #
##############################

FILENAME_PREFIX="MultiSafepay_For_OpenCart_2.3.X_Release_"
OPENCART_VERSION="2.3.X"
RELEASE_FOLDER_SUB_DIRECTORY="2.3.X"

# Remove old folder
if [[ ! -d "$RELEASE_FOLDER_SUB_DIRECTORY" ]];
  then
    mkdir "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY";
  else
    rm -rf "${RELEASE_FOLDER:?}/${RELEASE_FOLDER_SUB_DIRECTORY:?}" &&
    mkdir "$RELEASE_FOLDER_SUB_DIRECTORY";
fi

git archive --format zip -9 --output "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
unzip "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
rm "$FILENAME_PREFIX""$RELEASE_VERSION".zip &&
composer install --no-dev &&

# Rename src to upload folder
mv src "$FOLDER_PREFIX" &&

# Remove unwanted filed in admin
rm "$FOLDER_PREFIX"/admin/view/template/extension/payment/multisafepay.twig &&
rm "$FOLDER_PREFIX"/admin/view/template/extension/payment/multisafepay_order.twig &&
rm -rf "$FOLDER_PREFIX"/admin/view/template/payment &&
rm -rf "$FOLDER_PREFIX"/admin/model/payment &&
rm -rf "$FOLDER_PREFIX"/admin/controller/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/de-de/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/en-gb/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/es/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/it-it/payment &&
rm -rf "$FOLDER_PREFIX"/admin/language/nl-nl/payment &&

# Remove unwanted filed in catalog
rm "$FOLDER_PREFIX"/catalog/view/theme/default/template/extension/payment/multisafepay.twig &&
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

# The extension is clean for OC-2.3.X; zip everything
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX"
mv "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip ../ &&
cd ../../ &&

##############################
# RELEASE FOR OPENCART 3.0.X #
##############################

FILENAME_PREFIX="MultiSafepay_For_OpenCart_3.0.X_Release_"
OPENCART_VERSION="3.0.X"
RELEASE_FOLDER_SUB_DIRECTORY="3.0.X"

# Remove old folder
if [[ ! -d "$RELEASE_FOLDER_SUB_DIRECTORY" ]];
  then
    mkdir "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY";
  else
    rm -rf "${RELEASE_FOLDER:?}/${RELEASE_FOLDER_SUB_DIRECTORY:?}" &&
    mkdir "$RELEASE_FOLDER_SUB_DIRECTORY";
fi

git archive --format zip -9 --output "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY"/"$FILENAME_PREFIX""$RELEASE_VERSION".zip "$RELEASE_VERSION" &&

# Unzip for composer install
cd "$RELEASE_FOLDER"/"$RELEASE_FOLDER_SUB_DIRECTORY" &&
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
zip -9 -r "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip "$FOLDER_PREFIX" &&
mv "$FILENAME_PREFIX""$RELEASE_VERSION".ocmod.zip ../ &&
cd ../../ &&

## Remove everything
rm -rf "$RELEASE_FOLDER"/2.0.X &&
rm -rf "$RELEASE_FOLDER"/2.1.X &&
rm -rf "$RELEASE_FOLDER"/2.2.X &&
rm -rf "$RELEASE_FOLDER"/2.3.X &&
rm -rf "$RELEASE_FOLDER"/3.0.X