<?php
namespace Oro\Bundle\FlexibleEntityBundle\Test\Entity;

use Oro\Bundle\FlexibleEntityBundle\Tests\Entity\Demo\FlexibleAttributeValue;
use Oro\Bundle\FlexibleEntityBundle\Tests\Entity\Demo\Flexible;
use Oro\Bundle\FlexibleEntityBundle\Model\Attribute\Type\AbstractAttributeType;
use Oro\Bundle\FlexibleEntityBundle\Entity\Attribute;
use Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption;

/**
 * Test related demo class, aims to cover abstract one
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */
class FlexibleAttributeValueTest extends \PHPUnit_Framework_TestCase
{

    protected $flexible;

    protected $attribute;

    protected $value;

    /**
     * Set up unit test
     */
    public function setUp()
    {
        // create flexible
        $this->flexible = new Flexible();
        // create attribute
        $this->attribute = new Attribute();
        $this->attribute->setCode('mycode');
        $this->attribute->setBackendType(AbstractAttributeType::BACKEND_TYPE_VARCHAR);
        // create value
        $this->value = new FlexibleAttributeValue();
        $this->value->setAttribute($this->attribute);
        $this->value->setEntity($this->flexible);
    }

    /**
     * Test related method
     */
    public function testGetId()
    {
        $this->assertNull($this->value->getId());
    }

    /**
     * Test related method
     */
    public function testGetAttribute()
    {
        $this->assertEquals($this->value->getAttribute(), $this->attribute);
    }

    /**
     * Test related method
     */
    public function testGetLocaleCode()
    {
        $code = 'fr_FR';
        $this->value->setLocaleCode($code);
        $this->assertEquals($this->value->getLocaleCode(), $code);
    }

    /**
     * Test related method
     */
    public function testGetData()
    {
        $data = 'my value';
        $this->value->setData($data);
        $this->assertEquals($this->value->getData(), $data);
    }

    /**
     * Test related method
     */
    public function testGetUnit()
    {
        $unit = 'mm';
        $this->value->setUnit($unit);
        $this->assertEquals($this->value->getUnit(), $unit);
    }

    /**
     * Test related method
     */
    public function testGetCurrency()
    {
        $currency = 'USD';
        $this->value->setCurrency($currency);
        $this->assertEquals($this->value->getCurrency(), $currency);
    }

    /**
     * Test related method
     */
    public function testGetOption()
    {
        $option = new AttributeOption();
        $this->value->setOption($option);
        $this->assertEquals($this->value->getOption(), $option);
    }

}