<div>
    <div class="btn-group">
        <a href='<?=$this->url->create('event/delete/' . $event->id)?>' class="btn btn-default">Delete event</a>
        <a href='<?=$this->url->create('calendar')?>' class="btn btn-info">Back</a>
    </div>
    <div class="jumbotron">
        <?= $form ?>
    </div>
</div>
