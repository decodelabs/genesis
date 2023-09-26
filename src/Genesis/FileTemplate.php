<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\File;
use DecodeLabs\Exceptional;

class FileTemplate
{
    /**
     * @var array<string, ?string>
     */
    protected array $slots = [];

    protected File $templateFile;

    public function __construct(
        string|File $templateFile
    ) {
        $this->templateFile = Atlas::file($templateFile);

        if (!$this->templateFile->exists()) {
            throw Exceptional::Runtime('Template file could not be found');
        }
    }


    /**
     * Set slots
     *
     * @param array<string, string> $slots
     * @return $this
     */
    public function setSlots(array $slots): static
    {
        foreach ($slots as $name => $slot) {
            $this->setSlot($name, $slot);
        }

        return $this;
    }

    /**
     * Get slots
     *
     * @return array<string, ?string>
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * Set slot
     *
     * @return $this;
     */
    public function setSlot(
        string $name,
        string $slot
    ): static {
        $this->slots[$name] = $slot;
        return $this;
    }

    /**
     * Get slot
     */
    public function getSlot(string $name): ?string
    {
        if (array_key_exists($name, $this->slots)) {
            return $this->slots[$name];
        }

        return $this->slots[$name] = $this->generateSlot($name);
    }

    protected function generateSlot(string $name): ?string
    {
        switch ($name) {
            case 'date':
                return date('Y-m-d');
        }

        return null;
    }


    /**
     * Interpolate and save to file
     */
    public function saveTo(
        string|File $file
    ): File {
        $content = (string)preg_replace_callback('/{{ ?([a-zA-Z0-9_]+) ?}}/', function ($matches) {
            $name = $matches[1];
            $output = $this->getSlot($name);

            if ($output === null) {
                $output = $matches[0];
            }

            return $output;
        }, $this->templateFile->getContents());

        $content = (string)preg_replace('/^\#\!(.*)\n/m', '', $content);

        $file = Atlas::file($file);
        $file->putContents($content);

        return $file;
    }
}
