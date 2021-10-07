<?php

namespace Paywant;

class JsonSerializableModel implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
