<?php

namespace Anax\Events;

/**
 * A controller for Events.
 *
 */
class EventController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;


    /**
     * View the calendar.
     *
     * @return void
     */
    public function initialize()
    {
        $this->events = new \Anax\Events\Event();
        $this->events->setDI($this->di);
    }

    /**
     * Initialize the controller.
     *
     * @return void
     */

    public function viewAction(){

        $this->initialize();
        $calendar = new \Anax\Calendar\CCalendar();
        $calendar->getValues();
        $calendar->generateCalenderData();
        $month = $calendar->getMonthNumber();
        $year = $calendar->getYear();

        $date = $this->dispatcher->getParam('date');
        $_GET = array();
        $currentDate = $year ."-". $month ."-". sprintf('%02s',$date);


        $events = $this->events->findEventsOfDay($currentDate);
        $eventCount = $this->events->getEventCountPerDayOfMonth($month);

        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'title' => [
                'type'        => 'text',
                'label'       => 'Title',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'time' => [
                'type'        => 'date',
                'label'       => 'Date for event',
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
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            $this->dispatcher->forward([
                'controller' => 'event',
                'action'     => 'add',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $this->views->add('calendar/calendar', [
            'content' => $calendar->printResponsiveCalendar($eventCount),
            'form'    => $form->getHTML(),
            'events'     => $events,
        ]);

    }

    public function addAction(){

        $now = gmdate('Y-m-d H:i:s');

        $calendar = new \Anax\Calendar\CCalendar();
        $calendar->getValues();
        $calendar->generateCalenderData();
        $month = $calendar->getMonth();
        $year = $calendar->getYear();
        $day = $this->request->getPost('time');

        $this->events->save([
            'id'        => $this->request->getPost('id'),
            'title' => $this->request->getPost('title'),
            'showdate' => $this->request->getPost('time'),
            'content' => $this->request->getPost('content'),
            'created' => $now,
        ]);
        $redirect = "calendar?month=$month&year=$year&date=$day";
        $url = $this->url->create($redirect);
        $this->response->redirect($url);

    }

    public function findEventsAction()
    {

        $this->initialize();

        $reslut = $this->events->findEventsOfDay();

        $this->views->add('calendar/calendar', [
            'events' => $reslut,
        ]);

        $url = $this->url->create('calendar');
        $this->response->redirect($url);


    }
    public function deleteAction($id = null)
    {
        $this->initialize();

        if (!isset($id)) {
            die("Missing id");
        }

        $this->events->delete($id);

        $url = $this->url->create('calendar');
        $this->response->redirect($url);
    }


    public function idAction($id = null)
    {
        $this->initialize();

        $event = $this->events->find($id);

        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $id
            ],
            'title' => [
                'class'       => 'form-control',
                'type'        => 'text',
                'label'       => 'Title',
                'value'       => $event->title,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'time' => [
                'class'       => 'form-control',
                'type'        => 'date',
                'label'       => 'Date for event',
                'value'       => $event->showdate,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'class'       => 'form-control',
                'type'        => 'textarea',
                'label'       => 'Content',
                'value'       => $event->content,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'class'       => 'btn btn-default',
                'type'        => 'submit',
                'value'       => 'Update',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            $this->dispatcher->forward([
                'controller' => 'event',
                'action'     => 'add',
                'params'     => ['id' => $id],
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $this->theme->setTitle("View event with id");
        $this->views->add('calendar/view', [
            'event' => $event,
            'form'     => $form->getHTML()
        ]);
    }
}