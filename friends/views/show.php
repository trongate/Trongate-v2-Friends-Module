<h1><?= $headline ?></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Friend Details
    </div>
    <div class="card-body">
        <div class="text-right mb-3">
            <?= anchor($back_url, 'Back', array('class' => 'button alt')) ?>
            <?= anchor(BASE_URL.'friends/create/'.$update_id, 'Edit', array('class' => 'button')) ?>
            <?= anchor('friends/delete_conf/'.$update_id, 'Delete',  array('class' => 'button danger')) ?>
        </div>
        
        <div class="detail-grid">
            <div class="detail-row">
                <div class="detail-label">First Name</div>
                <div class="detail-value"><?= out($first_name) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Last Name</div>
                <div class="detail-value"><?= out($last_name) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email Address</div>
                <div class="detail-value"><?= out($email_address) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Birthday</div>
                <div class="detail-value"><?= out($birthday_formatted) ?></div>
            </div>
        </div>
    </div>
</div>
