<?php
class PluginMailQueue_admin{
  private $settings = null;
  private $mysql = null;
  function __construct() {
    if(!wfUser::hasRole('webadmin')){
      exit('Role webadmin is required.');
    }
    /**
     * Time limit.
     */
    set_time_limit(60*5);
    /**
     * Memory limit
     */
    ini_set('memory_limit', '4048M');
    /**
     * Include.
     */
    wfPlugin::includeonce('theme/include');
    wfPlugin::enable('theme/include');
    wfPlugin::includeonce('wf/array');
    wfPlugin::includeonce('wf/yml');
    /**
     * Enable.
     */
    wfPlugin::enable('wf/table');
  /**
     * Layout path.
     */
    wfGlobals::setSys('layout_path', '/plugin/mail/queue_admin/layout');
    /**
     * Settings.
     */
    $this->settings = wfPlugin::getModuleSettings('mail/queue_admin', true);
    /**
     * Set mysql from session if empty.
     */
    if(!$this->settings->get('mysql')){
      $user = wfUser::getSession();
      $this->settings->set('mysql', $user->get('plugin/mail/queue_admin/mysql'));
    }
    /**
     * If no mysql is set.
     */
    if(!$this->settings->get('mysql')){
      exit('No database is provided.');
    }
    /**
     * Mysql
     */
    wfPlugin::includeonce('wf/mysql');
    $this->mysql = new PluginWfMysql();
  }
  public function page_start(){
    $page = $this->getYml('page/start');
    $page->setByTag($this->settings->get('mysql'));  
    /**
     * Insert admin layout from theme.
     */
    $page = wfDocument::insertAdminLayout($this->settings, 1, $page);
    /**
     * 
     */
    wfDocument::mergeLayout($page->get());
  }
  public function page_queue_list(){
    $page = $this->getYml('page/queue_list');
    wfDocument::renderElement($page->get());
  }
  public function page_queue_list_data(){
    $rs = $this->db_queue_list();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($rs));    
  }
  public function page_send_list(){
    $page = $this->getYml('page/send_list');
    wfDocument::renderElement($page->get());
  }
  public function page_send_list_data(){
    $rs = $this->db_send_list();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($rs));    
  }
  public function getYml($dir){
    return new PluginWfYml(__DIR__.'/'.$dir.".yml");
  }
  public function db_open(){
    $this->mysql->open($this->settings->get('mysql'));
  }
  public function getSql($key, $dir = __DIR__){
    return new PluginWfYml($dir.'/mysql/sql.yml', $key);
  }
  private function db_queue_list(){
    $this->db_open();
    $sql = $this->getSql('queue_list');
    $this->mysql->execute($sql->get());
    $rs = $this->mysql->getMany();
    /**
     * attachment
     */
    foreach($rs as $k => $v){
      $dir = wfFilesystem::getScandir(wfGlobals::getAppDir().$this->settings->get('mysql/mail_queue_admin_attachment_folder').'/'.$v['id']);
      if($dir){
        $s = nulL;
        foreach($dir as $v2){
          $s .= ', '.$v2;
        }
        $rs[$k]['attachment'] = sizeof($dir).$s;
      }else{
        $rs[$k]['attachment'] = null;
      }
    }
    /**
     * 
     */
    return $rs;
  }
  private function db_send_list(){
    $this->db_open();
    $sql = $this->getSql('send_list');
    $this->mysql->execute($sql->get());
    $rs = $this->mysql->getMany();
    return $rs;
  }
}