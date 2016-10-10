<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Filter;

use Symfony\Component\PropertyAccess\PropertyAccessor;

use Oro\Bundle\ApiBundle\Filter\ComparisonFilter;
use Oro\Bundle\ApiBundle\Filter\SimpleFilterFactory;

class SimpleFilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var SimpleFilterFactory */
    protected $filterFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filterFactory = new SimpleFilterFactory(new PropertyAccessor());
    }

    public function testForUnknownFilter()
    {
        $this->assertNull($this->filterFactory->createFilter('unknown'));
    }

    public function testForFilterWithoutAdditionalParameters()
    {
        $filterType = 'string';

        $this->filterFactory->addFilter(
            $filterType,
            'Oro\Bundle\ApiBundle\Filter\ComparisonFilter'
        );

        $expectedFilter = new ComparisonFilter($filterType);

        $this->assertEquals(
            $expectedFilter,
            $this->filterFactory->createFilter($filterType)
        );
    }

    public function testForFilterWithAdditionalParameters()
    {
        $filterType = 'string';
        $supportedOperators = ['=', '!='];

        $this->filterFactory->addFilter(
            $filterType,
            'Oro\Bundle\ApiBundle\Filter\ComparisonFilter',
            ['supported_operators' => $supportedOperators]
        );

        $expectedFilter = new ComparisonFilter($filterType);
        $expectedFilter->setSupportedOperators($supportedOperators);

        $this->assertEquals(
            $expectedFilter,
            $this->filterFactory->createFilter($filterType)
        );
    }

    public function testOverrideParameters()
    {
        $filterType = 'string';

        $this->filterFactory->addFilter(
            $filterType,
            'Oro\Bundle\ApiBundle\Filter\ComparisonFilter',
            ['supported_operators' => ['=', '!=']]
        );

        $expectedFilter = new ComparisonFilter($filterType);
        $expectedFilter->setSupportedOperators(['=']);

        $this->assertEquals(
            $expectedFilter,
            $this->filterFactory->createFilter($filterType, ['supported_operators' => ['=']])
        );
    }

    public function testWhenFilterTypeDoesNotEqualToDataType()
    {
        $filterType = 'someFilter';
        $dataType = 'integer';

        $this->filterFactory->addFilter(
            $filterType,
            'Oro\Bundle\ApiBundle\Filter\ComparisonFilter'
        );

        $expectedFilter = new ComparisonFilter($dataType);

        $this->assertEquals(
            $expectedFilter,
            $this->filterFactory->createFilter($filterType, ['data_type' => $dataType])
        );
    }
}
