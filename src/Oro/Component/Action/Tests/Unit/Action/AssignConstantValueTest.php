<?php

namespace Oro\Component\Action\Tests\Unit\Action;

use Oro\Component\Action\Action\ActionInterface;
use Oro\Component\Action\Action\AssignConstantValue;
use Oro\Component\ConfigExpression\ContextAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class AssignConstantValueTest extends \PHPUnit\Framework\TestCase
{
    const TEST_CONSTANT = 'test_c';

    /**
     * @var ContextAccessor
     */
    protected $contextAccessor;

    /**
     * @var ActionInterface
     */
    protected $action;

    protected function setUp(): void
    {
        $this->contextAccessor = new ContextAccessor();

        $this->action = new AssignConstantValue($this->contextAccessor);
        $dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $this->action->setDispatcher($dispatcher);
    }

    /**
     * @dataProvider invalidOptionsDataProvider
     * @param array $options
     */
    public function testInitializeException(array $options)
    {
        $this->expectException(\Oro\Component\Action\Exception\InvalidParameterException::class);
        $this->action->initialize($options);
    }

    /**
     * @return array
     */
    public function invalidOptionsDataProvider()
    {
        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [['attribute' => 'attr', 'value' => 'val', 'other' => 4]],
            [['some' => 'test', 'value' => 2]],
            [['some' => 'test', 'attribute' => 'attr']],
        ];
    }

    /**
     * @dataProvider optionsDataProvider
     * @param array $options
     * @param string $attribute
     * @param string $value
     */
    public function testInitialize(array $options, $attribute, $value)
    {
        $this->assertSame($this->action, $this->action->initialize($options));

        $this->assertAttributeEquals($attribute, 'attribute', $this->action);
        $this->assertAttributeEquals($value, 'value', $this->action);
    }

    /**
     * @return array
     */
    public function optionsDataProvider()
    {
        return [
            [['attr', 'val'], 'attr', 'val'],
            [['attribute' => 'attr', 'value' => 'val'], 'attr', 'val'],
        ];
    }

    public function testExecuteIncorrectUnknownConstant()
    {
        $this->expectException(\Oro\Component\Action\Exception\InvalidParameterException::class);
        $this->expectExceptionMessage('Cannot evaluate value of "someValue", constant is not exist.');

        $value = new PropertyPath('val');
        $attribute = new PropertyPath('attr');

        $context = new \stdClass();
        $context->val = 'someValue';
        $context->attr = null;

        $this->action->initialize([$attribute, $value]);
        $this->action->execute($context);
    }

    public function testExecuteIncorrectNoClass()
    {
        $this->expectException(\Oro\Component\Action\Exception\InvalidParameterException::class);
        $this->expectExceptionMessage('Cannot evaluate value of "UnknownClass1000::someValue", class is not exist.');

        $value = new PropertyPath('val');
        $attribute = new PropertyPath('attr');

        $context = new \stdClass();
        $context->val = 'UnknownClass1000::someValue';
        $context->attr = null;

        $this->action->initialize([$attribute, $value]);
        $this->action->execute($context);
    }

    public function testExecuteException()
    {
        $this->expectException(\Oro\Component\Action\Exception\InvalidParameterException::class);
        $this->expectExceptionMessage(
            'Action "assign_constant_value" expects a string in parameter "value", array is given.'
        );

        $value = new PropertyPath('val');
        $attribute = new PropertyPath('attr');

        $context = new \stdClass();
        $context->val = ['test', 'other'];
        $context->attr = null;

        $this->action->initialize([$attribute, $value]);
        $this->action->execute($context);
    }

    public function testExecute()
    {
        $value = new PropertyPath('val');
        $attribute = new PropertyPath('attr');

        $context = new \stdClass();
        $context->val = 'Oro\Component\Action\Tests\Unit\Action\AssignConstantValueTest::TEST_CONSTANT';
        $context->attr = null;

        $this->action->initialize([$attribute, $value]);
        $this->action->execute($context);

        $this->assertEquals(self::TEST_CONSTANT, $context->attr);
    }
}
