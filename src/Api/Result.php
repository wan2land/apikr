<?php
namespace Apikr\Api;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JmesPath\Env;

class Result implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $expression
     * @return mixed
     */
    public function search($expression)
    {
        return Env::search($expression, $this->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
