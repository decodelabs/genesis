# Genesis — Package Specification

> **Cluster:** `runtime`
> **Language:** `php`
> **Milestone:** `m2`
> **Repo:** `https://github.com/decodelabs/genesis`
> **Role:** Bootstrapping

This document describes the purpose, contracts, and design of **Genesis** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** Genesis to bootstrap their applications.
- Contributors **maintaining or extending** Genesis.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

Genesis provides a universal bootstrapping framework for PHP applications. It orchestrates the initialization process by coordinating Hub implementations, environment configuration, build system integration, platform initialization, and Kingdom service container setup. Genesis takes the guesswork out of structuring the lowest level code in frameworks and applications, providing a unified, dependable bootstrap process for all environments. It includes support for compiled builds, allowing applications to isolate active runtime code from source, which is especially useful for legacy frameworks that can't easily be deployed using third-party automated deployment systems.

### 1.2 Non-Goals

Genesis does **not**:

- Provide application-specific business logic or domain models
- Implement routing, middleware, or request handling
- Provide database abstraction or ORM functionality
- Implement user authentication or authorization
- Provide templating or view rendering
- Implement session management or caching
- Provide asset management or bundling
- Implement form handling or validation
- Provide application scaffolding or code generation
- Implement deployment or DevOps tooling beyond build compilation
- Provide testing frameworks or test runners
- Implement logging or monitoring beyond basic error handling

Genesis focuses on providing the foundational bootstrapping infrastructure, not on implementing application-specific features.

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `runtime` (see Chorus taxonomy)
- Genesis is positioned as a runtime framework that provides the foundational bootstrapping infrastructure for PHP applications. It sits at the lowest level of the application stack, orchestrating initialization before any application code runs. It is used by frameworks like Fabric and applications built on the Decode Labs ecosystem to establish a standard bootstrapping pattern. Genesis integrates with Kingdom for service container management, Monarch for global service location, and various other packages for specific functionality.

### 2.2 Typical Usage Contexts

Typical places Genesis appears:

- Application entry points (HTTP and CLI)
- Framework bootstrapping (e.g., Fabric)
- Build and deployment processes
- Development tooling (e.g., Effigy)
- Application initialization and startup
- Environment configuration and setup
- Service container initialization
- Path management and resolution
- Build compilation and activation
- Static analysis and code inspection (via AnalysisMode)

Genesis is intended to be used as the first step in any application lifecycle, providing the infrastructure needed for all subsequent operations.

---

## 3. Public Surface

> This section focuses on the conceptual API, not every symbol.

### 3.1 Key Types

The primary public types are:

- `DecodeLabs\Genesis`
  Main bootstrapping service that orchestrates the entire initialization process. Handles Hub loading, build information, environment configuration, platform initialization, and Kingdom setup. Implements Kingdom's `Service` interface for dependency injection.

- `DecodeLabs\Genesis\Hub`
  Interface that applications must implement to customize the bootstrapping process. Defines methods for loader initialization, build loading, environment configuration, platform initialization, and Kingdom loading. Applications extend this interface to provide application-specific bootstrapping logic.

- `DecodeLabs\Genesis\Build`
  Represents build information including path, timestamp, and compilation status. Implements Monarch's `Build` interface. Provides cache busting logic based on compilation status and environment mode.

- `DecodeLabs\Genesis\Environment`
  Represents runtime environment configuration including mode, name, error reporting, locale, and timezone. Implements Monarch's `Environment` interface. Configures PHP settings (umask, error reporting, display errors, locale, timezone, MB encoding).

- `DecodeLabs\Genesis\Build\Manifest`
  Interface for build system configuration. Defines methods for scanning packages, tasks, and build items. Applications implement this to customize the build compilation process.

- `DecodeLabs\Genesis\Build\Handler`
  Handles the build compilation and activation process. Orchestrates pre-compile tasks, compilation, post-compile tasks, activation, and post-activation tasks. Uses Manifest to gather build information.

- `DecodeLabs\Genesis\Build\Strategy`
  Interface for build activation strategies. Defines methods for activating and clearing builds. Default implementation is `Seamless` which uses a flip-flop policy with two build directories.

