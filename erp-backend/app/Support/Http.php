<?php

namespace App\Support;

class Http
{
    const OK                  = 200;
    const Created             = 201;
    const NoContent           = 204;
    const BadRequest          = 400;
    const Unauthorized        = 401;
    const PaymentRequired     = 402;
    const Forbidden           = 403;
    const NotFound            = 404;
    const MethodNotAllowed    = 405;
    const UnprocessableEntity = 422;
    const TooManyRequests     = 429;
    const InternalServerError = 500;
    const UnauthorizedPayment = 402;
}
