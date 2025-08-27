# Changelog

All notable changes to this project will be documented in this file.<br>
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### Unreleased
- Moved bootstrap generation to separate Generator class

---

### [v0.14.4](https://github.com/decodelabs/genesis/commits/v0.14.4) - 27th August 2025

- Switched to using dirname(__FILE__) in generated bootstraps

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.14.3...v0.14.4)

---

### [v0.14.3](https://github.com/decodelabs/genesis/commits/v0.14.3) - 27th August 2025

- Avoid __DIR__ in generated bootstraps

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.14.2...v0.14.3)

---

### [v0.14.2](https://github.com/decodelabs/genesis/commits/v0.14.2) - 26th August 2025

- Check for Genesis installation in Composer plugin

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.14.1...v0.14.2)

---

### [v0.14.1](https://github.com/decodelabs/genesis/commits/v0.14.1) - 26th August 2025

- Made hub config definition optional in Composer plugin

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.14.0...v0.14.1)

---

### [v0.14.0](https://github.com/decodelabs/genesis/commits/v0.14.0) - 23rd August 2025

- Removed Bootstrap interface
- Converted package to composer plugin
- Implemented entry point generation in Composer plugin
- Simplified Seamless build strategy
- Generate strategy bootstrap at build time

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.13.1...v0.14.0)

---

### [v0.13.1](https://github.com/decodelabs/genesis/commits/v0.13.1) - 22nd August 2025

- Ported Build task from Fabric

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.13.0...v0.13.1)

---

### [v0.13.0](https://github.com/decodelabs/genesis/commits/v0.13.0) - 21st August 2025

- Removed Loader interface
- Replaced Kernel interface with Kingdom Runtime
- Simplified Container initialisation
- Moved FileTemplate to Hatch
- Added Kingdom Service support
- Use Archetype as a Service
- Use Slingshot to resolve Build Tasks

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.5...v0.13.0)

---

### [v0.12.5](https://github.com/decodelabs/genesis/commits/v0.12.5) - 16th July 2025

- Applied ECS formatting to all code

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.4...v0.12.5)

---

### [v0.12.4](https://github.com/decodelabs/genesis/commits/v0.12.4) - 6th June 2025

- Upgraded Exceptional to v0.6
- Removed Glitch Proxy dependency

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.3...v0.12.4)

---

### [v0.12.3](https://github.com/decodelabs/genesis/commits/v0.12.3) - 21st May 2025

- Upgraded Atlas to v0.13
- Upgraded Terminus to v0.13

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.2...v0.12.3)

---

### [v0.12.2](https://github.com/decodelabs/genesis/commits/v0.12.2) - 20th May 2025

- Upgraded Terminus to v0.12.0

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.1...v0.12.2)

---

### [v0.12.1](https://github.com/decodelabs/genesis/commits/v0.12.1) - 9th April 2025

- Moved Environment Mode to Monarch

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.12.0...v0.12.1)

---

### [v0.12.0](https://github.com/decodelabs/genesis/commits/v0.12.0) - 9th April 2025

- Simplified bootstrap structure
- Added build strategy system
- Moved build implementation to strategy interface
- Added build task scanning facility
- Simplified Build Manifest interface
- Moved path aliases to Monarch
- Removed container Plugin access

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.11.3...v0.12.0)

---

### [v0.11.3](https://github.com/decodelabs/genesis/commits/v0.11.3) - 1st April 2025

- Added path alias system

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.11.2...v0.11.3)

---

### [v0.11.2](https://github.com/decodelabs/genesis/commits/v0.11.2) - 11th March 2025

- Simplified vendor path logic in Bootstrap

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.11.1...v0.11.2)

---

### [v0.11.1](https://github.com/decodelabs/genesis/commits/v0.11.1) - 3rd March 2025

- Initiate with development EnvironmentConfig

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.11.0...v0.11.1)

---

### [v0.11.0](https://github.com/decodelabs/genesis/commits/v0.11.0) - 20th February 2025

- Replaced remaining accessors with property hooks
- Upgraded Coercion dependency

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.10.0...v0.11.0)

---

### [v0.10.0](https://github.com/decodelabs/genesis/commits/v0.10.0) - 13th February 2025

- Replaced accessors with properties
- Upgraded PHPStan to v2
- Tidied boolean logic
- Fixed Exceptional syntax
- Added PHP8.4 to CI workflow
- Made PHP8.4 minimum version

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.9.2...v0.10.0)

---

### [v0.9.2](https://github.com/decodelabs/genesis/commits/v0.9.2) - 7th February 2025

- Removed ref to E_STRICT

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.9.1...v0.9.2)

---

### [v0.9.1](https://github.com/decodelabs/genesis/commits/v0.9.1) - 7th February 2025

