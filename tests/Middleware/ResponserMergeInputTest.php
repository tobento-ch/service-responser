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
 * ResponserMergeInputTest
 */
class ResponserMergeInputTest extends TestCase
{
    public function testMergesGetQueryParams()
    {
        $psr17Factory = new Psr17Factory();
        
        // create middleware dispatcher.
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler($psr17Factory->createResponse(404)),
            new AutowiringMiddlewareFactory(new Container()) // any PSR-11 container
        );
        
        $responser = new Responser($psr17Factory, $psr17Factory);
        
        $dispatcher->add(new Middleware\Responser($responser));
        
        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

            $responser = $request->getAttribute(ResponserInterface::class);
            
            $responser->withInput(['key' => 'value']);

            return $handler->handle($request);
        });
        
        $dispatcher->add(new Middleware\ResponserMergeInput($responser));

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                
            $this->assertSame(
                ['key' => 'value'],
                $request->getQueryParams()
            );

            return $handler->handle($request);
        });
        
        $request = $psr17Factory->createServerRequest('GET', 'https://example.com');

        $response = $dispatcher->handle($request);     
    }
    
    public function testMergesParsedBody()
    {
        $psr17Factory = new Psr17Factory();
        
        // create middleware dispatcher.
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler($psr17Factory->createResponse(404)),
            new AutowiringMiddlewareFactory(new Container()) // any PSR-11 container
        );
        
        $responser = new Responser($psr17Factory, $psr17Factory);
        
        $dispatcher->add(new Middleware\Responser($responser));
        
        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

            $responser = $request->getAttribute(ResponserInterface::class);
            
            $responser->withInput(['key' => 'value']);

            return $handler->handle($request);
        });
        
        $dispatcher->add(new Middleware\ResponserMergeInput($responser));

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                
            $this->assertSame(
                ['key' => 'value'],
                $request->getParsedBody()
            );

            return $handler->handle($request);
        });
        
        $request = $psr17Factory->createServerRequest('POST', 'https://example.com');
        $request = $request->withParsedBody([]);

        $response = $dispatcher->handle($request);     
    }    
}