- `DecodeLabs\Genesis\Build\Task`
  Interface for build tasks that can be executed during the build process. Tasks are categorized as `PreCompile`, `PostCompile`, or `PostActivation` and executed in priority order.

- `DecodeLabs\Genesis\Build\Provider`
  Interface for scanning build items (files and directories) to include in compiled builds. Applications and packages can implement this to contribute files to builds.

- `DecodeLabs\Genesis\Build\Package`
  Represents a package to be included in a build, with source directory and optional target path.

- `DecodeLabs\Genesis\Environment\Config`
  Interface for environment configuration. Provides properties for mode, name, error reporting, display errors, umask, locale, and timezone. Implementations include `Development`, `Testing`, and `Production`.

- `DecodeLabs\Genesis\AnalysisMode`
  Enum for analysis modes (`Library` or `Project`). Used to determine how Genesis should behave during static analysis or code inspection.

- `DecodeLabs\Genesis\Composer\Plugin`
  Composer plugin that automatically generates entry point files (`genesis.php` and `genesis-analyze.php`) when composer updates. Reads Hub class from `composer.json` extra configuration.

- `DecodeLabs\Genesis\Composer\Generator`
  Generates entry point files for Genesis bootstrapping. Creates `genesis.php` for runtime and `genesis-analyze.php` for static analysis.

### 3.2 Main Entry Points

The main usage pattern is through Composer plugin integration:

```php
// In composer.json
{
    "type": "project",
    "extra": {
        "genesis": {
            "hub": "MyApp\\Genesis\\Hub"
        }
    }
}

// HTTP server rewrites to vendor/genesis.php
// Genesis takes care of the rest
```

Applications implement Hub:

```php
namespace MyApp\Genesis;

use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Hub as HubInterface;
use DecodeLabs\Genesis\Build;
use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Kingdom;

class Hub implements HubInterface
{
    public function __construct(
        Genesis $genesis,
        ?AnalysisMode $analysisMode = null
    ) {
        // Initialize
    }
    
    public function initializeLoaders(): void
    {
        // Set up loaders
    }
    
    public function loadBuild(): Build
    {
        // Load build information
    }
    
    public function loadEnvironmentConfig(): EnvConfig
    {
        // Load environment configuration
    }
    
    public function initializePlatform(): void
    {
        // Initialize platform
    }
    
    public function loadKingdom(): Kingdom
    {
        // Load Kingdom
    }
}
```

---

## 4. Dependencies

### 4.1 Decode Labs

- `decodelabs/archetype` (required)
  Used for class resolution, especially for Hub class resolution from string names. Also used for scanning build tasks and providers.

- `decodelabs/atlas` (required)
  Used for file and directory operations in build system, path management, and file system access.

- `decodelabs/exceptional` (required)
  Used for exception handling throughout the bootstrapping process.

- `decodelabs/kingdom` (required)
  Used for service container and application management. Genesis loads and initializes Kingdom as part of the bootstrapping process.

- `decodelabs/monarch` (required)
  Used for global service location, path management, and application metadata. Genesis sets up Monarch with paths, build, environment, and Kingdom.

- `decodelabs/slingshot` (required)
  Used for dependency injection and instance resolution, especially for Build Handler and build tasks.

- `decodelabs/systemic` (required)
  Used for system operations and environment detection.

- `decodelabs/terminus` (required)
  Used for CLI session management in build system and task execution.

### 4.2 External

- `composer-plugin-api` (required, ^2.0)
  Used for Composer plugin functionality to generate entry point files.

### 4.3 Optional Integrations

