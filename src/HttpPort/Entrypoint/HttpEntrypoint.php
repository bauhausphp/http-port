<?php

namespace Bauhaus\HttpPort\Entrypoint;

use Bauhaus\HttpPort\MatchedRequest;

interface HttpEntrypoint
{
    public function mapRequest(MatchedRequest $request): object;
}
