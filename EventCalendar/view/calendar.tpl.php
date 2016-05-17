
<div class='row-fluid'>

<?= $content ?>

            <div class="col-md-4">

                        <div class="clearfix"></div>
                <?php if(isset($events)) : ?>
                        <ul class="list-group">
                                <?php foreach ($events as $event) : ?>
                                    <li class="list-group-item">
                                        <div class="card">
                                            <div class="card-block">
                                                <h4 class="card-title"><?=$event->title?></h4>
                                                <p class="card-text"><?=$event->content?></p>
                                                <p class="card-text"><?=$event->id?></p>
                                            <a href='<?=$this->di->get('url')->create('event/id/' . $event->id) ?>' class="btn btn-primary btn-xs">Show event</a>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                            </li>
                        </ul>
                <?php endif; ?>
                    </div>
            </div>
    </div>




<!-- Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add event</h4>
                </div>
                <div class="modal-body">
                    <div class="container center-block"><?= $form ?> </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>




