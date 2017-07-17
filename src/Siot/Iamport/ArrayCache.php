<?php
namespace Apikr\Siot\Iamport;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use Traversable;

class ArrayCache implements CacheInterface
{
    /** @var array */
    protected $caches  = [];

    /** @var array */
    protected $expires = [];
    
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        foreach ($this->getMultiple([$key], $default) as $v) {
            return $v;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        if ($keys instanceof Traversable) {
            $keys = iterator_to_array($keys, false);
        } elseif (!is_array($keys)) {
            throw new InvalidArgumentException(sprintf('Cache keys must be array or Traversable, "%s" given', is_object($keys) ? get_class($keys) : gettype($keys)));
        }
        $now = time();
        foreach ($keys as $key) {
            if (isset($this->expires[$key]) && $this->expires[$key] >= $now) {
                yield $key => $this->caches[$key];
            } else {
                yield $key => $default;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->setMultiple([$key => $value], $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values) && !$values instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf('Cache values must be array or Traversable, "%s" given', is_object($values) ? get_class($values) : gettype($values)));
        }
        if (false === $ttl = $this->normalizeTtl($ttl)) {
            return $this->deleteMultiple(array_keys($values));
        }

        $expiry = 0 < $ttl ? time() + $ttl : PHP_INT_MAX;

        foreach ($values as $key => $value) {
            $this->caches[$key] = $value;
            $this->expires[$key] = $expiry;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys) && !$keys instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf('Cache keys must be array or Traversable, "%s" given', is_object($keys) ? get_class($keys) : gettype($keys)));
        }
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->caches[$key], $this->expires[$key]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->caches = $this->expires = [];
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->expires[$key]) && $this->expires[$key] >= time();
    }

    /**
     * @param mixed $ttl
     * @return bool|int
     */
    private function normalizeTtl($ttl)
    {
        if (null === $ttl) {
            return 0;
        }
        if ($ttl instanceof \DateInterval) {
            $ttl = (int) \DateTime::createFromFormat('U', 0)->add($ttl)->format('U');
        }
        if (is_int($ttl)) {
            return 0 < $ttl ? $ttl : false;
        }

        throw new InvalidArgumentException(sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given', is_object($ttl) ? get_class($ttl) : gettype($ttl)));
    }
}
