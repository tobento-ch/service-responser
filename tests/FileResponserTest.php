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
use Tobento\Service\Responser\FileResponser;
use Tobento\Service\Responser\FileResponserInterface;
use Tobento\Service\Responser\ResponseInfo;
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\StreamFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * FileResponserTest
 */
class FileResponserTest extends TestCase
{
    protected function createFileResponser(): FileResponserInterface
    {
        $psr17Factory = new Psr17Factory();

        return new FileResponser(
            responseFactory: $psr17Factory,
            streamFactory: $psr17Factory,
        );
    }
    
    public function testThatImplementsFileResponserInterface()
    {
        $this->assertInstanceof(
            FileResponserInterface::class,
            $this->createFileResponser()
        );
    }
    
    public function testCreateMethod()
    {
        $response = $this->createFileResponser()->create(
            code: 404,
        );
        
        $this->assertSame(404, $response->getStatusCode());    
    }
    
    public function testStreamFactoryMethod()
    {
        $this->assertInstanceof(
            StreamFactoryInterface::class,
            $this->createFileResponser()->streamFactory()
        );
    }
    
    public function testInfoMethod()
    {
        $response = $this->createFileResponser()->create();
        
        $this->assertInstanceof(
            ResponseInfo::class,
            $this->createFileResponser()->info($response)
        );
    }    
    
    public function testRenderMethodWithString()
    {
        $response = $this->createFileResponser()->render(
            file: __FILE__,
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'inline; filename=FileResponserTest.php',
            (string)$response->getHeaderLine('Content-Disposition')
        );
    }
    
    public function testRenderMethodWithStringAndName()
    {
        $response = $this->createFileResponser()->render(
            file: __FILE__,
            name: 'File',
        );
        
        $this->assertSame(
            'inline; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );
    }    
    
    public function testRenderMethodWithFile()
    {
        $response = $this->createFileResponser()->render(
            file: new File(__FILE__),
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'inline; filename=FileResponserTest.php',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testRenderMethodWithFileAndName()
    {
        $response = $this->createFileResponser()->render(
            file: new File(__FILE__),
            name: 'File',
        );
        
        $this->assertSame(
            'inline; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }    
    
    public function testRenderMethodWithStream()
    {
        $responser = $this->createFileResponser();
        $stream = $responser->streamFactory()->createStreamFromFile(__FILE__);
        
        $response = $responser->render(
            file: $stream,
            name: 'File',
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'inline; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testRenderMethodWithResource()
    {
        $response = $this->createFileResponser()->render(
            file: fopen(__FILE__, 'r'),
            name: 'File',
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'inline; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testRenderMethodWithSpecifiedContentType()
    {
        $response = $this->createFileResponser()->render(
            file: __FILE__,
            contentType: 'application/octet-stream',
        );
        
        $this->assertSame('application/octet-stream', (string)$response->getHeaderLine('Content-Type'));       
    }
    
    public function testDownloadMethodWithString()
    {
        $response = $this->createFileResponser()->download(
            file: __FILE__,
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'attachment; filename=FileResponserTest.php',
            (string)$response->getHeaderLine('Content-Disposition')
        );
    }
    
    public function testDownloadMethodWithStringAndName()
    {
        $response = $this->createFileResponser()->download(
            file: __FILE__,
            name: 'File',
        );
        
        $this->assertSame(
            'attachment; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );
    }    
    
    public function testDownloadMethodWithFile()
    {
        $response = $this->createFileResponser()->download(
            file: new File(__FILE__),
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'attachment; filename=FileResponserTest.php',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testDownloadMethodWithFileAndName()
    {
        $response = $this->createFileResponser()->download(
            file: new File(__FILE__),
            name: 'File',
        );
        
        $this->assertSame(
            'attachment; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }    
    
    public function testDownloadMethodWithStream()
    {
        $responser = $this->createFileResponser();
        $stream = $responser->streamFactory()->createStreamFromFile(__FILE__);
        
        $response = $responser->download(
            file: $stream,
            name: 'File',
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'attachment; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testDownloadMethodWithResource()
    {
        $response = $this->createFileResponser()->download(
            file: fopen(__FILE__, 'r'),
            name: 'File',
        );

        $this->assertSame(200, $response->getStatusCode());
        
        $this->assertStringEqualsFile(__FILE__, (string)$response->getBody());
        
        $this->assertSame(filesize(__FILE__), $response->getBody()->getSize());
        
        $this->assertSame('text/x-php', (string)$response->getHeaderLine('Content-Type'));
        
        $this->assertSame(
            'attachment; filename=File',
            (string)$response->getHeaderLine('Content-Disposition')
        );        
    }
    
    public function testDownloadMethodWithSpecifiedContentType()
    {
        $response = $this->createFileResponser()->download(
            file: __FILE__,
            contentType: 'application/octet-stream',
        );
        
        $this->assertSame('application/octet-stream', (string)$response->getHeaderLine('Content-Type'));       
    }    
}