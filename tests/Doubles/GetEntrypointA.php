<?php

namespace Bauhaus\Doubles;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\MatchedRequest;

#[Endpoint('GET /endpoint-a')]
class GetEntrypointA implements HttpEntrypoint
{
    public function mapRequest(MatchedRequest $request): MessageFromGetEntrypointA
    {
        return new MessageFromGetEntrypointA();
    }
}
