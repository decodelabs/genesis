# Genesis compiled builds

Working with compiled builds requires a number of steps and some grit and determination - it affects the very nature of your app's runtime in production so shouldn't be taken lightly.

Genesis employs a flip-flop build policy using two different run folders - one active and one pending. New builds are created in the pending folder and a specific run-file is created; the corresponding run-file is removed from the old active folder. It is then up to the bootstrap to look for the existence of the run-file in either folder and mount the app in whichever is available.

## Build manifest

The first step is to implement the `Build\Manifest` interface -

```php
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Terminus\Session;
use Generator;

interface Manifest
{
    public function getCliSession(): Session;
    public function generateBuildId(): string;

    public function getBuildTempDir(): Dir;

    public function getRunDir(): Dir;
    public function getRunName1(): string;
    public function getRunName2(): string;
    public function getEntryFileName(): string;

    /**
     * @return Generator<Task>
     */
    public function scanPreCompileTasks(): Generator;

    /**
     * @return Generator<Package>
     */
    public function scanPackages(): Generator;

    /**
     * @return Generator<File|Dir, string>
     */
    public function scanPackage(Package $package): Generator;

    public function writeEntryFile(File $file): void;

    /**
     * @return Generator<Task>
     */
    public function scanPostCompileTasks(): Generator;

    /**
     * @return Generator<Task>
     */
    public function scanPostActivationTasks(): Generator;
}
```

Your implementation of this interface is responsible for defining the location of builds, the names of build folders and run-files, loading tasks to be executed in the build process, and providing the list of files and folders to be included in the build.


## Bootstrap

Once the build manifest is in place, you will also need a `Bootstrap` implementation to take care of finding the correct run folder.

```php
// Directly load the bootstrap without initialising composer
require_once 'path/to/vendor/decodelabs/genesis/src/Bootstrap.php';

namespace My\App;

use My\App\Hub;
use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Bootstrap as Base;

class Bootstrap extends Base {

    /**
     * Get list of possible build locations
     */
    public function getRootSearchPaths(): array
    {
        // Return a list of files to search for mapped to the location of the vendor folder
        return [
            'path/to/run-folder/active1/run.php' => 'path/to/run-folder/active1/vendor',
            'path/to/run-folder/active2/run.php' => 'path/to/run-folder/active2/vendor',
            __FILE__ => 'path/to/app-source/vendor'
        ];
    }

    /**
     * Run found build location
     */
    public function execute(string $vendorPath): void
    {
        // Once a vendor folder is found, composer autoloads
        // This method should then consider itself the app's entry point
        Genesis::run(Hub::class, [
            'vendorPath' => $vendorPath
        ]);
        exit;
    }
}
```

Then instead of calling `run` in your entry, you load the Bootstrap from source:

```php
// Load your bootstrap class
require_once 'path/to/my/classes/Bootstrap.php';

// Search through root paths and execute
(new My\App\Bootstrap()->run());
```

This way, your entry defers any work to the Bootstrap class - your Bootstrap being the only thing that needs to load before composer, and won't need to change between application deployments.


## Build tasks

You will also need some CLI tasks to build your app and clear builds when necessary.
In the body of your task (assuming your app is bootstrapped and running some sort of CLI handling Kernel):

```php
Genesis::$build->getHandler()->run();
```

This call will work its way through the process, consuming all of the information your `BuildManifest` supplies it, and generates an active build folder.

Your clear-build task should include:

```php
Genesis::$build->getHandler()->clear();
```
