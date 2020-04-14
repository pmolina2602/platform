<?php

namespace Oro\Component\Action\Tests\Unit\Action;

use Oro\Component\Action\Action\RefreshGrid;
use Oro\Component\Action\Tests\Unit\Action\Stub\StubStorage;
use Oro\Component\ConfigExpression\ContextAccessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class RefreshGridTest extends \PHPUnit\Framework\TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var RefreshGrid */
    protected $action;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->action = new RefreshGrid(new ContextAccessor());
        $this->action->setDispatcher($this->eventDispatcher);
    }

    protected function tearDown(): void
    {
        unset($this->action, $this->eventDispatcher);
    }

    public function testInitialize()
    {
        $gridname = 'test_grid';

        $this->assertInstanceOf(
            'Oro\Component\Action\Action\ActionInterface',
            $this->action->initialize([$gridname])
        );

        $this->assertAttributeEquals([$gridname], 'gridNames', $this->action);
    }

    public function testInitializeException()
    {
        $this->expectException(\Oro\Component\Action\Exception\InvalidParameterException::class);
        $this->expectExceptionMessage('Gridname parameter must be specified');

        $this->action->initialize([]);
    }

    /**
     * @param array $inputData
     * @param array $expectedData
     *
     * @dataProvider executeMethodProvider
     */
    public function testExecuteMethod(array $inputData, array $expectedData)
    {
        $context = new StubStorage($inputData['context']);

        $this->action->initialize($inputData['options']);
        $this->action->execute($context);

        $this->assertEquals($expectedData, $context->getValues());
    }

    /**
     * @return array
     */
    public function executeMethodProvider()
    {
        return [
            'add grid' => [
                'input' => [
                    'context' => ['param1' => 'value1'],
                    'options' => ['grid1']
                ],
                'expected' => ['param1' => 'value1', 'refreshGrid' => ['grid1']],
            ],
            'merge grids' => [
                'input' => [
                    'context' => ['param2' => 'value2', 'refreshGrid' => ['grid1']],
                    'options' => ['grid2', 'grid2']
                ],
                'expected' => ['param2' => 'value2', 'refreshGrid' => ['grid1', 'grid2']],
            ],
            'with property path' => [
                'input' => [
                    'context' => [
                        'param2' => 'value2',
                        'refreshGrid' => ['grid1'],
                        'testPropertyPath' => 'propertyPathData'
                    ],
                    'options' => ['grid2', new PropertyPath('testPropertyPath')]
                ],
                'expected' => [
                    'param2' => 'value2',
                    'refreshGrid' => ['grid1', 'grid2', 'propertyPathData'],
                    'testPropertyPath' => 'propertyPathData'
                ]
            ]
        ];
    }
}
