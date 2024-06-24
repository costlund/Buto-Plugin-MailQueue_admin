<?php
class PluginMailQueue_admin{
  private $settings = null;
  private $mysql = null;
  function __construct() {
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
    wfPlugin::enable('mail/queue_admin');
    /**
     * Layout path.
     */
    wfGlobals::setSys('layout_path', '/plugin/mail/queue_admin/layout');
    /**
     * Settings.
     */
    $this->settings = wfPlugin::getModuleSettings('mail/queue_admin', true);
    $this->settings->set('mysql', wfSettings::getSettingsFromYmlString($this->settings->get('mysql')));
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
     * role
     */
    if(!$this->settings->get('role')){
      $this->settings->set('role/', 'webmaster');
    }
    /**
     * Mysql
     */
    wfPlugin::includeonce('wf/mysql');
    $this->mysql = new PluginWfMysql();
  }
  private function secure_user(){
    $exit = true;
    foreach($this->settings->get('role') as $k => $v){
      if(wfUser::hasRole($v)){
        $exit = false;
        break;
      }
    }
    if($exit){
      exit('Role restrictions!');
    }
  }
  public function page_start(){
    $this->secure_user();
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
    $this->secure_user();
    $page = $this->getYml('page/queue_list');
    wfDocument::renderElement($page->get());
  }
  public function page_queue_list_data(){
    $this->secure_user();
    $rs = $this->db_queue_list();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($rs));    
  }
  public function page_send_list(){
    $this->secure_user();
    $page = $this->getYml('page/send_list');
    wfDocument::renderElement($page->get());
  }
  public function page_send_list_data(){
    $this->secure_user();
    $rs = $this->db_send_list();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($rs));    
  }
  public function page_queue_view(){
    $this->secure_user();
    $one = $this->db_queue_one();
    $element = wfDocument::getElementFromFolder(__DIR__, __FUNCTION__);
    $element->setByTag($one->get());
    $element->setByTag(array('one' => $one->get()), 'data');
    wfDocument::renderElement($element);
  }
  public function page_queue_delete(){
    $this->secure_user();
    $one = $this->db_queue_one();
    if(!$one->get('sent')){
      /**
       * attatchments
       */
      if(wfFilesystem::fileExist($one->get('attatchment_folder'))){
        wfFilesystem::delete_dir($one->get('attatchment_folder'));
      }
      /**
       * 
       */
      $this->db_queue_delete_one();
    }
    $element = wfDocument::getElementFromFolder(__DIR__, __FUNCTION__);
    wfDocument::renderElement($element);
  }
  public function widget_include(){
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
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
        $rs[$k]['attachment_count'] = sizeof($dir);
      }else{
        $rs[$k]['attachment_count'] = null;
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
  private function db_queue_one(){
    $this->db_open();
    $sql = $this->getSql(__FUNCTION__);
    $this->mysql->execute($sql->get());
    $rs = $this->mysql->getOne(array('sql' => $sql->get()));
    $dir = wfFilesystem::getScandir(wfGlobals::getAppDir().$this->settings->get('attachment_folder').'/'.$rs->get('id'));
    $rs->set('attatchment_count', sizeof($dir));
    $rs->set('attatchment', $dir);
    $rs->set('attatchment_folder', wfGlobals::getAppDir().$this->settings->get('attachment_folder').'/'.$rs->get('id'));
    return $rs;
  }
  private function db_queue_delete_one(){
    $this->db_open();
    $sql = $this->getSql(__FUNCTION__);
    $this->mysql->execute($sql->get());
    return null;
  }
}