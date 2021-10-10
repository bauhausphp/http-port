<?php

namespace Bauhaus\Doubles;

class MessageFromPostEntrypoint
{
    public function __construct(
        private int $integer,
        private string $string,
    ) {
    }
}
