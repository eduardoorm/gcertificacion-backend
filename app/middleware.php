<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Settings\SettingsInterface;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\JwtAuthentication\RequestMethodRule;
use Tuupola\Middleware\JwtAuthentication\RequestPathRule;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(new JwtAuthentication([
        "secure" => true,
        //"relaxed" => ["localhost"],
        "secret" => "supersecretkeyyoushouldnotcommittogithub",
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["statusText"] = $arguments["message"];
    
            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
    
            return $response->withHeader("Content-Type", "application/json");
        },
        "rules" => [
            new RequestPathRule([
                "path" => ['/'],
                "ignore" => [
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/auth/login",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/images",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/videos",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/excel",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/files",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/certificados",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/test",
                    $app->getContainer()->get(SettingsInterface::class)->get("contextApi")."/informe",
                ],
            ]),
            new RequestMethodRule([
                "ignore" => ["OPTIONS"]
            ])
        ],
    ]));
};
