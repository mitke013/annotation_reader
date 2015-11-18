<?php

namespace wjb\AnnotationReaderBundle\Tests\Fixture\Model;

use wjb\AnnotationReaderBundle\Tests\Fixture\Annotation\Property as PropertyAnnotation;
use wjb\AnnotationReaderBundle\Tests\Fixture\Annotation\Object as ObjectAnnotation;

/**
 * @author Zeljko Mitic <zeljko.mitic@soprex.com>
 *
 * @ObjectAnnotation\Entity(persist="all")
 *
 */
class SimpleObject
{
    /**
     * @PropertyAnnotation\DefaultValue(value="abc")
     */
    private $firstName;

    /**
     * @PropertyAnnotation\Type(name="group")
     */
    private $lastName;

    /**
     * @PropertyAnnotation\DefaultValue(value="def")
     * @PropertyAnnotation\Type(name="group")
     */
    private $compound;

    public function getFirstName()
    {
        return $this->firstName;
    }


    public function getLastName()
    {
        return $this->lastName;
    }

    public function getCompound()
    {
        return $this->compound;
    }

}