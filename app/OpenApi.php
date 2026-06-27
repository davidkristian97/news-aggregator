<?php

namespace App;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="API for aggregating news articles from multiple sources"
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Articles", description="Article endpoints")
 * @OA\Tag(name="Me", description="Authenticated user endpoints")
 * @OA\Tag(name="Filters", description="Filter option endpoints")
 */
class OpenApi {}
