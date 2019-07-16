<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

return function (App $app) {
    $container = $app->getContainer();

    $app->post('/login', function (Request $request, Response $response, array $args) {
      $input = $request->getParsedBody();

      $userStore = $this->db;
      $user = $userStore->where('user','=',$input['user'])->fetch();

      // verify user
      if(count($user)==0 || count($user)>1) {
          return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']);
      }
      // verify password.
      if (!password_verify($input['password'],$user[0]['password'])) {
          return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.');
      }
      $settings = $this->get('settings'); // get settings array.
      $token = JWT::encode(['id' => $user[0]['id'], 'user' => $user[0]['user']], $settings['jwt']['secret'], "HS256");
      return $this->response->withJson(['token' => $token]);
    });

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
};
