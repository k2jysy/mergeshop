<?php /* This file icludes css for import function */ ?>
<style type="text/css">
    .wooexim_wrapper form { padding: 20px 0; }

    .wooexim_wrapper #advanced_settings { display: none; }

    .wooexim_wrapper .import_error_messages {
        margin: 6px 0;
        padding: 0;
    }

    .wooexim_wrapper .import_error_messages li {
        margin: 2px 0;
        padding: 4px;
        background-color: #f9dede;
        border: 1px solid #ff8e8e;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }

    .wooexim_wrapper #import_status {
        padding: 8px 8px 8px 82px;
        min-height: 66px;
        position: relative;
        margin: 6px 0;
        background-color: #fff5d1;
        border: 1px solid #ffc658;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .wooexim_wrapper #import_status.complete {
        background-color: #ecfdbe;
        border: 1px solid #a1dd00;
    }

    .wooexim_wrapper #import_status img {
        position: absolute;
        top: 8px;
        left: 8px;
    }

    .wooexim_wrapper #import_status strong {
        font-size: 18px;
        line-height: 1.2em;
        padding: 6px 0;
        display: block;
    }

    .wooexim_wrapper #import_status #import_in_progress { display: block; }
    .wooexim_wrapper #import_status.complete #import_in_progress { display: none; }

    .wooexim_wrapper #import_status #import_complete { display: none; }
    .wooexim_wrapper #import_status.complete #import_complete { display: block; }

    .wooexim_wrapper #import_status td,
    .wooexim_wrapper #import_status th {
        text-align: left;
        font-size: 13px;
        line-height: 1em;
        padding: 4px 10px 4px 0;
    }

    .wooexim_wrapper table th { vertical-align: top; }

    .wooexim_wrapper table.super_wide th,
    .wooexim_wrapper table.super_wide td {
        width: 120px;
        min-width: 120px;
    }

    .wooexim_wrapper table.super_wide th.narrow,
    .wooexim_wrapper table.super_wide td.narrow
    .wooexim_wrapper table th.narrow,
    .wooexim_wrapper table td.narrow {
        width: 65px;
    }
    .wooexim_wrapper table input { margin: 1px 0; }

    .wooexim_wrapper table tr.header_row th {
        background-color: #DCEEF8;
        background-image: none;
        vertical-align: middle;
    }

    .wooexim_wrapper .map_to_settings {
        margin: 2px 0;
        padding: 2px;
        overflow: hidden;
    }
    .wooexim_wrapper .map_to_settings select { width: 98%; }

    .wooexim_wrapper .field_settings {
        display: none;
        margin: 2px 0;
        padding: 4px;
        background-color: #e0e0e0;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }
    .wooexim_wrapper .field_settings h4 {
        margin: 0;
        font-size: 0.9em;
        line-height: 1.2em;
    }
    .wooexim_wrapper .field_settings p {
        margin: 4px 0;
        overflow: hidden;
        font-size: .9em;
        line-height: 1.3em;
    }
    .wooexim_wrapper .field_settings select { width: 98%; }
    .wooexim_wrapper .field_settings input[type="text"] { width: 98%; }

    .wooexim_wrapper #inserted_rows tr.error td { background-color: #FFF6D3; }
    .wooexim_wrapper #inserted_rows tr.fail td { background-color: #FFA8A8; }

    .wooexim_wrapper #inserted_rows .icon {
        display: block;
        width: 16px;
        height: 16px;
        background-position: 0 0;
        background-repeat: no-repeat;
    }
    .wooexim_wrapper #inserted_rows tr.success .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>../img/accept.png'); }
    .wooexim_wrapper #inserted_rows tr.error .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>../img/error.png'); }
    .wooexim_wrapper #inserted_rows tr.fail .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>../img/exclamation.png'); }

    .wooexim_wrapper #debug {
        display: none;
        font-family: monospace;
        font-size: 14px;
        line-height: 16px;
        color: #333;
        background-color: #f5f5f5;
        border: 1px solid #efefef;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 0 10px;
    }

    .wooexim_wrapper #credits {
        margin: 20px 0 6px;
    }

    .wooexim_wrapper #credits p {
        margin: 2px 0;
    }

    .wooexim_wrapper #donate_form {
        float: left;
        margin: 0 6px;
        padding: 0;
    }

    .wooexim_wrapper #donate_form form {
        margin: 0;
        padding: 0;
    }
</style>