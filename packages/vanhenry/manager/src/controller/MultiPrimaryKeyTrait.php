<?php
namespace vanhenry\manager\controller;
use Illuminate\Database\Eloquent\Builder;
trait MultiPrimaryKeyTrait
{
    protected function getKeyForSaveQuery($key = null)
    {
    	if(!isset($key)){
    		$key = $this->getKeyName();
    	}
        if (isset($this->original[$key])) {
            return $this->original[$key];
        }
        return $this->getAttribute($key);
    }
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            if (isset($this->$key))
                $query->where($key, '=', $this->$key);
            else
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
        }
        return $query;
    }
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();
        foreach ($me->getKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }
        return $query->first($columns);
    }
    public function getIncrementing()
    {
        return false;
    }
}
