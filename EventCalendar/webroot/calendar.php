<?php

$di->set('EventController', function() use ($di) {
    $controller = new \Anax\Events\EventController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/events_sqlite.php');
    $db->connect();
    return $db;
});

$app->router->add('calendar-create-database', function() use ($app) {

    $app->db->dropTableIfExists('event')->execute();

    $app->db->createTable(
        'event',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'title' => ['varchar(80)'],
            'name' => ['varchar(80)'],
            'content' => ['text'],
            'showdate' => ['datetime'],
            'startdate' => ['datetime'],
            'stopdate' => ['datetime'],
            'created' => ['datetime'],
            'updated' => ['datetime'],
            'status' => ['datetime'],
        ]
    )->execute();

    $url = $app->url->create('calendar');
    $app->response->redirect($url);
});

$app->router->add('add-event', function() use ($app) {
    $app->theme->setTitle("Add event");

    $form = new \Mos\HTMLForm\CForm();

    $form = $form->create([], [
        'title' => [
            'type'        => 'text',
            'label'       => 'Title',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'name' => [
            'type'        => 'text',
            'label'       => 'Name',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'content' => [
            'type'        => 'textarea',
            'label'       => 'Content',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'submit' => [
            'type'        => 'submit',
            'label'       => 'Add',
            'callback'  => function($form) {
                $form->saveInSession = true;
                return true;
            }
        ],
    ]);

    // Check the status of the form
    $status = $form->check();

    if ($status === true) {

        $app->dispatcher->forward([
            'controller' => 'calendar',
            'action'     => 'add',
        ]);

    } else if ($status === false) {

        var_dump('Check method returned false');
        die;
    }


    $app->views->add('me/page', [
        'title' => 'Add event',
        'content' => $form->getHTML()
    ]);
});

$app->router->add('calendar', function() use ($app) {

    $app->theme->setTitle("Welcome to Anax Calendar");
    $app->views->add('calendar/index');

    $date = $app->request->getGet('date');

    $app->dispatcher->forward([
        'controller' => 'event',
        'action'     => 'view',
        'params'     => ['date' => $date]
    ]);

});
