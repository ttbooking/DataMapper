<?php

namespace DataMapper\Elements\ObjectInfo;

use DataMapper\Interfaces\InputCaster;
use DataMapper\Interfaces\OutputCaster;

class SerializationInfo
{

	/** @var list<InputCaster> */
	public array $inputCasters = [];
	/** @var list<OutputCaster> */
	public array $outputCasters = [];
	public bool $ignoresSerialization = false;
	public ?string $mapFromName = null;
	public ?string $serializeToName = null;
}