- No optional integrations. All dependencies are required.

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- Genesis must be instantiated with a root path and Hub class name
- Hub class must be resolvable via Archetype
- Hub must implement all required methods
- Build information is loaded before environment configuration
- Environment configuration is loaded before platform initialization
- Platform initialization happens before Kingdom loading
- Kingdom is initialized after being loaded
- Genesis registers itself with Monarch after initialization
- Build Handler is lazy-loaded and requires a Build Manifest
- Entry point files are generated by Composer plugin
- Entry point files check for compiled builds before falling back to source
- Build compilation uses flip-flop strategy with two directories
- Build tasks are executed in priority order (highest first)
- Environment configuration applies PHP settings immediately
- Paths are set up before any other operations
- Start time is recorded at Genesis instantiation
- Analysis mode affects loader initialization and environment config loading
- Compiled builds have non-null timestamps
- Source builds have null timestamps
- Cache busting is enabled for compiled builds or development mode
- Build activation disables old entry files and enables new ones
- Build clearing removes all build artifacts

### 5.2 Input & Output Contracts

**Genesis Constructor:**
- `__construct(string $rootPath, string $hubClass, ?AnalysisMode $analysisMode = null): void` — Initializes Genesis with root path, Hub class name, and optional analysis mode. No return value. Throws exceptions if Hub cannot be resolved or if initialization fails.

**Genesis Methods:**
- `run(): void` — Runs the Kingdom runtime and shuts down. No return value. Called after initialization to start the application.

**Hub Interface:**
- `__construct(Genesis $genesis, ?AnalysisMode $analysisMode = null): void` — Initializes Hub with Genesis instance and optional analysis mode. No return value.
- `initializeLoaders(): void` — Sets up loaders (e.g., Archetype mappings). No return value.
- `loadBuild(): Build` — Loads build information. Returns Build instance.
- `loadEnvironmentConfig(): EnvConfig` — Loads environment configuration. Returns EnvConfig instance (Development, Testing, or Production).
- `initializePlatform(): void` — Initializes platform (e.g., error handlers). No return value.
- `loadKingdom(): Kingdom` — Loads and returns Kingdom instance. Returns Kingdom instance.

**Build Methods:**
- `shouldCacheBust(): bool` — Determines if cache busting should be enabled. Returns true for compiled builds or development mode.

**Build Handler Methods:**
- `run(): void` — Executes the build process (pre-compile tasks, compilation, post-compile tasks, activation, post-activation tasks). No return value.
- `compile(): Dir` — Compiles build into temporary directory. Returns compiled build directory.
- `activate(): void` — Activates compiled build using strategy. No return value.
- `clear(): void` — Clears all build artifacts. No return value.

**Build Manifest Interface:**
- `getCliSession(): Session` — Gets CLI session for build output. Returns Session instance.
- `generateBuildId(): string` — Generates unique build identifier. Returns string.
- `getBuildTempDir(): Dir` — Gets temporary directory for build compilation. Returns Dir instance.
- `scanPreCompileTasks(): Generator<Task>` — Scans for pre-compile tasks. Returns generator of Task instances.
- `scanPackages(): Generator<Package>` — Scans for packages to include in build. Returns generator of Package instances.
- `scanPackage(Package $package): Generator<File|Dir,string>` — Scans package for build items. Returns generator of file/directory mappings.
- `writeEntryFile(File $file, string $buildId, string $hubClass): void` — Writes build entry file. No return value.
- `scanPostCompileTasks(): Generator<Task>` — Scans for post-compile tasks. Returns generator of Task instances.
- `scanPostActivationTasks(): Generator<Task>` — Scans for post-activation tasks. Returns generator of Task instances.

**Build Strategy Interface:**
- `activate(Dir $buildDir, Session $session): void` — Activates build directory. No return value.
- `clear(Session $session): void` — Clears build artifacts. No return value.

**Build Task Interface:**
- `run(Session $session): void` — Executes task. No return value.

**Build Provider Interface:**
- `scanBuildItems(Dir $rootDir): Generator<File|Dir,string>` — Scans for build items. Returns generator of file/directory mappings.

**Environment Config Interface:**
- Properties: `name`, `mode`, `displayErrors`, `errorReporting`, `umask`, `defaultLocale`, `defaultTimezone` — All optional, with getters and setters where applicable.

**Composer Plugin Methods:**
- `process(Event $event): void` — Generates entry point files after composer install/update. No return value.

---

## 6. Error Handling

