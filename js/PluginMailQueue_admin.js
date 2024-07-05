function PluginMailQueue_admin(){
  this.list_id = 'mailqueuadmin_list';
  this.start = function(){
    window.open('/mailqueueadmin/start');
  }
  this.queue_view = function(data){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_view', label: 'Mail', url: '/mailqueueadmin/queue_view?id='+data.id});
  }
  this.queue_delete = function(data){
    PluginWfBootstrapjs.confirm({content: 'Are you sure to delete?', method: function(){PluginMailQueue_admin.queue_delete_confirmed();}, data: data });
  }
  this.queue_delete_confirmed = function(){
    if(PluginWfBootstrapjs.confirm_data.ok){
      PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_delete', label: 'Delete', size: 'sm', url: '/mailqueueadmin/queue_delete?id='+PluginWfBootstrapjs.confirm_data.data.id, fade: false});
    }
  }
  this.queue_delete_done = function(data){
    $('#modal_mailqueueadmin_view').modal('hide');
    $('#modal_mailqueueadmin_delete').modal('hide');
    $('#'+this.list_id).DataTable().ajax.reload();
  }
  this.create = function(key, name){
    if(undefined == key){
      key = '';
    }
    if(undefined == name){
      name = '';
    }
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_create', label: 'Create', url: '/[[class]]/create?key='+key+'&name='+name});
  }
  this.edit = function(data){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_edit', label: 'Create', url: '/mailqueueadmin/edit?id='+data.id});
  }
  this.form_create_capture = function(){
    if(document.getElementById('modal_mailqueueadmin_view_body')){
      $('#modal_mailqueueadmin_edit').modal('hide');
      PluginWfAjax.update('modal_mailqueueadmin_view_body');
    }else{
      $('#modal_mailqueueadmin_create').modal('hide');
      $('#modal_mailqueueadmin_create_multiple').modal('hide');
      $('#modal_mailqueueadmin_create_multiple_query').modal('hide');
    }
    $('#'+this.list_id).DataTable().ajax.reload();
  }
  this.create_multiple = function(){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_create_multiple', label: 'Create multiple', size: 'lg', url: '/[[class]]/create_multiple'});
  }
  this.create_multiple_query = function(data){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_create_multiple_query', label: 'Mail', size: 'lg', url: '/mailqueueadmin/create_multiple_query?key='+data.key+'&name='+data.name});
  }
  this.queue_delete_many = function(){
    PluginWfBootstrapjs.confirm({content: 'Are you sure to delete all not sent?', method: function(){PluginMailQueue_admin.queue_delete_many_confirmed();}, data: {} });
  }
  this.queue_delete_many_confirmed = function(data){
    if(PluginWfBootstrapjs.confirm_data.ok){
      PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_delete_many', label: 'Delete not sent', size: 'sm', url: '/mailqueueadmin/queue_delete_many'});
    }
  }
  this.queue_delete_many_done = function(data){
    $('#'+this.list_id).DataTable().ajax.reload();
  }
}
var PluginMailQueue_admin = new PluginMailQueue_admin();