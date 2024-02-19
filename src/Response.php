<?php

namespace O21\ApiEntity\Response;

use JsonException;
use O21\ApiEntity\Exception\InvalidJsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * @param  string|ResponseInterface  $response
 * @return array
 * @throws \O21\ApiEntity\Exception\InvalidJsonException
 */
function json_props(string|ResponseInterface $response): array
{
    if ($response instanceof ResponseInterface) {
        $response = $response->getBody()->getContents();
    }

    try {
        $props = json_decode(
            $response,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException $e) {
        throw new InvalidJsonException($response);
    }

    return $props;
}