- Hub resolution failures throw `Exceptional` exceptions
- Missing Hub class throws Archetype exceptions
- Build Manifest missing throws `Exceptional::Setup` exception
- Build compilation failures throw `Exceptional::Runtime` exceptions
- Invalid build directories throw exceptions
- Task execution failures are caught and logged, but don't stop build process
- Environment configuration failures may cause PHP warnings/errors
- Path resolution failures throw exceptions
- Kingdom loading failures throw exceptions
- Platform initialization failures may cause application startup to fail
- Entry file generation failures are handled gracefully by Composer plugin
- Build activation failures throw exceptions
- Invalid strategy implementations throw exceptions
- Provider scanning failures may cause build items to be skipped

---

## 7. Configuration & Extensibility

- Configuration is done via Composer (`composer.json` extra.genesis.hub)
- Hub class must be specified in Composer configuration
- Application type (`project` vs `library`) affects analysis mode
- Build Manifest can be customized by implementing the interface
- Build Strategy can be customized by implementing the interface
- Build Tasks can be added by implementing Task interfaces
- Build Providers can be added by implementing Provider interface
- Environment Config can be customized by implementing the interface
- Hub can be extended to customize any aspect of bootstrapping
- Analysis mode affects loader initialization and environment config loading
- Build compilation can be disabled via `$buildHandler->compile = false`
- Build tasks are executed in priority order (configurable via `$priority` property)
- Entry file generation is automatic via Composer plugin
- Build ID generation can be customized in Manifest
- Build temp directory can be customized in Manifest
- CLI session can be customized in Manifest
- Package scanning can be customized in Manifest
- Build item scanning can be customized via Providers

---

## 8. Interactions with Other Packages

### 8.1 Kingdom

Genesis uses Kingdom for:
- Service container and application management
- Application lifecycle (initialize, run, shutdown)
- Service registration and resolution

Genesis loads Kingdom via Hub and registers it with Monarch.

### 8.2 Monarch

Genesis uses Monarch for:
- Global service location
- Path management (root, run, working, localData, sharedData)
- Build information storage
- Environment information storage
- Kingdom registration
- Start time recording

Genesis sets up Monarch with all necessary information during bootstrapping.

### 8.3 Archetype

Genesis uses Archetype for:
- Hub class resolution from string names
- Scanning build tasks and providers
- Class discovery and resolution

### 8.4 Atlas

Genesis uses Atlas for:
- File and directory operations in build system
- Path management and file system access
- Build item copying and merging

### 8.5 Slingshot

Genesis uses Slingshot for:
- Dependency injection for Build Handler
- Instance resolution for build tasks
- Service resolution

### 8.6 Terminus

Genesis uses Terminus for:
- CLI session management in build system
- Task execution output
- User interaction during builds

### 8.7 Systemic

Genesis uses Systemic for:
- System operations
- Environment detection
- Command execution

### 8.8 Exceptional

Genesis uses Exceptional for:
- Exception handling throughout bootstrapping
- Error reporting and logging

### 8.9 Composer

Genesis integrates with Composer via:
- Plugin system for entry point generation
- Package metadata reading
- Dependency resolution

### 8.10 Other Packages

Genesis may be used by:
- `decodelabs/fabric` — Framework bootstrapping
- `decodelabs/clip` — CLI application bootstrapping
- `decodelabs/effigy` — Development tooling
- Applications built on Decode Labs ecosystem

---

## 9. Usage Examples

### 9.1 Basic Application Setup

```php
// composer.json
{
    "name": "myapp/application",
    "type": "project",
    "require": {
        "decodelabs/genesis": "^0.14"
    },
    "extra": {
        "genesis": {
            "hub": "MyApp\\Genesis\\Hub"
        }
    }
}

// HTTP server rewrites to vendor/genesis.php
// Genesis automatically bootstraps
```

### 9.2 Hub Implementation

