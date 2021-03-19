# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

***

## 3.9.0
Release date: March, 19th 2021

### Added
+ PLGOPNS-374: Add generic gateway

### Changed
+ PLGOPNS-384: Upgrade PHP-SDK to 4.1.0

***

## 3.8.0
Release date: February, 11th 2021

### Fixed
+ PLGOPNS-382: Fix bug to get the proper settings in a multi store OpenCart site

***

## 3.7.0
Release date: February, 9th 2021

### Fixed
+ PLGOPNS-381: Fix percentage coupon applied before taxes

***

## 3.6.0
Release date: February, 8th 2021

### Added
+ PLGOPNS-379: Add support for OpenCart 3.0.3.7  
+ PLGOPNS-373: Add company_name to CustomerDetails object in order request

### Changed
+ PLGOPNS-380: Upgrade PHP-SDK to 4.1.0
+ PLGOPNS-377: Replace getPaymentLink() with getPaymentUrl() to prepare future deprecation of the method in the PHP-SDK
+ PLGOPNS-376: Improvements in callback notification function

### Fixed
+ PLGOPNS-375: Remove unneded code related with the shopping cart of the transaction    
+ PLGOPNS-378: Fix bug for fixed coupons applied before taxes

***

## 3.5.0
Release date: December, 18th 2020

### Added
+ PLGOPNS-361: Add link to documentation for in3 payment method
+ PLGOPNS-358: Add giftcards logos missing: GivaCard, Wellness, Winkelcheque
+ PLGOPNS-367: Include giftcards in validation before enable a gateway 

### Changed
+ PLGOPNS-372: Upgrade PHP-SDK to 4.0.3
+ DAVAMS-345:  Update logo of Trustly
+ PLGOPNS-359: Remove the round function from getMoneyObjectOrderAmount
+ PLGOPNS-365: Encode html entities in product name to avoid encoding typo errors.

### Fixed
+ PLGOPNS-366: Fix bug, gift voucher items should pass to the transaction as negative amounts
+ PLGOPNS-371: Fix filter per customer group; when customer is not logged

***

## 3.4.0
Release date: November, 19th 2020

### Added
+ SUPD-752:    Add Good4fun Giftcard
+ PLGOPNS-347: Add support in the upgrade script for plugin version 2.2.X 
+ PLGOPNS-350: Upgrade to 4.0.1 MultiSafepay PHP-SDK
+ PLGOPNS-242: Remove coupons, vouchers, rewards and affiliate commissions used, after full refunds

### Changed
+ PLGOPNS-257: Include in support tab the contact details of international offices

***

## 3.3.0
Release date: October, 13th 2020

### Added
+ PLGOPNS-247: Extend compatibility of the extension for OpenCart v. 2.X

### Changed
+ PLGOPNS-329: Show payment logo in checkout and strip html tags before insert payment method in database
+ PLGOPNS-303: Improve iDEAL issuer selection list
+ PLGOPNS-245: Improve the alignment of payment methods logos in checkout page

***

## 3.2.0
Release date: September, 17th 2020

+ DAVAMS-320 : Rebrand 'Klarna' to 'Klarna - Pay later in 14 days' inclusive a new logo
+ PLGOPNS-256: Add a link to documentation in Second Chance field

### Fixed
+ PLGOPNS-295: Add getEventByCode function, which does not exist prior to OC 3.0.2
+ PLGOPNS-284: Fix placeholder for 'lifetime payment link' field

***

## 3.1.0
Release date: September, 14th 2020

### Improvements 
+ PLGOPNS-246: Add a notification message if PHP version is not supported by the plugin.
+ PLGOPNS-243: Improve fields related with lifetime of the payment link in the settings
+ PLGOPNS-253: Validate the field lifetime of payment link to avoid negative number
+ PLGOPNS-252: Improve description for each payment method on backoffice

***

## 3.0.0
Release date: September, 7th 2020

### Improvements 
+ PLGOPNS-133: New opencart rewrite
+ PLGOPNS-176: Migrate XML API to JSON API PHP SDK
+ PLGOPNS-224: Debug option in settings
+ PLGOPNS-149: Make extension installable from OpenCart extension installer tool
+ PLGOPNS-220: Translations for Deutsch, Italian, French, Spanish
+ PLGOPNS-100: Use direct instead redirect for all payment methods when possible
+ PLGOPNS-140: Send shipped status for all payment methods after shipping
+ PLGOPNS-217: Improve function that return titles
+ PLGOPNS-167: Add support for reward-points
+ PLGOPNS-209: Add support for refunds
+ PLGOPNS-108: Improve information about each field using tooltips in settings page
+ Add support for customer balance
+ Add support for all native order totals extensions
+ Add support for gift vouchers
+ Add support to update order status as cancelled or expired from the admin
+ Add support for payment method in3, CBC

