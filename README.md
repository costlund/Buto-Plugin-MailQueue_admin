# Buto-Plugin-MailQueue_admin

Page plugin to monitor mail created via PluginMailQueue.

## Settings
```
plugin_modules:
  mailqueueadmin:
    plugin: 'mail/queue_admin'
    settings:    
```
MySQL.
```
      mysql: 'yml:/../buto_data/theme/_folder_/_folder_/mysql.yml'
```
Folder for attachments.
```
      attachment_folder: '/../buto_data/theme/[theme]/mail_queue_attachment'
```
If no role param is set role webmaster is set. 
This role secure all pages.
```
      role:
        - webmaster
```
Add queries to create multiple emails.
```
      queries:
        -
          name: All emails in account
          sql:
            sql: select distinct email from account where not isnull(email)
            select:
              - email
```

### Include
Include Javascript in html head section.
```
type: widget
data:
  plugin: 'mail/queue_admin'
  method: include
```

## Usage

Go to start page.

```
/mailqueueadmin/start
```

## Session

One could set session param to use a dynamic mysql connection.

```
$_SESSION['plugin']['mail']['queue_admin']['mysql'] = $mysql;
```

