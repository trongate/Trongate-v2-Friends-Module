<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Friend Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        
        echo form_label('First Name');
        echo form_input('first_name', $first_name, ["placeholder" => "Enter First Name"]);
        
        echo form_label('Last Name');
        echo form_input('last_name', $last_name, ["placeholder" => "Enter Last Name"]);
        
        echo form_label('Email Address');
        echo form_input('email_address', $email_address, ["placeholder" => "Enter Email Address", "type" => "email"]);
        
        echo form_label('Birthday');
        echo form_date('birthday', $birthday);

        echo '<div class="text-center">';
        echo anchor($cancel_url, 'Cancel', ['class' => 'button alt']);
        echo form_submit('submit', 'Submit');
        echo form_close();
        echo '</div>';
        ?>
    </div>
</div>
