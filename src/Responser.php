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

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Tobento\Service\Message\HasMessages;
use Tobento\Service\Message\MessagesInterface;
use Tobento\Service\Support\Arrayable;
use Tobento\Service\Support\Renderable;
use JsonSerializable;
use ArrayObject;
use Stringable;

/**
 * Responser
 */
class Responser implements ResponserInterface
{
    use HasMessages;

    /**
     * The messages key to store the data.
     */
    protected const STORAGE_MESSAGES_KEY = '_responser_messages';
    
    /**
     * The input key to store the data.
     */
    protected const STORAGE_INPUT_KEY = '_responser_input';
    
    /**
     * @var array The input data.
     */
    protected array $input = [];
    
    /**
     * Create a new Responser.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     * @param null|RendererInterface $renderer
     * @param null|StorageInterface $storage
     * @param null|MessagesInterface $messages
     */
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected null|RendererInterface $renderer = null,
        protected null|StorageInterface $storage = null,
        null|MessagesInterface $messages = null,
    ) {
        $this->messages = $messages;
        
        // Get previous flashed data and assign it.
        $this->assignPreviousFlashedData();
    }
    
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
     * Returns the file responser.
     *
     * @return FileResponserInterface
     */
    public function file(): FileResponserInterface
    {
        return new FileResponser($this->responseFactory, $this->streamFactory);
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
     * Redirect response.
     *
     * @param string|Stringable|UriInterface $uri
     * @param int $code
     * @return ResponseInterface
     */
    public function redirect(string|Stringable|UriInterface $uri, int $code = 302): ResponseInterface
    {
        $this->flashing();
        
        return $this->create($code)->withHeader('Location', (string)$uri);
    }
    
    /**
     * Write html into the body response.
     *
     * @param string $html
     * @param int $code
     * @param string $contentType
     * @return ResponseInterface
     */
    public function html(
        string $html,
        int $code = 200,
        string $contentType = 'text/html; charset=utf-8'
    ): ResponseInterface {
        $response = $this->create($code);
        $response->getBody()->write($html);
        
        return $response->withHeader('Content-Type', $contentType);
    }
    
    /**
     * Write json data into the body response.
     *
     * @param mixed $data
     * @param int $code
     * @return ResponseInterface
     */
    public function json(mixed $data, int $code = 200): ResponseInterface
    {
        $response = $this->create($code);
        
        $response->getBody()->write($this->convertToJson($data));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Render view and write to the body response.
     *
     * @param string $view
     * @param array $data
     * @param int $code
     * @param string $contentType
     * @return ResponseInterface
     */
    public function render(
        string $view,
        array $data = [],
        int $code = 200,
        string $contentType = 'text/html; charset=utf-8'
    ): ResponseInterface {
        $response = $this->create($code);
        
        if ($this->renderer) {
            $response->getBody()->write($this->renderer->render($view, $data));
        }
        
        return $response->withHeader('Content-Type', $contentType);
    }

    /**
     * Write data into the body response.
     *
     * @param mixed $data
     * @param int $code
     * @return ResponseInterface
     */
    public function write(mixed $data, int $code = 200): ResponseInterface
    {
        $response = $this->create($code);
        
        if ($this->isJsonable($data)) {
            $response->getBody()->write($this->convertToJson($data));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        if ($data instanceof Renderable) {
            $response->getBody()->write($data->render());
            return $response;
        }
        
        if (is_string($data) || $data instanceof Stringable) {
            $response->getBody()->write((string)$data);
            return $response;
        }    

        return $response;
    }
    
    /**
     * Response with input data.
     *
     * @param array $input
     * @return static $this
     */
    public function withInput(array $input): static
    {
        $this->input = $input;
        
        return $this;
    }

    /**
     * Returns the input data.
     *
     * @return array
     */
    public function getInput(): array
    {
        return $this->input;
    }
            
    /**
     * Can the content be converted into JSON.
     *
     * @param mixed $data
     * @return bool True is jsonable, otherwise false
     */
    protected function isJsonable(mixed $data): bool
    {
        return $data instanceof Arrayable ||
               $data instanceof ArrayObject ||
               $data instanceof JsonSerializable ||
               is_array($data);
    }
    
    /**
     * Converts the data into JSON.
     *
     * @param mixed $data
     * @return string JSON string
     */
    protected function convertToJson(mixed $data): string
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return json_encode($data);
    }
    
    /**
     * Assign the previous flashed data.
     *
     * @return void
     */
    protected function assignPreviousFlashedData(): void
    {
        if (is_null($this->storage)) {
            return;
        }
        
        // assign messages
        $messages = $this->storage->get(self::STORAGE_MESSAGES_KEY, []);
        
        if (is_array($messages)) {
            $this->messages()->push($messages);
        }
        
        // assign input
        $input = $this->storage->get(self::STORAGE_INPUT_KEY, []);

        if (is_array($input)) {
            $this->input = $input;
        }
    }

    /**
     * Flashing data.
     *
     * @return void
     *
     * @psalm-suppress UndefinedInterfaceMethod
     */
    protected function flashing(): void
    {
        if (is_null($this->storage)) {
            return;
        }
        
        if ($this->messages() instanceof Arrayable) {
            $this->storage->flash(self::STORAGE_MESSAGES_KEY, $this->messages()->toArray());   
        }
        
        $this->storage->flash(self::STORAGE_INPUT_KEY, $this->input);
    }
}