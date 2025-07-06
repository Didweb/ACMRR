<?php
namespace App\Tests\Entity\RecordLabel;

use App\Entity\RecordLabel;
use PHPUnit\Framework\TestCase;

class RecordLabelTest  extends TestCase
{
    public function testIdCanBeSetAndRetrieved(): void
    {
        $label = new RecordLabel();

        $label->setId(123);
        $this->assertSame(123, $label->getId());
    }

    public function testNameCanBeSetAndRetrieved(): void
    {
        $label = new RecordLabel();

        $label->setName('Sony Music');
        $this->assertSame('Sony Music', $label->getName());
    }

    public function testFluentInterface(): void
    {
        $label = new RecordLabel();

        $result = $label->setName('Universal');
        $this->assertSame($label, $result);

        $result = $label->setId(456);
        $this->assertSame($label, $result);
    }
}