<?php

namespace Bauhaus\Doubles;

use Bauhaus\HttpPort\Entrypoint\Endpoint;
use Bauhaus\HttpPort\Entrypoint\HttpEntrypoint;
use Bauhaus\HttpPort\MatchedRequest;

#[Endpoint('POST /endpoint-a/{stringParam}/{integerParam}')]
class PostEntrypointA implements HttpEntrypoint
{
    public function mapRequest(MatchedRequest $request): MessageFromPostEntrypoint
    {
        return new MessageFromPostEntrypoint(
            $request->paramFromPathAsInteger('integerParam'),
            $request->paramFromPathAsString('stringParam'),
        );
    }
}
