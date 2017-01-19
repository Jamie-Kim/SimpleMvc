<?php
/**
 * AppModel
 */
class AppModel extends SimpleMvc\Model
{
    public $rg_manager_table = 'rg_manager';
    public $rg_user_table = 'rg_userInfo';
    public $rg_signature_table = 'rg_file_signature';
    public $rg_sentInfo_table = 'rg_sentInfo';
    public $rg_history_table = 'rg_import_history';
    public $rg_geolocation_table = 'rg_geolocation';    
    public $rg_security_log_table = 'rg_security_log';    
}