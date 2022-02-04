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

namespace Tobento\Service\Responser;

use Tobento\Service\Filesystem\File;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * FileResponser
 */
class FileResponser implements FileResponserInterface
{
    /**
     * Create a new FileResponser.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
    ) {}

    /**
     * Create a new response.
     *
     * @param int $code
     * @return ResponseInterface
     */
    public function create(int $code = 200): ResponseInterface
    {
        return $this->responseFactory->createResponse($code);
    }
    
    /**
     * Returns the stream factory.
     *
     * @return StreamFactoryInterface
     */
    public function streamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }
    
    /**
     * Returns the response info.
     *
     * @param ResponseInterface $response
     * @return ResponseInfo
     */
    public function info(ResponseInterface $response): ResponseInfo
    {
        return new ResponseInfo($response);
    }
    
    /**
     * Download file response.
     *
     * @param mixed $file string|File|StreamInterface|resource
     * @param string $name
     * @param string $contentType
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function download(
        mixed $file,
        string $name = '',
        null|string $contentType = null
    ): ResponseInterface {
        
        if (is_string($file)) {
            return $this->fromFile(new File($file), $name, $contentType);
        }
        
        if ($file instanceof File) {
            return $this->fromFile($file, $name, $contentType);
        }
        
        if ($file instanceof StreamInterface) {
            return $this->fromStream($file, $name, $contentType);
        }
        
        if (is_resource($file)) {
            return $this->fromResource($file, $name, $contentType);
        }
        
        throw new InvalidArgumentException('Invalid file to download');
    }
    
    /**
     * Render file response.
     *
     * @param mixed $file string|File|StreamInterface|resource
     * @param string $name
     * @param null|string $contentType
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function render(
        mixed $file,
        string $name = '',
        null|string $contentType = null
    ): ResponseInterface {
        
        if (is_string($file)) {
            return $this->fromFile(new File($file), $name, $contentType, false);
        }
        
        if ($file instanceof File) {
            return $this->fromFile($file, $name, $contentType, false);
        }
        
        if ($file instanceof StreamInterface) {
            return $this->fromStream($file, $name, $contentType, false);
        }
        
        if (is_resource($file)) {
            return $this->fromResource($file, $name, $contentType, false);
        }
        
        throw new InvalidArgumentException('Invalid file to render');
    }
    
    /**
     * From file.
     *
     * @param File $file
     * @param string $name
     * @param null|string $contentType
     * @param bool $download
     * @return ResponseInterface
     */
    protected function fromFile(
        File $file,
        string $name = '',
        null|string $contentType = null,
        bool $download = true
    ): ResponseInterface {
        
        if ($download) {
            $response = $file->downloadResponse($this->create(), $this->streamFactory());
            
            if (!empty($name)) {
                $response = $response->withHeader(
                    'Content-Disposition',
                    'attachment; filename='.$name
                );
            }
        } else {
            $response = $file->fileResponse($this->create(), $this->streamFactory());
            
            if (!empty($name)) {
                $response = $response->withHeader(
                    'Content-Disposition',
                    'inline; filename='.$name
                );
            }            
        }
        
        if (!is_null($contentType)) {
            $response = $response->withHeader('Content-Type', $contentType);
        }
        
        return $response;
    }

    /**
     * From resource.
     *
     * @param resource $resource
     * @param string $name
     * @param null|string $contentType
     * @param bool $download
     * @return ResponseInterface
     */
    protected function fromResource(
        $resource,
        string $name = '',
        null|string $contentType = null,
        bool $download = true,
    ): ResponseInterface {
        
        if (is_null($contentType)) {
            $contentType = mime_content_type($resource);
            $contentType = is_string($contentType) ? $contentType : null;
        }
        
        return $this->fromStream(
            $this->streamFactory()->createStreamFromResource($resource),
            $name,
            $contentType,
            $download
        );
    }
    
    /**
     * From stream.
     *
     * @param StreamInterface $stream
     * @param string $name
     * @param null|string $contentType
     * @param bool $download     
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    protected function fromStream(
        StreamInterface $stream,
        string $name = '',
        null|string $contentType = null,
        bool $download = true,
    ): ResponseInterface {
        
        if (empty($name)) {
            throw new InvalidArgumentException('Invalid name provided');
        }
        
        if (is_null($contentType)) {
            $uri = $stream->getMetadata('uri');
            
            if (is_string($uri)) {
                $contentType = mime_content_type($uri);
                $contentType = is_string($contentType) ? $contentType : null;
            }
            
            if (is_null($contentType)) {
                $contentType = 'application/octet-stream';
            }
        }
        
        $response = $this->create()
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Length', (string)$stream->getSize())
            ->withBody($stream);
        
        if ($download) {
            $response = $response->withHeader('Content-Disposition', 'attachment; filename='.$name);
            return $response;
        }
        
        $response = $response->withHeader('Content-Disposition', 'inline; filename='.$name);
        return $response;
    }
}