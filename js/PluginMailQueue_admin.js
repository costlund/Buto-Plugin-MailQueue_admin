function PluginMailQueue_admin(){
  this.list_id = null;
  this.queue_view = function(data){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_view', label: 'Mail', size: 'xl', url: '/mailqueueadmin/queue_view?id='+data.id});
  }
  this.queue_delete = function(data){
    PluginWfBootstrapjs.modal({id: 'modal_mailqueueadmin_delete', label: 'Delete', size: 'sm', url: '/mailqueueadmin/queue_delete?id='+data.id, fade: false});
  }
  this.queue_delete_done = function(data){
    $('#modal_mailqueueadmin_view').modal('hide');
    $('#modal_mailqueueadmin_delete').modal('hide');
    $('#'+this.list_id).DataTable().ajax.reload();
  }
}
var PluginMailQueue_admin = new PluginMailQueue_admin();