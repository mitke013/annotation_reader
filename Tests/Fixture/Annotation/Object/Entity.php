<?php

namespace wjb\AnnotationReaderBundle\Tests\Fixture\Annotation\Object;

/**
 * @author Zeljko Mitic <mitke013@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Entity
{

    public $persist = 'ALL';

}