```php
namespace MyApp\Genesis;

use DecodeLabs\Archetype;
use DecodeLabs\Genesis;
use DecodeLabs\Genesis\AnalysisMode;
use DecodeLabs\Genesis\Build;
use DecodeLabs\Genesis\Build\Manifest as BuildManifest;
use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Genesis\Hub as HubInterface;
use DecodeLabs\Kingdom;
use DecodeLabs\Monarch;
use DecodeLabs\Pandora\Container;

class Hub implements HubInterface
{
    public ?BuildManifest $buildManifest {
        get => new MyBuildManifest($this->strategy, $this->archetype);
    }
    
    protected Container $container;
    protected Archetype $archetype;
    protected Strategy $strategy;
    
    public function __construct(
        protected Genesis $genesis,
        protected ?AnalysisMode $analysisMode = null
    ) {
        $this->container = new Container();
        $this->archetype = $this->container->get(Archetype::class);
        $this->strategy = new Seamless();
    }
    
    public function initializeLoaders(): void
    {
        // Set up Archetype mappings
        $this->archetype->map(
            root: 'MyApp',
            namespace: 'MyApp',
            priority: 10
        );
    }
    
    public function loadBuild(): Build
    {
        $paths = Monarch::getPaths();
        $buildPath = $paths->root;
        $timestamp = null; // Source build
        
        return new Build($this->genesis, $buildPath, $timestamp);
    }
    
    public function loadEnvironmentConfig(): EnvConfig
    {
        // Load from Dovetail or use defaults
        return new EnvConfig\Development('local');
    }
    
    public function initializePlatform(): void
    {
        // Initialize error handlers, etc.
    }
    
    public function loadKingdom(): Kingdom
    {
        $class = $this->archetype->resolve(Kingdom::class);
        return new $class($this->container);
    }
}
```

### 9.3 Build Manifest Implementation

```php
namespace MyApp\Genesis\Build;

use DecodeLabs\Archetype;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Genesis\Build\Manifest;
use DecodeLabs\Genesis\Build\ManifestTrait;
use DecodeLabs\Genesis\Build\Strategy;
use DecodeLabs\Genesis\Build\Strategy\Seamless;
use DecodeLabs\Terminus\Session;

class MyBuildManifest implements Manifest
{
    use ManifestTrait;
    
    public Strategy $strategy {
        get => new Seamless();
    }
    
    public function __construct(
        Strategy $strategy,
        Archetype $archetype
    ) {
        $this->strategy = $strategy;
        $this->archetype = $archetype;
    }
    
    public function writeEntryFile(
        File $file,
        string $buildId,
        string $hubClass
    ): void {
        $file->putContents(
            <<<PHP
            <?php
            declare(strict_types=1);
            
            const BUILD_ID = '{$buildId}';
            
            require_once __DIR__ . '/vendor/autoload.php';
            
            new DecodeLabs\Genesis(
                rootPath: __DIR__,
                hubClass: {$hubClass}::class
            )->run();
            PHP
        );
    }
}
```

### 9.4 Build Task Implementation

```php
namespace MyApp\Genesis\Build\Task;

use DecodeLabs\Genesis\Build\Task;
use DecodeLabs\Genesis\Build\Task\PostCompile;
use DecodeLabs\Terminus\Session;

class OptimizeAssets implements PostCompile
{
    public int $priority {
        get => 100;
    }
    
    public string $description {
        get => 'Optimizing assets';
    }
    
    public function run(Session $session): void
    {
        $session->info('Optimizing assets...');
        // Asset optimization logic
        $session->success('Assets optimized');
    }
}
```

### 9.5 Build Provider Implementation

```php
namespace MyApp\Genesis\Build\Provider;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Genesis\Build\Provider;
use Generator;

class ConfigFiles implements Provider
{
    public string $name {
        get => 'config';
    }
    
    public function __construct()
    {
    }
    
    public function scanBuildItems(Dir $rootDir): Generator
    {
        yield $rootDir->getDir('config') => 'config/';
        yield $rootDir->getFile('.env') => '.env';
    }
}
```

### 9.6 Environment Config Implementation

