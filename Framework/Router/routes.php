<?php

    $router->get('/news', 'HomeController','news'); // DONE --

    $router->get('/news/{id}', 'HomeController','getNewsPost'); // DONE --

    $router->post('/login', 'AuthorizationController','login'); // DONE --

    $router->post('/register', 'AuthorizationController','register'); // DONE --

    $router->patch('/news/approve/{id}', 'ActionsController', 'approveNews'); // DONE --

    $router->patch('/changePassword', 'AuthorizationController','changePassword'); // DONE --

    $router->post('/news/add', 'ActionsController','addNews'); // DONE --

    $router->put('/news/edit/{id}', 'ActionsController','editNews'); // DONE --

    $router->delete('/news/delete/{id}', 'ActionsController','deleteNews'); // DONE

