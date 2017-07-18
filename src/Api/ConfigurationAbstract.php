<?php
namespace Apikr\Api;

use InvalidArgumentException;

abstract class ConfigurationAbstract
{
    /** @var array */
    protected $defaults = [];
    
    /** @var array */
    protected $required = [];

    /** @var array */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function assertRequired()
    {
        $required = [];
        foreach ($this->required as $name) {
            if (!array_key_exists($name, $this->attributes) && !array_key_exists($name, $this->defaults)) {
                $required[] = $name;
            }
        }
        if (count($required)) {
            $keys = "'" . implode("', '", $required) . "'";
            throw new InvalidArgumentException("설정에서 {$keys}는 필수값입니다.");
        }
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $methodName = 'get' . lcfirst($name) . 'Attribute';
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        if (array_key_exists($name, $this->defaults)) {
            return $this->defaults[$name];
        }
        return null;
    }
}
