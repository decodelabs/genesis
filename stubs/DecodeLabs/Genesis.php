<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Genesis\Context as Inst;
use DecodeLabs\Genesis\Bootstrap as BootstrapPlugin;
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
    /** @var BootstrapPlugin|PluginWrapper<BootstrapPlugin> $bootstrap */
    public static BootstrapPlugin|PluginWrapper $bootstrap;
    public static LoaderPlugin $loader;
    /** @var HubPlugin|PluginWrapper<HubPlugin> $hub */
    public static HubPlugin|PluginWrapper $hub;
    public static BuildPlugin $build;
    public static EnvironmentPlugin $environment;
    /** @var KernelPlugin|PluginWrapper<KernelPlugin> $kernel */
    public static KernelPlugin|PluginWrapper $kernel;

    public static function bootstrap(BootstrapPlugin $bootstrap): KernelPlugin {
        return static::$_veneerInstance->bootstrap(...func_get_args());
    }
    public static function bootstrapAndRun(BootstrapPlugin $bootstrap): void {}
    public static function getStartTime(): float {
        return static::$_veneerInstance->getStartTime();
    }
};
