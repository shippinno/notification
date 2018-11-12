<?php

namespace Shippinno\Notification\Domain\Model;

use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;

class NotificationMetadataSpecificationTest extends TestCase
{
    public function testEQ()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::EQ, 'c');
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'c']
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'z']
                ])->build()
            )
        );
    }

    public function testNEQ()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::NEQ, 'c');
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'z']
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'c']
                ])->build()
            )
        );
    }

    public function testCONTAINS()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::CONTAINS, 'c');
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'abcde']
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'zyxwv']
                ])->build()
            )
        );
    }

    public function testGT()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::GT, 1);
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 2]
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 1]
                ])->build()
            )
        );
    }

    public function testGTE()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::GTE, 1);
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 1]
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 0]
                ])->build()
            )
        );
    }

    public function testLT()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::LT, 1);
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 0]
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 1]
                ])->build()
            )
        );
    }

    public function testLTE()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::LTE, 1);
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 1]
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 2]
                ])->build()
            )
        );
    }

    public function testIN()
    {
        $specification = new NotificationMetadataSpecification('a.b', Comparison::IN, ['a', 'b', 'c']);
        $this->assertTrue(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'c']
                ])->build()
            )
        );
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([
                    'a' => ['b' => 'z']
                ])->build()
            )
        );
    }

    public function testItIsNotSatisfiedByNotificationWithoutSpecifiedKey()
    {
        $specification = new NotificationMetadataSpecification('XXX', Comparison::EQ, 'value');
        $this->assertFalse(
            $specification->isSatisfiedBy(
                NotificationBuilder::notification()->withMetadata([])->build()
            )
        );
    }
}
