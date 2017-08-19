<?php

namespace Bidzm\ActivityLog\Diff;

use Bidzm\ActivityLog\Exceptions\InvalidGranualityException;
use cogpowered\FineDiff\Diff as Differ;

class Diff
{
    protected $model;

    protected $before = [];

    protected $after = [];

    protected $diff;

    protected function __construct($activity)
    {
        $this->model = new $activity->loggable;

        $this->before = $this->data($activity->before);

        $this->after = $this->data($activity->after);

        $this->differ = new Differ($this->granularity());
    }

    public static function make($activity)
    {
        $instance = new static($activity);

        return $instance->diff();
    }

    protected function data($data)
    {
        $class = get_class($this->model);

        $model = new $class;

        if (!$model->diffRaw()) {
            $model->unguard();

            $data = $model->fill($data)->attributesToArray();

            $model->reguard();
        }

        return $data;
    }

    protected function keys()
    {
        return array_keys(array_merge($this->before, $this->after));
    }

    protected function diff()
    {
        $result = [];

        foreach ($this->keys() as $key) {
            $result[] = [
                'key' => $key,
                'value' => array_get($this->before, $key),
                'before' => array_get($this->before, $key),
                'after' => array_get($this->after, $key),
                'html' => $this->diff->render(
                        array_get($this->before, $key, ''),
                        array_get($this->after, $key, '')),
            ];
        }

        return $result;
    }

    protected function granularity()
    {
        $granularity = $this->model->diffGranularity();

        switch ($granularity) {
            case 'character':
                return new \cogpowered\FineDiff\Granularity\Character;
            case 'word':
                return new \cogpowered\FineDiff\Granularity\Word;
            case 'sentence':
                return new \cogpowered\FineDiff\Granularity\Sentence;
            case 'paragraph':
                return new \cogpowered\FineDiff\Granularity\Paragraph;
        }

        throw new InvalidGranualityException("The '$granularity' granularity is not valid.");
    }
}