### Fixed
+ PLGOPNS-226: Tax issue
+ PLGOPNS-212: Fix PHP DocBlock declaration and correct @author property
+ PLGOPNS-120: Fix multi-currency support
+ PLGOPNS-219: Fix admin view settings page to meet the OpenCart guidelines for UI
+ PLGOPNS-143: Improve parsing of locale.
+ PLGOPNS-223: Check restrictions to show logos in checkout according with title lenght and add support for multi language logos
+ PLGOPNS-103: Correct spelling ING Home'Pay
+ PLGOPNS-144: Coupons restricted to specific products failing to calculate totals
+ PLGOPNS-165: Shipping title contains html tags when Sendcloud is used
+ PLGOPNS-151: Correct spelling of gateways
+ Fix Multi Currency
+ Fix support for taxes
+ Fix support for coupons

### Removed
+ PLGOPNS-228: Remove payment methods BABYGIFTCARD, EROTIEKBON.
+ PLGOPNS-225: Reorder the directories to put MultiSafepay.combined.php in the OpenCart library folder
+ PLGOPNS-147: Remove FastCheckout code
+ PLGOPNS-174: Remove unsupported giftcards
+ Remove VqMod dependency

***

## 2.3.0
Release date: April 2nd, 2020

### Added
+ PLGOPNS-178: Add Apple Pay

### Fixed
+ PLGOPNS-170: Fix payment methods not visible in backend order

***

## 2.2.1
Release date: March 27th, 2019
### Added
+ PLGOPNS-134: Added payment logo's to the backend of the webshop
+ PLGOPNS-118: Added payment logo's in checkout for all payment methods
+ PLGOPNS-129: Added an unique identifier to the transaction for products with a variation
+ PLGOPNS-117: Added compatibility for Simple Payment Fee plugin

### Fixed
+ PLGOPNS-110: Fixed configuration for Multi-Store does not work properly
+ PLGOPNS-109: Direct Debit contained wrong source code
+ PLGOPNS-137: Fixed missing Webshop Giftcard title on checkout
+ PLGOPNS-131: Fixed Fast Checkout is unable to retrieve shipping methods
+ PLGOPNS-113: Notice message when tax is not defined in the backend of the webshop

### Changed
+ PLGOPNS-135: Updated frontend translations
+ PLGOPNS-125: Updated backend translations
+ PLGOPNS-136: Changed loading path of MultiSafepay.combined.php
+ PLGOPNS-105: Improved layout configuration screen
+ PLGOPNS-91: Renamed ING Homepay to ING Home'Pay
+ PLGOPNS-96: Renamed Mister Cash to Bancontact
+ PLGOPNS-102: Make sending of order update mail configurable
+ PLGOPNS-101: Make confirm_message configurable

### Removed
+ PLGOPNS-50: Removed the configuration option 'When to confirm order'
+ PLGOPNS-116: Removed configuration option 'payment_multisafepay_fco_tax_percent'

***

## 2.2.0
Release date: June 15th, 2018
### Added 
+ PLGOPNS-72: Add support for Alipay/ING Home'Pay/Belfius/KBC
+ PLGOPNS-80: Add support for AfterPay
+ PLGOPNS-78: Add support for Santander Betaalplan
+ PLGOPNS-79: Add support for Trustly
+ PLGOPNS-73: Add VVV-Cadeaukaart as a gift card

### Improvements 
+ PLGOPNS-59: Add the shipping address to the transaction request
+ PLGOPNS-86: Improve translations

### Fixed
+ PLGOPNS-74: MultiStore is now correctly supported
+ PLGOPNS-75: Configuration form is now applied for geozone and min/max amount

***

## 2.1.0
Release date: November 13th, 2017
### Improvements
+ Add payment method PaySafeCard.

### Changes
+ Make (only) compatible with OpenCart versions 3.x.

***

## 2.0.2
Release date: February 15th, 2017
### Improvements
+ Only compatible with OpenCart versions 2.3.x.

***

## 2.0.1
Release date: May 20, 2015
### Improvements
+ Added support for Multi-store..
+ Detect current template based on OpenCart version.
+ Added Klarna support.

### Fixed
+ Fixed undefined notices.
+ Fast Checkout wasn't visible after having set "Account type" to "Connect".

### Changes
+ Added VQMod as the preferred method for Fast Checkout integrations.

***

## 2.0.0
Release date: Mar 13, 2015

###  Fixes
+ Fixed problem with converting amount to other currency.

***

## 1.8.1
Release date: Mar 13, 2015

### Fixes
+ Fixed problem with converting amount to other currency.

***

## 2.0.0
Release date: Feb 13, 2015

***

## 1.8.1
Release date: Mar 10, 2014

### Improvements
+ Added support for American Express.
+ Added support for Spaarpunten.
+ Added support for Shipped status.

### Fixes
+ Fixed issue with preselect gateway.
+ Fixed unavailable $order_info bug.

***

## 1.8.0
Release date: Sep 2, 2013

### Improvements
+ Added support for install and configure separate gateways.
+ Added support for Multi-Currency.

### Changes
+ Gateways are now only visible for the supported currency. All available with EUR, Visa and Mastercard for EUR, USD and GBP.
+ Added geo zone support for all supported gateways. Gateway is not visible when address isn't within the selected zone.

### Fixes
+ Now the product-weight is converted correctly to kilogram.
+ Fixed issue when multiple products use multiple different VAT settings.

***

## 1.7.3
Release date: Aug 20, 2013
### Fixes
+ Fixed bug with weight-based shipping for Fast Checkout.
+ Fixed bug with database prefix for table names.
