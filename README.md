# AbraFlexi Price Fixer

Product price updater for AbraFlexi

![PriceFixer](abraflexi-pricefixer.svg?raw=true)

* Fix prices of product bundles in AbraFlexi.
* Count purchase prices for set of producs.
* Set Group of goods for meber of bundles to ABRAFLEXI_GROUP

[![wakatime](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/018e4b01-3133-41a5-8bc4-39752d34fe20.svg)](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/018e4b01-3133-41a5-8bc4-39752d34fe20)

## Commands included

* `abraflexi-pricefixer`       - fix price of one pricelist item
* `abraflexi-pricefixer-all`   - fix price of all pricelist items
* `abraflexi-pricefixer-init`  - prepare AbraFlexi for pricefixer

## Configuration

Here are the configuration keys used with AbraFlexi:

* `ABRAFLEXI_PROVIDER` - Addressbook Company used to be provider
* `ABRAFLEXI_USERNAME` - AbraFlexi username
* `ABRAFLEXI_PASSWORD` - AbraFlexi password
* `ABRAFLEXI_URL` - AbraFlexi API URL
* `ABRAFLEXI_COMPANY` - AbraFlexi company code
* `ABRAFLEXI_GROUP` - Group of Goods for sets of items
* `EASE_LOGGER` - List of logging drivers to be used

Please make sure to set these configuration keys appropriately before running the code.

## Runing

>> NOTE: please run `abraflexi-pricefixer-init` command before first run.

When installed from debian package, the `abraflexi-pricefixer` command is availble systemwide.

If EASE_LOGGER contain `console` then colored output is shown:

![run](run.png?raw=true)

Written for [![MojaVoda.sk](mojavoda.png?raw=true)](https://www.mojavoda.sk/)

## MultiFlexi

AbraFlexi PriceFixer is ready for run as [MultiFlexi](https://multiflexi.eu) application.
See the full list of ready-to-run applications within the MultiFlexi platform on the [application list page](https://www.multiflexi.eu/apps.php).

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)

## Installation

There is repository for Debian/Ubuntu Linux distributions:

```shell
sudo apt install lsb-release wget apt-transport-https bzip2

wget -qO- https://repo.vitexsoftware.com/keyring.gpg | sudo tee /etc/apt/trusted.gpg.d/vitexsoftware.gpg
echo "deb [signed-by=/etc/apt/trusted.gpg.d/vitexsoftware.gpg]  https://repo.vitexsoftware.com  $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo apt update

sudo apt install abraflexi-pricefixer
```
