<?php

namespace Bauhaus\Doubles;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;

#[Endpoint('GET /colliding')]
class CollidingEntrypointA implements HttpEntrypoint
{
    public function mapRequest(): object
    {
    }
}
