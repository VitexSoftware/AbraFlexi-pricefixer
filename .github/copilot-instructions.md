# Copilot Instructions

## Schema Validation
- All files in the `multiflexi/*.app.json` directory **must** conform to the schema:
  - Schema URL: https://raw.githubusercontent.com/VitexSoftware/php-vitexsoftware-multiflexi-core/refs/heads/main/multiflexi.app.schema.json
- **Why:** Ensures compatibility with MultiFlexi framework and prevents runtime errors.

## JSON Validation Process
- **Always validate** changes to MultiFlexi JSON files using:
  ```bash
  multiflexi-cli application validate-json --file multiflexi/[filename].app.json
  ```
- **Example:**
  ```bash
  multiflexi-cli application validate-json --file multiflexi/pricefixer.app.json
  ```

## PHP Code Quality
- After **every** edit to a PHP file, **mandatory** run syntax check:
  ```bash
  php -l <filename>
  ```
- **Why:** Catches syntax errors early and maintains code quality.
- **Example:**
  ```bash
  php -l src/PriceFixer.php
  ```

## Project Context
- This is an **AbraFlexi price fixing** tool
- Automatically adjusts and corrects pricing data in AbraFlexi accounting system
- Uses MultiFlexi framework for configuration and deployment management
