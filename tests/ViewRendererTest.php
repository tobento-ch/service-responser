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
use Tobento\Service\Responser\RendererInterface;
use Tobento\Service\Responser\ViewRenderer;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;

/**
 * ViewRendererTest
 */
class ViewRendererTest extends TestCase
{
    protected function createRenderer(): RendererInterface
    {
        $view = new View(
            new PhpRenderer(
                new Dirs(
                    new Dir(__DIR__.'/views/'),
                )
            )
        );
        
        return new ViewRenderer($view);
    }  
    
    public function testRender()
    {
        $output = $this->createRenderer()->render(
            view: 'article/about',
            data: ['title' => 'About'],
        );
        
        $this->assertSame(
            '<!DOCTYPE html><html><head><title>About</title></head><body>About</body></html>',
            $output
        );  
    }
}