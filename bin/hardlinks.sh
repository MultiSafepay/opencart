#!/usr/bin/env bash

# Exit if any command fails
set -eo pipefail

CURRENT_DIRECTORY=$(pwd)

# Admin model directory
if [[ ! -d $CURRENT_DIRECTORY"/src/admin/model/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/model/payment";
fi

# Admin view directory
if [[ ! -d $CURRENT_DIRECTORY"/src/admin/view/template/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/view/template/payment";
fi

# Admin controller directory
if [[ ! -d $CURRENT_DIRECTORY"/src/admin/controller/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/controller/payment";
fi

# Admin languages directory
if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/de-de/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/de-de/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/english/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/english/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/es/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/es/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/spanish/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/spanish/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/it-it/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/it-it/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/italian/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/italian/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/admin/language/dutch/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/admin/language/dutch/payment";
fi


# Catalog model directory
if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/model/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/model/payment";
fi

# Catalog view directory
if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/view/theme/default//template/payment";
fi

# Catalog controller directory
if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/controller/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/controller/payment";
fi

# Catalog languages directory
if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/english/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/english/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/es/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/es/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/italian/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/italian/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment";
fi

if [[ ! -d $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment" ]]; then
    mkdir -p $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment";
fi


## Admin hardlinks
if [[ ! -f $CURRENT_DIRECTORY"/src/admin/model/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/model/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/model/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/model/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/model/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/model/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay.tpl" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/view/template/extension/payment/multisafepay.tpl" $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay.tpl";
  else
    rm $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay.tpl" &&
    ln $CURRENT_DIRECTORY"/src/admin/view/template/extension/payment/multisafepay.tpl" $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay.tpl";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay_order.tpl" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/view/template/extension/payment/multisafepay_order.tpl" $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay_order.tpl";
  else
    rm $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay_order.tpl" &&
    ln $CURRENT_DIRECTORY"/src/admin/view/template/extension/payment/multisafepay_order.tpl" $CURRENT_DIRECTORY"/src/admin/view/template/payment/multisafepay_order.tpl";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/controller/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/controller/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/controller/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/controller/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/controller/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/controller/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/de-de/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/de-de/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/de-de/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/de-de/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/deutsch/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/en-gb/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/english/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/english/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/english/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/english/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/es/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/es/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/es/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/es/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/spanish/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/spanish/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/spanish/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/spanish/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/it-it/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/it-it/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/it-it/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/it-it/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/italian/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/italian/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/italian/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/italian/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/nl-nl/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/admin/language/dutch/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/admin/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/dutch/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/admin/language/dutch/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/admin/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/admin/language/dutch/payment/multisafepay.php";
fi


## Catalog hardlinks
if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/model/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/model/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/model/payment/multisafepay.php";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/model/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/model/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/model/payment/multisafepay.php";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/payment/multisafepay.tpl" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/extension/payment/multisafepay.tpl" $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/payment/multisafepay.tpl";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/payment/multisafepay.tpl" &&
    ln $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/extension/payment/multisafepay.tpl" $CURRENT_DIRECTORY"/src/catalog/view/theme/default/template/payment/multisafepay.tpl";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/controller/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/controller/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/controller/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/controller/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/controller/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/controller/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/de-de/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/de-de/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/deutsch/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/en-gb/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/english/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/english/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/english/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/en-gb/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/english/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/es/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/es/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/es/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/es/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/es/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/spanish/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/it-it/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/italian/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/italian/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/italian/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/it-it/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/italian/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/payment";
fi

if [[ ! -f $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment/multisafepay.php" ]];
  then
    ln $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment";
  else
    rm $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment/multisafepay.php" &&
    ln $CURRENT_DIRECTORY"/src/catalog/language/nl-nl/extension/payment/multisafepay.php" $CURRENT_DIRECTORY"/src/catalog/language/dutch/payment";
fi