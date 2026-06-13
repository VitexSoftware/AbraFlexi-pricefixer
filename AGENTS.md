# WARP.md - Working AI Reference for AbraFlexi-pricefixer

## Project Overview
**Type**: PHP Project/Debian Package
**Purpose**: Purchase price updater for AbraFlexi — bundle prices from component costs, and warehouse last-price sync.
**Status**: Active
**Repository**: git@github.com:VitexSoftware/AbraFlexi-pricefixer.git

## Key Technologies
- PHP 8.x, Composer, PSR-4
- `spojenet/flexibee` library — namespace `AbraFlexi\*` (NOT `FlexiPeeHP\*`)
- Debian packaging (`debian/`)
- MultiFlexi app definitions (`multiflexi/`)

## Architecture & Structure

```
src/
  PriceFix/
    Bundler.php          ← extends Cenik; SADA bundle price from component nakupCena
    SadyAKomplety.php    ← extends RW; evidence sady-a-komplety
    StockPriceFixer.php  ← extends Cenik; copies posledCena → nakupCena for non-SADA items
  fixall.php             ← entry point for abraflexi-pricefixer-all
  fixstock.php           ← entry point for abraflexi-pricefixer-stock
  pricefix.php           ← entry point for abraflexi-pricefixer (single item)
  init.php               ← entry point for abraflexi-pricefixer-init
bin/
  abraflexi-pricefixer          ← single bundle fix
  abraflexi-pricefixer-all      ← all bundle fixes
  abraflexi-pricefixer-stock    ← warehouse last-price → purchase price sync
  abraflexi-pricefixer-init     ← init
tests/PriceFix/
  BundlerTest.php
  SadyAKompletyTest.php
  StockPriceFixerTest.php
multiflexi/
  price_fixer.multiflexi.app.json        ← MultiFlexi app: abraflexi-pricefixer-all
  stock_price_fixer.multiflexi.app.json  ← MultiFlexi app: abraflexi-pricefixer-stock
```

## Commands

| Command | Class | Purpose |
|---|---|---|
| `abraflexi-pricefixer` | `Bundler` | Fix price of a single SADA bundle |
| `abraflexi-pricefixer-all` | `Bundler::fixAll()` | Fix prices of all SADA bundles |
| `abraflexi-pricefixer-stock` | `StockPriceFixer::fixAll()` | Copy `posledCena` → `nakupCena` for non-SADA warehouse products |
| `abraflexi-pricefixer-init` | — | Prepare AbraFlexi data for pricefixer |

## Key Concepts

- **`nakupCena`** — purchase price in `cenik`; set from purchase receipts (no side costs)
- **`posledCena`** — last price on `skladova-karta` (warehouse card); includes side costs — more accurate
- **SADA** — bundle product group; identified by `skupZboz = ABRAFLEXI_GROUP` (default `code:SADA`)
- `Bundler` computes SADA bundle `nakupCena` as sum of component `nakupCena * qty`
- `StockPriceFixer` syncs `posledCena` → `nakupCena` for all non-SADA products with non-zero last price

## Configuration

| Variable | Default | Purpose |
|---|---|---|
| `ABRAFLEXI_URL` | — | AbraFlexi API URL |
| `ABRAFLEXI_LOGIN` | — | AbraFlexi username |
| `ABRAFLEXI_PASSWORD` | — | AbraFlexi password |
| `ABRAFLEXI_COMPANY` | — | AbraFlexi company code |
| `ABRAFLEXI_GROUP` | `code:SADA` | skupZboz value identifying bundle products |
| `ABRAFLEXI_WAREHOUSE` | `code:SKLAD-DS` | Warehouse code for `abraflexi-pricefixer-stock` |
| `ABRAFLEXI_PROVIDER` | `code:PRICEFIXER` | Supplier addressbook entry for provider price |
| `EASE_LOGGER` | `console\|syslog` | Log drivers |

## Debian Packaging

`debian/abraflexi-pricefixer.install` globs cover all new files automatically:
- `bin/*` → `/usr/bin`
- `src/*.php` → `/usr/share/abraflexi-pricefixer`
- `src/PriceFix/*.php` → `/usr/lib/abraflexi-pricefixer/PriceFix`
- `multiflexi/*.json` → `/usr/lib/abraflexi-pricefixer/multiflexi`

Build: `dpkg-buildpackage -b -uc` or `~/bin/mkdeb`

## Development Workflow

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run stock price fixer against test company
cd src
ABRAFLEXI_COMPANY=crystal_water_testovacia php -f fixstock.php
```

## Critical Rules

- Always use `AbraFlexi\*` namespace — never `FlexiPeeHP\*`
- Never commit credentials — use `.env` file (copy from `example.env`)
- `new SkladovaKarta()` without constructor options is correct — relies on env-configured shared connection (same as `new Cenik()` in Bundler)
