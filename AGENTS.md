# WARP.md - Working AI Reference for AbraFlexi-pricefixer

## Project Overview
**Type**: PHP Project/Debian Package
**Purpose**: [![wakatime](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/018e4b01-3133-41a5-8bc4-39752d34fe20.svg)](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/018e4b01-3133-41a5-8bc4-39752d34fe20)
**Status**: Active
**Repository**: git@github.com:VitexSoftware/AbraFlexi-pricefixer.git

## Key Technologies
- PHP
- Composer
- Debian Packaging

## Architecture & Structure
```
AbraFlexi-pricefixer/
├── src/           # Source code
├── tests/         # Test files
├── docs/          # Documentation
└── ...
```

## Development Workflow

### Prerequisites
- Development environment setup
- Required dependencies

### Setup Instructions
```bash
# Clone the repository
git clone git@github.com:VitexSoftware/AbraFlexi-pricefixer.git
cd AbraFlexi-pricefixer

# Install dependencies
composer install
```

### Build & Run
```bash
dpkg-buildpackage -b -uc
```

### Testing
```bash
composer test
```

## Key Concepts
- **Main Components**: Core functionality and modules
- **Configuration**: Configuration files and environment variables
- **Integration Points**: External services and dependencies

## Common Tasks

### Development
- Review code structure
- Implement new features
- Fix bugs and issues

### Deployment
- Build and package
- Deploy to target environment
- Monitor and maintain

## Troubleshooting
- **Common Issues**: Check logs and error messages
- **Debug Commands**: Use appropriate debugging tools
- **Support**: Check documentation and issue tracker

## Additional Notes
- Project-specific conventions
- Development guidelines
- Related documentation
