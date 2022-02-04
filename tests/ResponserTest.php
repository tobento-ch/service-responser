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
use Tobento\Service\Responser\FileResponserInterface;
use Tobento\Service\Responser\ResponseInfo;
use Tobento\Service\Message\MessagesInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * ResponserTest
 */
class ResponserTest extends TestCase
{
    protected function createResponser(): ResponserInterface
    {
        $psr17Factory = new Psr17Factory();

        return new Responser(
            responseFactory: $psr17Factory,
            streamFactory: $psr17Factory,
        );
    }
    
    public function testThatImplementsResponserInterface()
    {
        $this->assertInstanceof(
            ResponserInterface::class,
            $this->createResponser()
        );
    }
    
    public function testCreateMethod()
    {
        $response = $this->createResponser()->create(
            code: 404,
        );
        
        $this->assertSame(404, $response->getStatusCode());    
    }
    
    public function testStreamFactoryMethod()
    {
        $this->assertInstanceof(
            StreamFactoryInterface::class,
            $this->createResponser()->streamFactory()
        );
    }
    
    public function testFileMethod()
    {
        $this->assertInstanceof(
            FileResponserInterface::class,
            $this->createResponser()->file()
        );
    }
    
    public function testInfoMethod()
    {
        $response = $this->createResponser()->create();
        
        $this->assertInstanceof(
            ResponseInfo::class,
            $this->createResponser()->info($response)
        );
    }    
    
    public function testHtmlMethod()
    {
        $response = $this->createResponser()->html(
            html: 'html content',
            code: 200,
        );
        
        $this->assertSame('html content', (string)$response->getBody());
        
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(['text/html; charset=utf-8'], $response->getHeader('Content-Type'));        
    }
    
    public function testJsonMethod()
    {
        $response = $this->createResponser()->json(
            data: ['key' => 'value'],
            code: 200,
        );
        
        $this->assertSame('{"key":"value"}', (string)$response->getBody());
        
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));        
    }
    
    public function testWriteMethodWithStringData()
    {
        $response = $this->createResponser()->write(
            data: 'content',
            code: 200,
        );
        
        $this->assertSame('content', (string)$response->getBody());
        
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame([], $response->getHeader('Content-Type'));
    }
    
    public function testWriteMethodWithJsonableData()
    {
        $response = $this->createResponser()->write(
            data: ['key' => 'value'],
            code: 200,
        );
        
        $this->assertSame('{"key":"value"}', (string)$response->getBody());
        
        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
    }
    
    public function testRedirectMethod()
    {
        $response = $this->createResponser()->redirect(
            uri: 'example.com',
        );
        
        $this->assertSame('example.com', $response->getHeaderLine('Location'));
        
        $this->assertSame(302, $response->getStatusCode());
        
        $response = $this->createResponser()->redirect(
            uri: 'example.com',
            code: 307,
        );        

        $this->assertSame(307, $response->getStatusCode());
    }
    
    public function testInputData()
    {
        $responser = $this->createResponser()->withInput(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $responser->getInput());        
    }
    
    public function testMessagesMethod()
    { 
        $this->assertInstanceof(
            MessagesInterface::class,
            $this->createResponser()->messages()
        );     
    }    
}