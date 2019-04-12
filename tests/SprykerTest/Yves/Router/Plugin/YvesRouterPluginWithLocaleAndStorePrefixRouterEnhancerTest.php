<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Yves\Router\Plugin;

use Codeception\Test\Unit;
use Spryker\Yves\Router\Plugin\Router\YvesRouterPlugin;
use Spryker\Yves\Router\Plugin\RouterEnhancer\LocalePrefixRouterEnhancerPlugin;
use Spryker\Yves\Router\Plugin\RouterEnhancer\StorePrefixRouterEnhancerPlugin;
use Spryker\Yves\Router\UrlMatcher\RedirectableUrlMatcher;
use SprykerTest\Yves\Router\Plugin\Fixtures\RouteProviderPlugin;
use Symfony\Component\Routing\RequestContext;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Yves
 * @group Router
 * @group Plugin
 * @group YvesRouterPluginWithLocaleAndStorePrefixRouterEnhancerTest
 * Add your own group annotations below this line
 */
class YvesRouterPluginWithLocaleAndStorePrefixRouterEnhancerTest extends Unit
{
    /**
     * @var \SprykerTest\Yves\Router\RouterYvesTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->mockConfigMethod(
            'getRouterConfiguration',
            [
                'cache_dir' => null,
                'generator_cache_class' => 'YvesUrlGenerator',
                'matcher_cache_class' => 'YvesUrlMatcher',
                'matcher_base_class' => RedirectableUrlMatcher::class,
            ]
        );

        $this->tester->mockFactoryMethod('getRouteProviderPlugins', [
            new RouteProviderPlugin(),
        ]);

        $this->tester->mockFactoryMethod('getRouterEnhancerPlugins', [
            new LocalePrefixRouterEnhancerPlugin(),
            new StorePrefixRouterEnhancerPlugin(),
        ]);
    }

    /**
     * @return void
     */
    public function testMatchReturnsParameterWithLocaleAndStore(): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $router = $routerPlugin->getRouter();

        $parameters = $router->match('/de/DE/foo');

        $this->assertSame('foo', $parameters['_route']);
        $this->assertSame('de', $parameters['locale']);
        $this->assertSame('DE', $parameters['store']);
    }

    /**
     * @return void
     */
    public function testGenerateReturnsUrlWithLocaleAndStoreWhenLocaleAndStoreAreInContextParameter(): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $requestContext = new RequestContext();
        $requestContext->setParameter('locale', 'de');
        $requestContext->setParameter('store', 'DE');

        $router = $routerPlugin->getRouter();
        $router->setContext($requestContext);

        $url = $router->generate('foo');

        $this->assertSame('/de/DE/foo', $url);
    }

    /**
     * @return void
     */
    public function testGenerateReturnsUrlWithoutLocaleAndStoreWhenLocaleAndStoreAreNotInContextParameter(): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $router = $routerPlugin->getRouter();

        $url = $router->generate('foo');

        $this->assertSame('/foo', $url);
    }
}
