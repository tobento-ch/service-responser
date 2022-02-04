<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Responser\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\Middleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Tobento\Service\Middleware\MiddlewareDispatcher;
use Tobento\Service\Middleware\AutowiringMiddlewareFactory;
use Tobento\Service\Middleware\FallbackHandler;
use Tobento\Service\Container\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ResponserTest
 */
class ResponserTest extends TestCase
{
    public function testMiddleware()
    {
        $psr17Factory = new Psr17Factory();
        
        // create middleware dispatcher.
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler($psr17Factory->createResponse(404)),
            new AutowiringMiddlewareFactory(new Container()) // any PSR-11 container
        );
        
        $dispatcher->add(new Middleware\Responser(
            new Responser($psr17Factory, $psr17Factory)
        ));

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

            $responser = $request->getAttribute(ResponserInterface::class);

            $this->assertSame(
                true,
                $responser instanceof ResponserInterface
            ); 

            return $handler->handle($request);
        });

        $request = $psr17Factory->createServerRequest('GET', 'https://example.com');

        $response = $dispatcher->handle($request);     
    }
}