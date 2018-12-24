<?php

namespace MindGeek\Parser\Reader;
use MindGeek\Parser\Exception;

/**
 * JSON Parser reader.
 */
class Json implements ReaderInterface
{
    /**
     * Directory of the JSON file
     *
     * @var string
     */
    protected $directory;

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     * @throws Exception\RuntimeException
     */
    public function fromFile($filename)
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            throw new Exception\RuntimeException(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }

        $this->directory = dirname($filename);

        $parser = $this->decode(file_get_contents($filename));

        return $this->process($parser);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array|bool
     * @throws Exception\RuntimeException
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return [];
        }

        $this->directory = null;

        $Parser = $this->decode($string);

        return $this->process($Parser);
    }

    /**
     * Process the array for @include
     *
     * @param  array $data
     * @return array
     * @throws Exception\RuntimeException
     */
    protected function process(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            }
            if (trim($key) === '@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a JSON string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            }
        }
        return $data;
    }

    /**
     * Decode JSON Parseruration.
     *
     * Determines if ext/json is present, and, if so, uses that to decode the
     * Parseruration. Otherwise, it uses MindGeek-json, and, if that is missing,
     * raises an exception indicating inability to decode.
     *
     * @param string $data
     * @return array
     * @throws Exception\RuntimeException for any decoding errors.
     */
    private function decode($data)
    {
        $parser = json_decode($data, true);

        if (null !== $parser && ! is_array($parser)) {
            throw new Exception\RuntimeException(
                'Invalid JSON Parseruration; did not return an array or object'
            );
        }

        if (null !== $parser) {
            return $parser;
        }

        if (JSON_ERROR_NONE === json_last_error()) {
            return $parser;
        }

        throw new Exception\RuntimeException(json_last_error_msg());
    }
}
