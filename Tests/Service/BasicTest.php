<?php

namespace wjb\AnnotationReaderBundle\Tests\Service;

use ReflectionProperty;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use wjb\AnnotationReaderBundle\Service\AnnotationResolver;
use wjb\AnnotationReaderBundle\Tests\Fixture\Annotation\Property as PropertyAnnotation;
use wjb\AnnotationReaderBundle\Tests\Fixture\Annotation\Object as ObjectAnnotation;
use wjb\AnnotationReaderBundle\Tests\Fixture\Model\SimpleObject;

/**
 * @author Zeljko Mitic <zeljko.mitic@soprex.com>
 */
class BasicTest extends KernelTestCase
{

    /**
     * @var AnnotationResolver
     */
    private $annotationResolver;
    private $model;

    public function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->annotationResolver = self::$kernel->getContainer()->get('wjb.annotation_resolver');
        $this->model = new SimpleObject();
    }

    public function testSetup()
    {
        $this->assertInstanceOf(AnnotationResolver::class, $this->annotationResolver);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testPropertyAnnotationDefaultValueExists()
    {
        $this->annotationResolver->onPropertyAnnotation(function(PropertyAnnotation\DefaultValue $annotation, ReflectionProperty $property) {
            $this->assertTrue(in_array($annotation->value, ['abc', 'def']));
            $this->assertTrue(in_array($property->getName(), ['firstName', 'compound']));
            throw new RuntimeException;
        }, $this->model);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testPropertyAnnotationTypeExists()
    {
        $this->annotationResolver->onPropertyAnnotation(function(PropertyAnnotation\Type $annotation, ReflectionProperty $property) {
            $this->assertEquals('group', $annotation->name);
            $this->assertEquals('lastName', $property->getName());
            throw new RuntimeException;
        }, $this->model);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testClassAnnotationExists()
    {
        $this->annotationResolver->onClassAnnotation(function(ObjectAnnotation\Entity $annotation) {
            $this->assertEquals('all', $annotation->persist);
            throw new RuntimeException;
        }, $this->model);
    }

    public function testClassAnnotationDoesNotExist()
    {
        $this->annotationResolver->onClassAnnotation(function(ObjectAnnotation\UnusedClassAnnotation $annotation) {
            $this->fail();
        }, $this->model);
    }

    public function testCascadeAnnotations()
    {
        $this->annotationResolver->onPropertyAnnotation(function(PropertyAnnotation\DefaultValue $annotation, ReflectionProperty $property) {
            $this->annotationResolver->onPropertyAnnotation(function(PropertyAnnotation\Type $type, ReflectionProperty $property) {
                $this->assertEquals('compound', $property->getName());
            }, $property);
        }, $this->model);
    }

    public function testMultipleAnnotations()
    {
        $this->annotationResolver->onPropertyAnnotations(function(PropertyAnnotation\DefaultValue $annotation, PropertyAnnotation\Type $type, ReflectionProperty $property) {

        }, $this->model);
    }


}