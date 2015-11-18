<?php

namespace wjb\AnnotationReaderBundle\Service;

use Closure;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use ReflectionParameter;
use wjb\AnnotationReaderBundle\Exception\MissingParameterException;
use wjb\AnnotationReaderBundle\Exception\MissingTypeHintException;

/**
 * @author Zeljko Mitic <zeljko.mitic@soprex.com>
 */
class AnnotationResolver
{

    /**
     * @var CachedReader
     */
    private $cachedReader;

    public function __construct(AnnotationReader $cachedReader)
    {
        $this->cachedReader = $cachedReader;
    }

    /**
     * Run closure only if a property has type-hinted class in annotations
     */
    public function onPropertyAnnotation(Closure $closure, $object)
    {
        $annotationClassName = $this->getClassNameOfTypeHintedClosure($closure);

        if ($object instanceof \ReflectionProperty) {
            $annotation = $this->cachedReader->getPropertyAnnotation($object, $annotationClassName);
            if ($annotation) {
                $closure($annotation, $object);
            }

        } else {

            $reflection = new \ReflectionObject($object);
            $reflectionProperties = $reflection->getProperties();

            foreach ($reflectionProperties as $property) {
                $annotation = $this->cachedReader->getPropertyAnnotation($property, $annotationClassName);
                if ($annotation) {
                    $closure($annotation, $property);
                }
            }
        }
    }

    public function onPropertyAnnotations(Closure $closure, $object)
    {
        $args = func_get_args();
        dump($args);die;

    }

    public function onClassAnnotation(Closure $closure, $object)
    {
        $annotationClassName = $this->getClassNameOfTypeHintedClosure($closure);
        $reflection = new \ReflectionClass($object);

        $annotation = $this->cachedReader->getClassAnnotation($reflection, $annotationClassName);
        if ($annotation) {
            $closure($annotation, $reflection);
        }
    }


    private function getClassNameOfTypeHintedClosure(Closure $closure)
    {
        $closureReflection = new \ReflectionFunction($closure);
        $closureParams = $closureReflection->getParameters();

        if (!isset($closureParams[0])) {
            throw new MissingParameterException;
        }

        $firstParam = $closureParams[0];
        if (null == $reflectedParamClass = $firstParam->getClass()) {
            throw new MissingTypeHintException;
        }


        return $reflectedParamClass->getName();
    }

    /**
     * @param Closure $closure
     *
     * @return ReflectionParameter[]
     * @throws MissingTypeHintException
     */
    private function getParamsOfClosure(Closure $closure)
    {
        $closureReflection = new \ReflectionFunction($closure);
        $closureParams = $closureReflection->getParameters();

        foreach ($closureParams as $param) {
            if (!$param->getClass()) {
                throw new MissingTypeHintException(sprintf('Missing type-hint for variable %s', $param->getName()));
            }
        }

        return $closureParams;
    }

}