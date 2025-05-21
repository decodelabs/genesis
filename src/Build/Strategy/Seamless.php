<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Strategy;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis\Bootstrap;
use DecodeLabs\Genesis\Bootstrap\Seamless as SeamlessBootstrap;
use DecodeLabs\Genesis\Build\Strategy;
use DecodeLabs\Terminus\Session;

class Seamless implements Strategy
{
    protected SeamlessBootstrap $bootstrap;

    public function __construct(
        Bootstrap $bootstrap
    ) {
        if(!$bootstrap instanceof SeamlessBootstrap) {
            throw Exceptional::InvalidArgument('Bootstrap must be Seamless');
        }

        $this->bootstrap = $bootstrap;
    }

    public function activate(
        Dir $source,
        Session $session
    ): void {
        // Prepare
        $runDir = Atlas::dir($this->bootstrap->rootPath.'/'.$this->bootstrap->runDir);
        $entryName = $this->bootstrap->buildEntry;

        $runName1 = $this->bootstrap->buildPrefix . '1';
        $buildDir1 = $runDir->getDir($runName1);
        $entryFile1 = $buildDir1->getFile($entryName);

        $runName2 = $this->bootstrap->buildPrefix . '2';
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
            $entryFile2->renameTo($entryName.'.disabled');
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


    public function clear(
        Session $session
    ): void {
        $runDir = Atlas::dir($this->bootstrap->rootPath.'/'.$this->bootstrap->runDir);
        $entryName = $this->bootstrap->buildEntry;

        $runName1 = $this->bootstrap->buildPrefix . '1';
        $buildDir1 = $runDir->getDir($runName1);
        $entryFile1 = $buildDir1->getFile($entryName);

        $runName2 = $this->bootstrap->buildPrefix . '2';
        $buildDir2 = $runDir->getDir($runName2);
        $entryFile2 = $buildDir2->getFile($entryName);

        $found = false;


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
