# Buto-Plugin-MailQueue_admin

<p>Page plugin to monitor mail created via PluginMailQueue.</p>

<a name="key_0"></a>

## Settings



<pre><code>plugin_modules:
  mailqueueadmin:
    plugin: 'mail/queue_admin'
    settings:    </code></pre>
<p>MySQL.</p>
<pre><code>      mysql: 'yml:/../buto_data/theme/_folder_/_folder_/mysql.yml'</code></pre>
<p>Folder for attachments.</p>
<pre><code>      attachment_folder: '/../buto_data/theme/[theme]/mail_queue_attachment'</code></pre>
<p>If no role param is set role webmaster is set. 
This role secure all pages.</p>
<pre><code>      role:
        - webmaster</code></pre>
<p>Add queries to create multiple emails.</p>
<pre><code>      queries:
        -
          name: All emails in account
          sql:
            sql: select distinct email from account where not isnull(email)
            select:
              - email</code></pre>

<a name="key_1"></a>

## Usage



<p>Go to start page.</p>
<pre><code>/mailqueueadmin/start</code></pre>
<p>One could set session param to use a dynamic mysql connection.</p>
<pre><code>$_SESSION['plugin']['mail']['queue_admin']['mysql'] = $mysql;</code></pre>
<p>sd</p>

<a name="key_2"></a>

## Pages





<a name="key_2_0"></a>

### page_chart_error





<a name="key_2_1"></a>

### page_chart_not_sent





<a name="key_2_2"></a>

### page_chart_sent





<a name="key_2_3"></a>

### page_charts





<a name="key_2_4"></a>

### page_create





<a name="key_2_5"></a>

### page_create_capture





<a name="key_2_6"></a>

### page_create_multiple





<a name="key_2_7"></a>

### page_create_multiple_data





<a name="key_2_8"></a>

### page_create_multiple_query





<a name="key_2_9"></a>

### page_create_multiple_query_data





<a name="key_2_10"></a>

### page_edit





<a name="key_2_11"></a>

### page_queue_delete





<a name="key_2_12"></a>

### page_queue_delete_many





<a name="key_2_13"></a>

### page_queue_list





<a name="key_2_14"></a>

### page_queue_list_data





<a name="key_2_15"></a>

### page_queue_view





<a name="key_2_16"></a>

### page_send





<a name="key_2_17"></a>

### page_send_list





<a name="key_2_18"></a>

### page_send_list_data





<a name="key_2_19"></a>

### page_start





<a name="key_3"></a>

## Widgets





<a name="key_3_0"></a>

### widget_include



<p>Include Javascript in html head section.</p>
<pre><code>type: widget
data:
  plugin: 'mail/queue_admin'
  method: include</code></pre>

<a name="key_4"></a>

## Event





<a name="key_5"></a>

## Construct





<a name="key_5_0"></a>

### __construct





<a name="key_6"></a>

## Methods





<a name="key_6_0"></a>

### secure_user





<a name="key_6_1"></a>

### get_queries





<a name="key_6_2"></a>

### get_query





<a name="key_6_3"></a>

### getYml





<a name="key_6_4"></a>

### db_open





<a name="key_6_5"></a>

### getSql





<a name="key_6_6"></a>

### db_queue_list





<a name="key_6_7"></a>

### db_send_list





<a name="key_6_8"></a>

### db_queue_one





<a name="key_6_9"></a>

### db_queue_delete_one





<a name="key_6_10"></a>

### db_queue_update_one





<a name="key_6_11"></a>

### db_queue_select_delete_many





<a name="key_6_12"></a>

### db_queue_delete_many





<a name="key_6_13"></a>

### db_query





<a name="key_6_14"></a>

### db_chart_sent





<a name="key_6_15"></a>

### db_chart_not_sent





<a name="key_6_16"></a>

### db_chart_error





<a name="key_6_17"></a>

### form_create_render





<a name="key_6_18"></a>

### form_create_validate





<a name="key_6_19"></a>

### form_create_capture