```php
namespace MyApp\Genesis\Environment\Config;

use DecodeLabs\Genesis\Environment\Config;
use DecodeLabs\Monarch\EnvironmentMode as Mode;

class Staging implements Config
{
    protected const DefaultName = 'staging';
    
    public ?string $name {
        get => self::DefaultName;
    }
    
    public ?Mode $mode {
        get => Mode::Production;
    }
    
    public ?bool $displayErrors {
        get => false;
        set {}
    }
    
    public ?int $errorReporting {
        get => E_ALL & ~E_DEPRECATED & ~E_STRICT;
    }
    
    public ?int $umask {
        get => 0022;
        set {}
    }
    
    public ?string $defaultLocale {
        get => 'en_US';
    }
    
    public ?string $defaultTimezone {
        get => 'UTC';
    }
}
```

### 9.7 Build Compilation

```php
use DecodeLabs\Genesis;
use DecodeLabs\Monarch;

// In a CLI command
$genesis = Monarch::getService(Genesis::class);
$genesis->buildHandler->run();
```

### 9.8 Build Clearing

```php
use DecodeLabs\Genesis;
use DecodeLabs\Monarch;

// In a CLI command
$genesis = Monarch::getService(Genesis::class);
$genesis->buildHandler->clear();
```

### 9.9 Analysis Mode

```php
// For static analysis tools
// vendor/genesis-analyze.php is automatically generated
// Uses AnalysisMode::Library or AnalysisMode::Project based on composer type

use DecodeLabs\Genesis;
use DecodeLabs\Genesis\AnalysisMode;

// Manual usage
new Genesis(
    rootPath: __DIR__,
    hubClass: MyHub::class,
    analysisMode: AnalysisMode::Library
);
```

---

## 10. Implementation Notes (for Contributors)

### 10.1 Internal Architecture

At a high level, Genesis:
- Provides a Composer plugin that generates entry point files
- Orchestrates bootstrapping through Hub interface
- Manages build information and compilation
- Configures environment settings
- Initializes platform (error handlers, etc.)
- Loads and initializes Kingdom service container
- Integrates with Monarch for global service location
- Supports compiled builds with flip-flop activation strategy
- Scans for build tasks and providers via Archetype
- Executes build tasks in priority order

### 10.2 Bootstrapping Flow

1. Composer plugin generates entry point files
2. Entry point loads Genesis with root path and Hub class
3. Genesis sets start time in Monarch
4. Genesis sets root path in Monarch
5. Genesis resolves Hub class via Archetype
6. Genesis instantiates Hub
7. Genesis loads build information via Hub
8. Genesis sets build and run path in Monarch
9. Genesis initializes loaders via Hub
10. Genesis loads environment configuration via Hub
11. Genesis creates Environment and sets it in Monarch
12. Genesis initializes platform via Hub
13. Genesis loads Kingdom via Hub
14. Genesis registers itself and Kingdom with Monarch
15. Genesis initializes Kingdom
16. Genesis runs Kingdom (which handles requests/commands)
17. Genesis shuts down Kingdom

### 10.3 Build System

The build system uses a flip-flop strategy:
- Two build directories (`build1` and `build2`)
- New builds are compiled into temporary directory
- Build is moved to inactive directory
- Entry file is enabled in new directory
- Entry file is disabled in old directory
- `genesis.php` in run directory checks both directories
- Allows zero-downtime deployments

### 10.4 Entry Point Generation

Composer plugin generates two entry points:
- `vendor/genesis.php` — Runtime entry point, checks for compiled builds first
- `vendor/genesis-analyze.php` — Analysis entry point, uses AnalysisMode

### 10.5 Environment Configuration

Environment configuration applies immediately:
- Umask is set
- Error reporting is configured
- Display errors is configured
- Locale is set
- Timezone is set
- MB encoding is set to UTF-8

### 10.6 Path Management

Genesis sets up paths in Monarch:
- `root` — Application root directory
- `run` — Runtime directory (build path for compiled builds)
- `working` — Working directory (for CLI)
- `localData` — Local data directory
- `sharedData` — Shared data directory

### 10.7 Build Tasks

