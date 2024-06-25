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
    /**
     * 
     */
    if(strlen(wfRequest::get('key'))){
      wfRequest::set('has_key', true);
    }else{
      wfRequest::set('has_key', false);
    }
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
  public function page_create(){
    $this->secure_user();
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
  }
  public function page_create_capture(){
    $this->secure_user();
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
  }
  public function page_create_multiple(){
    $this->secure_user();
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
  }
  public function page_create_multiple_data(){
    $this->secure_user();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($this->get_queries()));    
  }
  public function page_create_multiple_query(){
    $this->secure_user();
    $element = wfDocument::getElementFromFolder(__DIR__, __FUNCTION__);
    $element->setByTag(wfRequest::getAll());
    wfDocument::renderElement($element);
  }
  public function page_create_multiple_query_data(){
    $this->secure_user();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($this->get_query()->get('rs')));    
  }
  public function page_queue_delete_many(){
    $rows = $this->db_queue_delete_many();
    $element = wfDocument::getElementFromFolder(__DIR__, __FUNCTION__);
    $element->setByTag(array('rows' => $rows));
    wfDocument::renderElement($element);
  }
  public function widget_include(){
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
  }
  private function get_queries(){
    if(!$this->settings->get('queries')){
      $this->settings->set('queries', array());
    }
    foreach($this->settings->get('queries') as $k => $v){
      $this->settings->set("queries/$k/key", $k);
    }
    return $this->settings->get('queries');
  }
  private function get_query(){
    $key = wfRequest::getInt('key');
    $query = new PluginWfArray($this->settings->get("queries/$key"));
    $query->set('rs', $this->db_query($query->get('sql')));
    return $query;
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
     * session
     */
    if(sizeof($rs)){
      wfUser::setSession('plugin/mail/queue_admin/queue_list/last_created_at', $rs[0]['created_at']);
    }else{
      wfUser::setSession('plugin/mail/queue_admin/queue_list/last_created_at', date('Y-m-d H:i:s'));
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
  private function db_queue_delete_many(){
    $this->db_open();
    $sql = $this->getSql(__FUNCTION__);
    $this->mysql->execute($sql->get());
    return $this->mysql->affected_rows;
  }
  private function db_query($query){
    $this->db_open();
    $sql = new PluginWfArray($query);
    $this->mysql->execute($sql->get());
    return $this->mysql->getMany();
  }
  public function form_create_validate($form){
    $form = new PluginWfArray($form);
    $key = wfRequest::get('key');
    if(strlen($key)){
      $form->set('items/mail_to/mandatory', false);
    }else{
      $form->set('items/key/mandatory', false);
    }
    return $form->get();
  }
  public function form_create_capture($data){
    wfPlugin::includeonce('mail/queue');
    $mail = new PluginMailQueue(true);
    $mail->set_settings('data/mysql', $this->settings->get('mysql'));
    /**
     * 
     */
    $key = wfRequest::get('key');
    if(strlen($key)){
      $query = $this->get_query();
      foreach($query->get('rs') as $k => $v){
        $mail->create(
          wfRequest::get('subject'), 
          wfRequest::get('body'), 
          $v['email'], 
          null, 
          null,
          null, 
          wfRequest::get('rank'), 
          null, 
          wfRequest::get('tag'),
          wfRequest::get('mail_from'),
          wfRequest::get('from_name')
          );
      }
    }else{
      $mail->create(
        wfRequest::get('subject'), 
        wfRequest::get('body'), 
        wfRequest::get('mail_to'), 
        null, 
        null,
        null, 
        wfRequest::get('rank'), 
        null, 
        wfRequest::get('tag'),
        wfRequest::get('mail_from'),
        wfRequest::get('from_name')
        );
    }
    /**
     * 
     */
    return array("PluginMailQueue_admin.form_create_capture()");
  }
}