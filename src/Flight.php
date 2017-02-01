<?php
/**
 * Project: BlueairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\BlueairScraper;

/**
 * Class Flight
 * Generic container used to convert date objects to formatted strings
 *
 * @package projectivemotion\BlueairScraper
 */
class Flight extends \ArrayObject implements \JsonSerializable
{
    public function __construct($input = [])
    {
        parent::__construct($input, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {

        $clone  =   $this->getArrayCopy();

        if(count($this) == 0)
            return $clone;

        $clone['departure'] =   $this['departure']->format('Y-m-d H:i:00');
        $clone['arrival'] =   $this['arrival']->format('Y-m-d H:i:00');
        return $clone;
    }
}