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

namespace Tobento\Service\Responser\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\ViewRenderer;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * ResponserRendererTest
 */
class ResponserRendererTest extends TestCase
{
    protected function createResponser(): ResponserInterface
    {
        $psr17Factory = new Psr17Factory();
        
        $view = new View(
            new PhpRenderer(
                new Dirs(
                    new Dir(__DIR__.'/views/'),
                )
            )
        );
        
        $renderer = new ViewRenderer($view);
        
        return new Responser(
            responseFactory: $psr17Factory,
            streamFactory: $psr17Factory,
            renderer: $renderer,
        );
    }
    
    public function testRender()
    {
        $response = $this->createResponser()->render(
            view: 'article/about',
            data: ['title' => 'About'],
            code: 200,
            contentType: 'text/html; charset=utf-8',
        );
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>About</title></head><body>About</body></html>',
            (string)$response->getBody()
        );
        
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(['text/html; charset=utf-8'], $response->getHeader('Content-Type'));        
    }
}