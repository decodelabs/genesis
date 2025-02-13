<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Genesis\Context as Inst;
use DecodeLabs\Pandora\Container as ContainerPlugin;
use DecodeLabs\Genesis\Loader\Stack as LoaderPlugin;
use DecodeLabs\Genesis\Hub as HubPlugin;
use DecodeLabs\Genesis\Build as BuildPlugin;
use DecodeLabs\Genesis\Environment as EnvironmentPlugin;
use DecodeLabs\Genesis\Kernel as KernelPlugin;
use DecodeLabs\Veneer\Plugin\Wrapper as PluginWrapper;

class Genesis implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Genesis';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;
    public static ContainerPlugin $container;
    public static LoaderPlugin $loader;
    /** @var HubPlugin|PluginWrapper<HubPlugin> $hub */
    public static HubPlugin|PluginWrapper $hub;
    public static BuildPlugin $build;
    public static EnvironmentPlugin $environment;
    /** @var KernelPlugin|PluginWrapper<KernelPlugin> $kernel */
    public static KernelPlugin|PluginWrapper $kernel;

    public static function replaceContainer(ContainerPlugin $container): void {}
    public static function run(string $hubName, array $options = []): void {}
    public static function initialize(string $hubName, array $options = []): KernelPlugin {
        return static::$_veneerInstance->initialize(...func_get_args());
    }
    public static function execute(): void {}
    public static function shutdown(): void {}
    public static function getStartTime(): float {
        return static::$_veneerInstance->getStartTime();
    }
};
