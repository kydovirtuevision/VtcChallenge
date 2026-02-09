<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Note;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    public function testNoteProperties()
    {
        $u = new User();
        $u->setEmail('x@y.z');

        $n = new Note();
        $n->setTitle('T');
        $n->setContent('C');
        $n->setCategory('cat');
        $n->setStatus('todo');
        $n->setOwner($u);

        $this->assertEquals('T', $n->getTitle());
        $this->assertEquals('C', $n->getContent());
        $this->assertEquals('cat', $n->getCategory());
        $this->assertEquals('todo', $n->getStatus());
        $this->assertSame($u, $n->getOwner());
    }
}
