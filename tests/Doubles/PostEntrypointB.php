<?php

namespace Bauhaus\Doubles;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\MatchedRequest;

#[Endpoint('POST /endpoint-b')]
class PostEntrypointB implements HttpEntrypoint
{
    public function mapRequest(MatchedRequest $request): MessageFromPostEntrypoint
    {
        return new MessageFromPostEntrypoint(
            $request->paramFromBodyAsInteger('f2'),
            $request->paramFromBodyAsString('f1'),
        );
    }
}