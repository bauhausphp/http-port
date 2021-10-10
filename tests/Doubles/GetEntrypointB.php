<?php

namespace Bauhaus\Doubles;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\MatchedRequest;

#[Endpoint('GET /endpoint-b')]
class GetEntrypointB implements HttpEntrypoint
{
    public function mapRequest(MatchedRequest $request): MessageFromGetEntrypointB
    {
        return new MessageFromGetEntrypointB();
    }
}
