<?php

namespace TaylorNetwork\VariableReplacer;


class VariableReplacer
{
    /**
     * Options
     *
     * @var array
     */
    public $options;

    /**
     * Stage to parse
     *
     * @var string
     */
    public $stage;

    /**
     * Replace with mixed
     *
     * @var mixed
     */
    public $replaceWith;

    /**
     * Original un-parsed ext
     *
     * @var string
     */
    protected $original;

    /**
     * Parsed text
     *
     * @var string
     */
    protected $parsed;

    /**
     * TextParser constructor.
     *
     * @param string  $stage
     * @param mixed  $replaceWith
     * @param array  $options
     */
    public function __construct(string $stage = null, $replaceWith = null, array $options = [])
    {
        $this->options = config('variable_replacer', []);

        if($stage !== null) {
            $this->stage($stage);
        }

        if($replaceWith !== null) {
            $this->replaceWith($replaceWith);
        }

        if(!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Parse some text
     *
     * @param string  $text
     * @return string
     */
    public function parse(string $text)
    {
        $this->original = $text;

        $parser = $this;

        $replacer = function ($matches) use ($parser) {
            return eval('return $parser->replaceWith->'.$matches[1].';');
        };

        $this->parsed = preg_replace_callback($this->getPattern(), $replacer, $this->original);

        return $this->parsed;
    }

    /**
     * Set the stage
     *
     * @param string  $stage
     * @return $this
     */
    public function stage(string $stage)
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * Set replaceWith model
     *
     * @param mixed  $model
     * @return $this
     */
    public function replaceWith($model)
    {
        $this->replaceWith = $model;
        return $this;
    }

    /**
     * Get the stage
     *
     * @return string
     */
    public function getStage()
    {
        return strtolower($this->stage);
    }

    /**
     * Set options array
     *
     * @param array  $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Set an option
     *
     * @param string  $option
     * @param mixed  $value
     * @return $this
     */
    public function setOption(string $option, $value)
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * Get an option
     *
     * @param string  $option
     * @return mixed
     */
    public function getOption(string $option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * Build the RegEx pattern
     *
     * @return string
     */
    public function getPattern()
    {
        $pattern = '';

        if($this->getOption('use-stage')) {
            $pattern .= $this->getOption('stage-prefix') . $this->getStage() . $this->getOption('stage-suffix');
        }

        $pattern .= $this->getOption('open-char') . '(.*?)' . $this->getOption('close-char');

        return $this->getOption('pattern-quote') . $pattern . $this->getOption('pattern-quote');
    }

    /**
     * __get
     *
     * @param string  $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
}