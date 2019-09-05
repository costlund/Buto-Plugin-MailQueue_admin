# Buto-Plugin-MailQueue_admin

Page plugin to monitor mail created via PluginMailQueue.

## Settings

```
plugin_modules:
  mailqueueadmin:
    plugin: 'mail/queue_admin'
    settings:
      admin_layout: /theme/_folder_/_folder_/admin_layout_bootstrap4.yml
      mysql: 'yml:/../buto_data/theme/_folder_/_folder_/mysql.yml'
```

## Usage

Go to start page.

```
/mailqueueadmin/start
```