- Fixed implicit nullable arguments

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.9.0...v0.9.1)

---

### [v0.9.0](https://github.com/decodelabs/genesis/commits/v0.9.0) - 21st August 2024

- Converted RunMode to enum
- Updated Veneer dependency and Stub
- Removed unneeded LazyLoad binding attribute
- Updated dependency versions

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.6...v0.9.0)

---

### [v0.8.6](https://github.com/decodelabs/genesis/commits/v0.8.6) - 17th July 2024

- Updated Veneer dependency

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.5...v0.8.6)

---

### [v0.8.5](https://github.com/decodelabs/genesis/commits/v0.8.5) - 29th April 2024

- Fixed Veneer stubs in gitattributes

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.4...v0.8.5)

---

### [v0.8.4](https://github.com/decodelabs/genesis/commits/v0.8.4) - 26th April 2024

- Updated Archetype dependency
- Made PHP8.1 minimum version

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.3...v0.8.4)

---

### [v0.8.3](https://github.com/decodelabs/genesis/commits/v0.8.3) - 7th November 2023

- Bind Context in Container on launch

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.2...v0.8.3)

---

### [v0.8.2](https://github.com/decodelabs/genesis/commits/v0.8.2) - 4th November 2023

- Deprecated execute() in favour of run()
- Added default execute() to Bootstrap

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.1...v0.8.2)

---

### [v0.8.1](https://github.com/decodelabs/genesis/commits/v0.8.1) - 4th November 2023

- Return Kernel from initialize()

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.8.0...v0.8.1)

---

### [v0.8.0](https://github.com/decodelabs/genesis/commits/v0.8.0) - 18th October 2023

- Refactored package file structure

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.7.4...v0.8.0)

---

### [v0.7.4](https://github.com/decodelabs/genesis/commits/v0.7.4) - 16th October 2023

- Updated Atlas dependency

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.7.3...v0.7.4)

---

### [v0.7.3](https://github.com/decodelabs/genesis/commits/v0.7.3) - 5th October 2023

- Updated Terminus dependency

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.7.2...v0.7.3)

---

### [v0.7.2](https://github.com/decodelabs/genesis/commits/v0.7.2) - 26th September 2023

- Converted phpstan doc comments to generic

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.7.1...v0.7.2)

---

### [v0.7.1](https://github.com/decodelabs/genesis/commits/v0.7.1) - 25th November 2022

- Added FileTemplate base

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.7.0...v0.7.1)

---

### [v0.7.0](https://github.com/decodelabs/genesis/commits/v0.7.0) - 22nd November 2022

- Added fluidity cast to Hub
- Migrated to use effigy in CI workflow
- Fixed PHP8.1 testing

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.6.2...v0.7.0)

---

### [v0.6.2](https://github.com/decodelabs/genesis/commits/v0.6.2) - 1st November 2022

- Added node exists check to build process

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.6.1...v0.6.2)

---

### [v0.6.1](https://github.com/decodelabs/genesis/commits/v0.6.1) - 12th October 2022

- Removed intiializeErrorHandler from Hub

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.6.0...v0.6.1)

---

### [v0.6.0](https://github.com/decodelabs/genesis/commits/v0.6.0) - 12th October 2022

- Updated initialisation interface

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.5.0...v0.6.0)

---

### [v0.5.0](https://github.com/decodelabs/genesis/commits/v0.5.0) - 4th October 2022

- Added BuildManifest to Hub interface

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.4.1...v0.5.0)

---

### [v0.4.1](https://github.com/decodelabs/genesis/commits/v0.4.1) - 3rd October 2022

- Added build Handler structure

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.4.0...v0.4.1)

---

### [v0.4.0](https://github.com/decodelabs/genesis/commits/v0.4.0) - 30th September 2022

- Updated kernel interface

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.3.0...v0.4.0)

---

### [v0.3.0](https://github.com/decodelabs/genesis/commits/v0.3.0) - 30th September 2022

- Updated Environment and Hub interfaces

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.2.1...v0.3.0)

---

### [v0.2.1](https://github.com/decodelabs/genesis/commits/v0.2.1) - 30th September 2022

- Set build compiled if build time set

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.2.0...v0.2.1)

---

### [v0.2.0](https://github.com/decodelabs/genesis/commits/v0.2.0) - 30th September 2022

- Added basic build info to context

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.1.1...v0.2.0)

---

### [v0.1.1](https://github.com/decodelabs/genesis/commits/v0.1.1) - 29th September 2022

- Added env config overrides

[Full list of changes](https://github.com/decodelabs/genesis/compare/v0.1.0...v0.1.1)

---

### [v0.1.0](https://github.com/decodelabs/genesis/commits/v0.1.0) - 29th September 2022

- Built initial codebase
