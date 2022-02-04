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
use Tobento\Service\Responser\StorageInterface;
use Tobento\Service\Responser\SessionStorage;
use Tobento\Service\Session\Session;
use Tobento\Service\Session\SessionStartException;
use Tobento\Service\Session\SessionSaveException;

/**
 * SessionStorageTest
 */
class SessionStorageTest extends TestCase
{
    public function testThatImplementsStorageInterface()
    {
        $session = new Session('name');
        $storage = new SessionStorage($session);
        
        $this->assertInstanceof(
            StorageInterface::class,
            $storage
        );
    }
    
    public function testFlash()
    {
        $session = new Session('name');
        $storage = new SessionStorage($session);
        
        // current request        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame(null, $storage->get('key'));
        
        $storage->flash('key', 'value');
        
        $this->assertSame('value', $storage->get('key'));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // next request        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame('value', $storage->get('key'));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // after next request        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame(null, $storage->get('key'));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }        
    }
}