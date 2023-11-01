<?php

declare(strict_types=1);

namespace DotEnvIt\ApiIntegrator\Enums;

enum AuthType: string
{
    case Bearer = 'Bearer';
    case Header = 'Header';
}
