<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Router;

use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Router\RouterConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use Spryker\Zed\Router\Business\Generator\UrlGenerator;
use Spryker\Zed\Router\Business\UrlMatcher\CompiledUrlMatcher;

/**
 * @method \Spryker\Shared\Router\RouterConfig getSharedConfig()
 */
class RouterConfig extends AbstractBundleConfig
{
    /**
     * Specification:
     * - Returns a Router configuration which makes use of a Router cache.
     *
     * @api
     *
     * @see \Symfony\Component\Routing\Router::setOptions()
     *
     * @return array
     */
    public function getRouterConfiguration(): array
    {
        return [
            'cache_dir' => $this->getCachePathIfCacheEnabled(),
            'generator_class' => UrlGenerator::class,
            'matcher_class' => CompiledUrlMatcher::class,
        ];
    }

    /**
     * Specification:
     * - Returns a Router configuration which does not make use of a Router cache.
     * - Fallback for development which is executed when the cached Router is not able to match.
     *
     * @api
     *
     * @see \Symfony\Component\Routing\Router::setOptions()
     *
     * @return array
     */
    public function getDevelopmentRouterConfiguration(): array
    {
        $routerConfiguration = $this->getRouterConfiguration();
        $routerConfiguration['cache_dir'] = null;

        return $routerConfiguration;
    }

    /**
     * @return string|null
     */
    protected function getCachePathIfCacheEnabled(): ?string
    {
        if ($this->get(RouterConstants::ZED_IS_CACHE_ENABLED, true)) {
            return $this->get(RouterConstants::ZED_CACHE_PATH, $this->getSharedConfig()->getDefaultRouterCachePath());
        }

        return null;
    }

    /**
     * Specification:
     * - Returns an array of directories where Controller are placed.
     * - Used to build to Router cache.
     *
     * @api
     *
     * @return string[]
     */
    public function getControllerDirectories(): array
    {
        $controllerDirectories = [];

        foreach ($this->get(KernelConstants::PROJECT_NAMESPACES) as $projectNamespace) {
            $controllerDirectories[] = sprintf('%s/%s/Zed/*/Communication/Controller/', rtrim(APPLICATION_SOURCE_DIR, '/'), $projectNamespace);
        }

        foreach ($this->get(KernelConstants::CORE_NAMESPACES) as $coreNamespace) {
            $composerPackageNamespace = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $coreNamespace));

            $controllerDirectories[] = sprintf(
                '%s/%s/*/src/%s/Zed/*/Communication/Controller/',
                rtrim(APPLICATION_VENDOR_DIR, '/'),
                $composerPackageNamespace,
                $coreNamespace
            );
        }

        return array_filter($controllerDirectories, 'glob');
    }

    /**
     * Specification:
     * - Returns if the SSl is enabled.
     * - When it is enabled and the current request is not secure, the Router will redirect to a secured URL.
     *
     * @api
     *
     * @return bool
     */
    public function isSslEnabled(): bool
    {
        return $this->get(RouterConstants::ZED_IS_SSL_ENABLED, true);
    }

    /**
     * Specification:
     * - Returns SSl excluded Route names.
     * - When SSL is enabled and the current Route name is excluded, the Router will not redirect to a secured URL.
     *
     * @api
     *
     * @return string[]
     */
    public function getSslExcludedRouteNames(): array
    {
        return $this->get(RouterConstants::ZED_SSL_EXCLUDED_ROUTE_NAMES, []);
    }
}
