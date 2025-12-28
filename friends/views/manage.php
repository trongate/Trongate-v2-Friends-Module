<h1>Manage Friends</h1>
<?php
flashdata();
echo '<p>'.anchor('friends/create', 'Create New Friend Record', array('class' => 'button alt')).'</p>';
if (empty($rows)) {
    echo '<p>There are currently no records to display.</p>';
    return;
}
echo Modules::run('pagination/display', $pagination_data);
?>

<table class="records-table">
    <thead>
        <tr>
            <th colspan="5">
                <div>
                    <div>&nbsp;</div>
                    <div>Records Per Page: <?php
                    $dropdown_attr['onchange'] = 'setPerPage()';
                    echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                    ?></div>

                </div>                    
            </th>
        </tr>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email Address</th>
            <th>Birthday</th>
            <th style="width: 20px;">Action</th>            
        </tr>
    </thead>
    <tbody>
        <?php 
        $attr['class'] = 'button alt';
        foreach($rows as $row) { ?>
        <tr>
            <td><?= out($row->first_name) ?></td>
            <td><?= out($row->last_name) ?></td>
            <td><?= out($row->email_address) ?></td>
            <td><?= out($row->birthday_formatted) ?></td>
            <td><?= anchor('friends/show/'.$row->id, 'View', $attr) ?></td>        
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>

<?php 
if(count($rows)>9) {
    unset($pagination_data['include_showing_statement']);
    echo Modules::run('pagination/display', $pagination_data);
}
?>

<script>
function setPerPage() {
    const selectedIndex = document.querySelector('select[name="per_page"]').value;
    window.location.href = '<?= BASE_URL ?>friends/set_per_page/' + selectedIndex;
}
</script>
