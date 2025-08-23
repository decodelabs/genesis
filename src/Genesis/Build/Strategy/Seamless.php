<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Strategy;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis\Build\Strategy;
use DecodeLabs\Monarch;
use DecodeLabs\Terminus\Session;

class Seamless implements Strategy
{
    public const string RunDir = 'data/local/run';
    public const string BuildPrefix = 'build';
    public const string BuildEntry = 'entry.php';

    public function activate(
        Dir $source,
        Session $session
    ): void {
        // Prepare
        $runDir = Atlas::getDir(Monarch::getPaths()->root . '/' . self::RunDir);
        $entryName = self::BuildEntry;

        $runName1 = self::BuildPrefix . '1';
        $buildDir1 = $runDir->getDir($runName1);
        $entryFile1 = $buildDir1->getFile($entryName);

        $runName2 = self::BuildPrefix . '2';
        $buildDir2 = $runDir->getDir($runName2);
        $entryFile2 = $buildDir2->getFile($entryName);

        clearstatcache(true);



        // Check for existing
        $active1Exists = $entryFile1->exists();
        $active2Exists = $entryFile2->exists();

        if (
            $active1Exists &&
            $active2Exists
        ) {
            $entryFile2->renameTo($entryName . '.disabled');
            $active2Exists = false;
            clearstatcache(true);
        }

        if ($active1Exists) {
            $current = $buildDir1;
            $old = $buildDir2;
        } elseif ($active2Exists) {
            $current = $buildDir2;
            $old = $buildDir1;
        } else {
            $current = null;
            $old = $buildDir1;
        }

        $targetName = $old->name;
        $session->{'.cyan'}($old->path);

        // Move previous out the way
        if ($old->exists()) {
            $session->write(' - ');
            $session->{'yellow'}($old->name);
            $session->write(' > ');
            $session->{'.red'}('deleted');

            $old->delete();
        }


        // Move source to runDir
        $session->write(' - ');
        $session->{'yellow'}($source->name);
        $session->write(' > ');
        $session->{'.green'}($targetName);

        $source->moveTo((string)$runDir, $targetName);
        sleep(1);


        // Write genesis.php
        $session->write(' - ');
        $session->{'.green'}(self::RunDir . '/genesis.php');
        $this->writeGenesisPhp($runDir);



        // Enable entry file
        $session->write(' - ');
        $session->{'white|dim'}($targetName . '/' . $entryName . '.disabled');
        $session->write(' > ');
        $session->{'.green'}($targetName . '/' . $entryName);

        $source->getFile($entryName . '.disabled')->renameTo($entryName);


        // Disable active entry file
        if ($current !== null) {
            $session->write(' - ');
            $session->{'yellow'}($current->name . '/' . $entryName);
            $session->write(' > ');
            $session->{'.white|dim'}($current->name . '/' . $entryName . '.disabled');

            $current->getFile($entryName)->renameTo($entryName . '.disabled');
        }
    }

    protected function writeGenesisPhp(
        Dir $runDir,
    ): void {
        $entry = self::BuildEntry;
        $prefix = self::BuildPrefix;

        $runDir->getFile('genesis.php')->putContents(
            <<<PHP
            <?php

            /**
             * @package Fabric Seamless Bootstrap
             * @license http://opensource.org/licenses/MIT
             */

            declare(strict_types=1);

            \$args = \$_SERVER['argv'] ?? [];

            if(in_array('--from-source', \$args)) {
                return false;
            }

            if (file_exists(__DIR__.'/{$prefix}1/{$entry}')) {
                require_once __DIR__.'/{$prefix}1/{$entry}';
                return true;
            }

            if (file_exists(__DIR__.'/{$prefix}2/{$entry}')) {
                require_once __DIR__.'/{$prefix}2/{$entry}';
                return true;
            }

            return false;
            PHP
        );
    }


    public function clear(
        Session $session
    ): void {
        $runDir = Atlas::getDir(Monarch::getPaths()->root . '/' . self::RunDir);
        $entryName = self::BuildEntry;

        $runName1 = self::BuildPrefix . '1';
        $buildDir1 = $runDir->getDir($runName1);
        $entryFile1 = $buildDir1->getFile($entryName);

        $runName2 = self::BuildPrefix . '2';
        $buildDir2 = $runDir->getDir($runName2);
        $entryFile2 = $buildDir2->getFile($entryName);

        $found = false;

        $genesisFile = $runDir->getFile('genesis.php');

        if ($genesisFile->exists()) {
            $genesisFile->delete();
            $session->deleteSuccess((string)$genesisFile);
            $found = true;
        }


        if ($entryFile1->exists()) {
            $entryFile1->delete();
            $session->deleteSuccess((string)$entryFile1);
            $found = true;
        }

        if ($entryFile2->exists()) {
            $entryFile2->delete();
            $session->deleteSuccess((string)$entryFile2);
            $found = true;
        }

        if ($buildDir1->exists()) {
            $buildDir1->delete();
            $session->deleteSuccess((string)$buildDir1);
            $found = true;
        }

        if ($buildDir2->exists()) {
            $buildDir2->delete();
            $session->deleteSuccess((string)$buildDir2);
            $found = true;
        }

        if ($runDir->exists()) {
            $runDir->delete();
            $session->deleteSuccess((string)$runDir);
            $found = true;
        }

        if (!$found) {
            $session->info('No builds found');
        }

        $session->newLine();
    }
}