Build tasks are categorized:
- `PreCompile` — Executed before compilation
- `PostCompile` — Executed after compilation, before activation
- `PostActivation` — Executed after activation

Tasks are executed in priority order (highest first).

### 10.8 Build Providers

Build providers scan for files and directories to include in builds:
- Providers are discovered via Archetype
- Each provider yields file/directory mappings
- Mappings specify source and target location
- Files are copied, directories are merged

### 10.9 Analysis Mode

Analysis mode affects behavior:
- `Library` — Skips application-specific initialization
- `Project` — Full initialization

Used for static analysis tools that need to analyze code without running the application.

### 10.10 Performance Considerations

- Lazy loading of Build Handler
- Build tasks are scanned once per build
- Providers are instantiated once per build
- Entry point files are cached by Composer
- Build compilation can be disabled for development
- Cache busting is conditional based on build type and mode

### 10.11 Gotchas & Historical Decisions

- Hub class must be resolvable via Archetype (not just a string)
- Build Manifest is optional (can be null)
- Build Handler requires Build Manifest to be created
- Entry point files check for compiled builds before falling back to source
- Build activation uses flip-flop strategy to avoid downtime
- Environment configuration applies PHP settings immediately (may affect other code)
- Paths are set up before any file operations
- Start time is recorded at Genesis instantiation (not at run())
- Analysis mode affects loader initialization (Library mode skips app namespace setup)
- Build compilation creates entry file as `.disabled` initially
- Build activation renames entry file from `.disabled` to active
- Old build entry files are disabled (renamed to `.disabled`)
- Build clearing removes all build artifacts including run directory
- Composer plugin only generates entry points if Genesis is installed
- Composer plugin warns if Hub class is missing for projects
- Build tasks must implement `Scannable` interface to be discovered
- Build providers are discovered via Archetype scanning
- Package scanning in Manifest is optional (can yield empty)
- Build temp directory is in localData by default
- Build ID is generated via `uniqid()` by default
- CLI session uses Terminus default session by default
- Strategy is provided by Manifest (not configurable elsewhere)
- Environment config is loaded via Hub (not directly)
- Kingdom is loaded via Hub (not directly instantiated)
- Genesis registers itself with Kingdom container
- Monarch is set up before Hub is instantiated
- Build information is available before environment config
- Platform initialization happens after environment config
- Kingdom initialization happens after platform initialization

---

## 11. Testing & Quality

- **Code Quality Score:** 4.5/5
- **README Quality Score:** 3/5
- **Documentation Score:** 0/5 (this spec)
- **Test Coverage Score:** 0/5

See `composer.json` for supported PHP versions.

---

## 12. Roadmap & Future Ideas

- Enhanced documentation and examples
- Additional build strategies
- Enhanced build task system
- Better error handling and recovery
- Performance optimizations
- Enhanced environment configuration
- Better integration with deployment systems
- Enhanced build compilation
- Better static analysis support
- Enhanced Composer plugin functionality
- Better path management
- Enhanced build provider system
- Additional build task types
- Better build activation strategies
- Enhanced entry point generation

---

## 13. References

- [Kingdom Package](https://github.com/decodelabs/kingdom) — Service container
- [Monarch Package](https://github.com/decodelabs/monarch) — Global service location
- [Fabric Package](https://github.com/decodelabs/fabric) — Framework using Genesis
- [Clip Package](https://github.com/decodelabs/clip) — CLI kernel using Genesis
- [Effigy Package](https://github.com/decodelabs/effigy) — Development tooling using Genesis
- [Archetype Package](https://github.com/decodelabs/archetype) — Class resolution
- [Atlas Package](https://github.com/decodelabs/atlas) — File system operations
- [Slingshot Package](https://github.com/decodelabs/slingshot) — Dependency injection
- [Terminus Package](https://github.com/decodelabs/terminus) — CLI I/O
- [Systemic Package](https://github.com/decodelabs/systemic) — System operations
- [Exceptional Package](https://github.com/decodelabs/exceptional) — Exception handling
- [Chorus Package Index](../../../chorus/config/packages.json) — Ecosystem metadata

