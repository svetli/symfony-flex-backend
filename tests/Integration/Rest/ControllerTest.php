<?php
declare(strict_types=1);
/**
 * /tests/Integration/Rest/ControllerTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Tests\Integration\Rest;

use App\DTO\RestDtoInterface;
use App\Rest\Controller;
use App\Rest\RestResourceInterface;
use App\Rest\ResponseHandler;
use App\Utils\Tests\PHPUnitUtil;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ControllerTest
 *
 * @package App\Tests\Integration\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class ControllerTest extends KernelTestCase
{
    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Resource service not set
     */
    public function testThatGetResourceThrowsAnExceptionIfNotSet(): void
    {
        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);
        $controller->getResource();
    }

    public function testThatGetResourceDoesNotThrowsAnExceptionIfSet(): void
    {
        /** @var RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $responseHandler = new ResponseHandler(new Serializer());

        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        static::assertInstanceOf(RestResourceInterface::class, $controller->getResource());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage ResponseHandler service not set
     */
    public function testThatGetResponseHandlerThrowsAnExceptionIfNotSet(): void
    {
        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);
        $controller->getResponseHandler();
    }

    public function testThatGetResponseHandlerDoesNotThrowsAnExceptionIfSet(): void
    {
        /** @var RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $responseHandler = new ResponseHandler(new Serializer());

        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        static::assertInstanceOf(ResponseHandler::class, $controller->getResponseHandler());
    }

    public function testThatGetDtoClassCallsExpectedServiceMethods(): void
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|RestDtoInterface $dtoClass */
        $dtoClass = $this->getMockBuilder(RestDtoInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $resource
            ->expects(static::once())
            ->method('getDtoClass')
            ->willReturn(\get_class($dtoClass));

        $responseHandler = new ResponseHandler(new Serializer());

        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        $controller->getDtoClass();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Given DTO class 'stdClass' is not implementing 'App\DTO\RestDtoInterface' interface.
     */
    public function testThatGetDtoClassThrowsAnExceptionIfResourceDoesNotReturnExpectedClass(): void
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $resource
            ->expects(static::once())
            ->method('getDtoClass')
            ->willReturn(\stdClass::class);

        $responseHandler = new ResponseHandler(new Serializer());

        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        $controller->getDtoClass();
    }

    public function testThatGetDtoClassWorksAsExpectedWithGivenDtoClasses(): void
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|RestDtoInterface $dtoClass */
        $dtoClass = $this->getMockBuilder(RestDtoInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $responseHandler = new ResponseHandler(new Serializer());

        $dtoClasses = [
            'foo' => \get_class($dtoClass),
        ];

        /** @var PHPUnit_Framework_MockObject_MockObject|Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        $reflection = new \ReflectionProperty(\get_class($controller), 'dtoClasses');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $dtoClasses);

        static::assertSame(\get_class($dtoClass), $controller->getDtoClass('foo'));
    }

    public function testThatGetFormTypeClassCallsExpectedServiceMethods(): void
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|FormTypeInterface $formTypeClass */
        $formTypeClass = $this->getMockBuilder(FormTypeInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $resource
            ->expects(static::once())
            ->method('getFormTypeClass')
            ->willReturn(\get_class($formTypeClass));

        $responseHandler = new ResponseHandler(new Serializer());

        /** @var Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        $controller->getFormTypeClass();
    }

    public function testThatGetFormTypeClassWorksAsExpectedWithGivenFormTypes(): void
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|FormTypeInterface $formTypeClass */
        $formTypeClass = $this->getMockBuilder(FormTypeInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|RestResourceInterface $resource */
        $resource = $this->getMockBuilder(RestResourceInterface::class)->getMock();

        $responseHandler = new ResponseHandler(new Serializer());

        $formTypes = [
            'foo' => \get_class($formTypeClass),
        ];

        /** @var PHPUnit_Framework_MockObject_MockObject|Controller $controller */
        $controller = $this->getMockForAbstractClass(Controller::class);

        PHPUnitUtil::callMethod($controller, 'init', [$resource, $responseHandler]);

        $reflection = new \ReflectionProperty(\get_class($controller), 'formTypes');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $formTypes);

        static::assertSame(\get_class($formTypeClass), $controller->getFormTypeClass('bar::foo'));
    }
}
