# Responser Service

Providing PSR-7 response wrapper classes with simplified methods.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
- [Documentation](#documentation)
    - [Responser](#responser)
        - [Create Responser](#create-responser)
            - [Renderer](#renderer)
            - [Storage](#storage)
        - [Html Response](#html-response)
        - [Json Response](#json-response)
        - [Render View Response](#render-view-response)
        - [Any Content Response](#any-content-response)
        - [Redirect Response](#redirect-response)
        - [Messages](#messages)
            - [Flash Messages](#flash-messages)
        - [Flash Input Data](#flash-input-data)
        - [Additional Responser Methods](#additional-responser-methods)
    - [File Responser](#file-responser)
        - [Create File Responser](#create-file-responser)
        - [Render File Response](#render-file-response)
        - [Download File Response](#download-file-response)
        - [Additional File Responser Methods](#additional-file-responser-methods)
    - [Response Info](#response-info)
    - [Middleware](#middleware)
        - [Responser Middleware](#responser-middleware)
        - [Merge Input Middleware](#merge-input-middleware)
- [Credits](#credits)
___

# Getting started

Add the latest version of the requester service project running this command.

```
composer require tobento/service-responser
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design
- Flash messages
- Flash input data

# Documentation

## Responser

### Create Responser

```php
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\RendererInterface;
use Tobento\Service\Responser\StorageInterface;
use Tobento\Service\Message\MessagesInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

$psr17Factory = new Psr17Factory();

$responser = new Responser(
    responseFactory: $psr17Factory, // Any PSR-17 ResponseFactoryInterface
    streamFactory: $psr17Factory, // Any PSR-17 StreamFactoryInterface
    renderer: null, // null|RendererInterface
    storage: null, // null|StorageInterface
    messages: null, // null|MessagesInterface
);

var_dump($responser instanceof ResponserInterface);
// bool(true)
```

#### Renderer

You may add a renderer if you want to [Render View Responses](#render-view-response).

Firstly, make sure you have the view service installed if you want to use the view renderer, otherwise you may implement your own renderer:

```
composer require tobento/service-view
```

Check out the [View Service](https://github.com/tobento-ch/service-view) to learn more about it in general.

```php
use Tobento\Service\Responser\RendererInterface;
use Tobento\Service\Responser\ViewRenderer;
use Tobento\Service\View\View;
use Tobento\Service\View\PhpRenderer;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Dir\Dir;

$view = new View(
    new PhpRenderer(
        new Dirs(
            new Dir('/private/views/'),
        )
    )
);

$renderer = new ViewRenderer($view);

var_dump($renderer instanceof RendererInterface);
// bool(true)
```

#### Storage

You may add a storage if you want to flash messages and/or input data.

Firstly, make sure you have the session service installed if you want to use the session storage, otherwise you may implement your own storage:

```
composer require tobento/service-session
```

Check out the [Session Service](https://github.com/tobento-ch/service-session) to learn more about how to start session.

```php
use Tobento\Service\Responser\StorageInterface;
use Tobento\Service\Responser\SessionStorage;
use Tobento\Service\Session\Session;

$session = new Session('name');

$storage = new SessionStorage($session);

var_dump($storage instanceof StorageInterface);
// bool(true)
```

### Html Response

Writes HTML into the body response and sets "text/html; charset=utf-8" content-type header.

```php
use Psr\Http\Message\ResponseInterface;

$response = $responser->html(
    html: 'html',
    code: 200, // is default
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

### Json Response

Writes JSON data into the body response and sets "application/json" content-type header.

```php
use Psr\Http\Message\ResponseInterface;

$response = $responser->json(
    data: ['key' => 'value'],
    code: 200, // is default
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

### Render View Response

Renders the specified view writing into the body response and sets "text/html; charset=utf-8" content-type header as default.

Check out [Renderer](#renderer) to learn more about the renderer implementation.

```php
use Psr\Http\Message\ResponseInterface;

$response = $responser->render(
    view: 'shop/products',
    data: ['products' => []],
    code: 200, // is default
    contentType: 'text/html; charset=utf-8', // is default
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

### Any Content Response

Writes data into the body response.

```php
use Psr\Http\Message\ResponseInterface;

$response = $responser->write(
    data: 'data', // mixed
    code: 200, // is default
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

### Redirect Response

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Stringable;

$response = $responser->redirect(
    uri: 'uri', // string|Stringable|UriInterface
    code: 302, // is default
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

### Messages

You may add messages for the current and/or next request.

Check out the [Message Service](https://github.com/tobento-ch/service-message) to learn more about it in general.

```php
use Tobento\Service\Message\MessagesInterface;

var_dump($responser->messages() instanceof MessagesInterface);
// bool(true)

$responser->messages()->add('success', 'Success message');

$response = $responser->render(
    view: 'shop/products',
    data: [
        'products' => [],
        'messages' => $responser->messages(),
    ],
);
```

in views/shop/products.php

```php
<?php foreach($messages as $message) { ?>
    <?= $message ?>
<?php } ?>
```

#### Flash Messages

You will need to provide a storage in order to flash messages.\
Check out [Storage](#storage) to learn more about the storage implementation.

```php
$responser->messages()->add('error', 'Error message');

$response = $responser->redirect('uri');
```

On the redirected uri:

```php
$response = $responser->render(
    view: 'shop/products',
    data: [
        'products' => [],
        'messages' => $responser->messages(),
    ],
);
```

### Flash Input Data

You will need to provide a storage in order to flash input data.\
Check out [Storage](#storage) to learn more about the storage implementation.

```php
$response = $responser
    ->withInput(['key' => 'value'])
    ->redirect('uri');
```

On the redirected uri:

```php
$input = $responser->getInput();
```

**Using middleware**

You may check out the [Merge Input Middleware](#merge-input-middleware) to merge the input data with the request data.

### Additional Responser Methods

**create**

You may create a response from the response factory:

```php
use Psr\Http\Message\ResponseInterface;

$response = $responser->create(200);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

**streamFactory**

You may get the stream factory:

```php
use Psr\Http\Message\StreamFactoryInterface;

$streamFactory = $responser->streamFactory();

var_dump($streamFactory instanceof StreamFactoryInterface);
// bool(true)
```

**file**

You may get the file responser:

Check out [File Responser](#file-responser) to learn more about the file repsonser in general.

```php
use Tobento\Service\Responser\FileResponserInterface;

$file = $responser->file();

var_dump($file instanceof FileResponserInterface);
// bool(true)
```

**info**

You may get the response info:

Check out [Response Info](#response-info) to learn more about the response info in general.

```php
use Tobento\Service\Responser\ResponseInfo;

$info = $responser->info($responser->create(403));

var_dump($info instanceof ResponseInfo);
// bool(true)
```

## File Responser

### Create File Responser

```php
use Tobento\Service\Responser\FileResponser;
use Tobento\Service\Responser\FileResponserInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

$psr17Factory = new Psr17Factory();

$fileResponser = new FileResponser(
    responseFactory: $psr17Factory, // Any PSR-17 ResponseFactoryInterface
    streamFactory: $psr17Factory, // Any PSR-17 StreamFactoryInterface
);

var_dump($fileResponser instanceof FileResponserInterface);
// bool(true)
```

### Render File Response

Create response to render (display) the file on browser.

```php
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

$response = $fileResponser->render(
    file: 'file.jpg', // string|File|StreamInterface|resource
    name: 'File', // string
    contentType: 'image/jpeg', // null|string
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

**Parameters explanation**

| Parameter | Description |
| --- | --- |
| **file** | The file to render. |
| **name** | Required if file is of type StreamInterface or resource, otherwise you may leave empty. |
| **contentType** | If null, it gets determined automatically. |

### Download File Response

Create response to download the file.

```php
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

$response = $fileResponser->download(
    file: 'file.jpg' // string|File|StreamInterface|resource
    name: 'File', // string
    contentType: 'image/jpeg', // null|string    
);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

**Parameters explanation**

| Parameter | Description |
| --- | --- |
| **file** | The file to download. |
| **name** | Required if file is of type StreamInterface or resource, otherwise you may leave empty. |
| **contentType** | If null, it gets determined automatically. |

### Additional File Responser Methods

**create**

You may create a response from the response factory:

```php
use Psr\Http\Message\ResponseInterface;

$response = $fileResponser->create(200);

var_dump($response instanceof ResponseInterface);
// bool(true)
```

**streamFactory**

You may get the stream factory:

```php
use Psr\Http\Message\StreamFactoryInterface;

$streamFactory = $fileResponser->streamFactory();

var_dump($streamFactory instanceof StreamFactoryInterface);
// bool(true)
```

**info**

You may get the response info:

Check out [Response Info](#response-info) to learn more about the response info in general.

```php
use Tobento\Service\Responser\ResponseInfo;

$info = $fileResponser->info($fileResponser->create(403));

var_dump($info instanceof ResponseInfo);
// bool(true)
```

## Response Info

```php
use Tobento\Service\Responser\ResponseInfo;
use Psr\Http\Message\ResponseInterface;

$responseInfo = new ResponseInfo(
    response: $response // ResponseInterface
);
```

**isInformational**

If the response is informational, status codes **1xx**.

```php
var_dump($responseInfo->isInformational());
// bool(true)
```

**isSuccessful**

If the response is successfull, status codes **2xx**.

```php
var_dump($responseInfo->isSuccessful());
// bool(true)
```

**isRedirection**

If the response is a redirection, status codes **3xx**.

```php
var_dump($responseInfo->isRedirection());
// bool(true)
```

**isClientError**

If the response is a client error, status codes **4xx**.

```php
var_dump($responseInfo->isClientError());
// bool(true)
```

**isServerError**

If the response is a server error, status codes **5xx**.

```php
var_dump($responseInfo->isServerError());
// bool(true)
```

**isOk**

If the response is ok, status code **200**.

```php
var_dump($responseInfo->isOk());
// bool(true)
```

**isForbidden**

If the response is a forbidden error, status code **403**.

```php
var_dump($responseInfo->isForbidden());
// bool(true)
```

**isNotFound**

If the response is a not found error, status code **404**.

```php
var_dump($responseInfo->isNotFound());
// bool(true)
```

**isCode**

If the response is of the specified status code(s).

```php
var_dump($responseInfo->isCode(403, 404));
// bool(true)
```

## Middleware

### Responser Middleware

Adds the responser to the request attributes.

```php
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\Middleware;
use Tobento\Service\Middleware\MiddlewareDispatcher;
use Tobento\Service\Middleware\AutowiringMiddlewareFactory;
use Tobento\Service\Middleware\FallbackHandler;
use Tobento\Service\Container\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

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
    
    var_dump($responser instanceof ResponserInterface);
    // bool(true)
    
    return $handler->handle($request);
});

$request = $psr17Factory->createServerRequest('GET', 'https://example.com');

$response = $dispatcher->handle($request);
```

### Merge Input Middleware

Merges the responser input with the request input.

```php
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\Middleware;
use Tobento\Service\Middleware\MiddlewareDispatcher;
use Tobento\Service\Middleware\AutowiringMiddlewareFactory;
use Tobento\Service\Middleware\FallbackHandler;
use Tobento\Service\Container\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

$psr17Factory = new Psr17Factory();

$container = new Container();
$container->set(ResponserInterface::class, Responser::class)->construct($psr17Factory, $psr17Factory);

// create middleware dispatcher.
$dispatcher = new MiddlewareDispatcher(
    new FallbackHandler($psr17Factory->createResponse(404)),
    new AutowiringMiddlewareFactory($container) // any PSR-11 container
);

// Simulating Previous request
$dispatcher->add(Middleware\Responser::class);

$dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    
    $responser = $request->getAttribute(ResponserInterface::class);
    
    $responser->withInput(['key' => 'value']);
    
    return $handler->handle($request);
});

// Current request
$dispatcher->add(Middleware\ResponserMergeInput::class);

$dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    
    var_dump($request->getQueryParams()['key']);
    // string(5) "value"
    
    return $handler->handle($request);
});

$request = $psr17Factory->createServerRequest('GET', 'https://example.com');

$response = $dispatcher->handle($request);
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)