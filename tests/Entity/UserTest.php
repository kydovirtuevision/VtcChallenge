<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserProperties()
    {
        $u = new User();
        $u->setEmail('a@b.c');
        $u->setPassword('secret');
        $u->setRoles(['ROLE_ADMIN']);
        $u->setIsVerified(true);
        $u->setConfirmationToken('tok');
        $u->setApiToken('apitok');

        $this->assertEquals('a@b.c', $u->getEmail());
        $this->assertEquals('secret', $u->getPassword());
        $this->assertContains('ROLE_USER', $u->getRoles());
        $this->assertTrue($u->isVerified());
        $this->assertEquals('tok', $u->getConfirmationToken());
        $this->assertEquals('apitok', $u->getApiToken());
    }
}
