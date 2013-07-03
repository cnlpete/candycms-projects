{strip}
  <div class='page-header'>
    <h1>
      {if $_REQUEST.action == 'create'}
        {$lang.projects.create}
      {else}
        Projekt '{$title}' bearbeiten
      {/if}
    </h1>
  </div>
  {if $_REQUEST.action == 'create'}
    <form method='post'
          class='form-horizontal'
          enctype='multipart/form-data'
          action='/{$_REQUEST.controller}/{$_REQUEST.action}'>
  {elseif $_REQUEST.action == 'update'}
    <form method='post'
          class='form-horizontal'
          enctype='multipart/form-data'
          action='/{$_REQUEST.controller}/{$_REQUEST.id}/{$_REQUEST.action}'>
  {/if}
    <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[title]'
               value="{$title}"
               type='text'
               id='input-title'
               data-maxlength='128'
               class='required js-maxlength'
               required />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea name='{$_REQUEST.controller}[content]'
                  class='js-editor required span5'
                  id='input-content'
                  rows='10'>{$content}</textarea>
        {if isset($error.content)}
          <span class='help-inline'>{$error.content}</span>
        {elseif count($editorinfo) > 0}
          <span class='help-block'>
            {$lang.global.editorinfo}
            &nbsp;
            {foreach $editorinfo as $aMarkup}
              <a href='{$aMarkup.url}' title='{$aMarkup.description}' class='js-tooltip'>
                <img src='{$aMarkup.iconurl}' />
              </a>
            {/foreach}
          </span>
        {/if}
      </div>
    </div>
    <div class='control-group{if isset($error.url_demo)} alert alert-error{/if}'>
      <label for='input-url_demo' class='control-label'>
        {$lang.projects.demo}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[url_demo]'
               value="{$url_demo}"
               type='url'
               id='input-url_demo'
               data-maxlength='128'
               class='js-maxlength' />
        <span class='help-inline'>
          {if isset($error.url_demo)}
            {$error.url_demo}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group{if isset($error.url_project)} alert alert-error{/if}'>
      <label for='input-url_project' class='control-label'>
        {$lang.projects.projectpage}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[url_project]'
               value="{$url_project}"
               type='url'
               id='input-url_project'
               data-maxlength='128'
               class='js-maxlength' />
        <span class='help-inline'>
          {if isset($error.url_project)}
            {$error.url_project}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-published' class='control-label'>
        {$lang.global.published}
      </label>
      <div class='controls'>
        <input name='{$_REQUEST.controller}[published]'
               value='1'
               type='checkbox'
               class='checkbox'
               id='input-published'
               {if $published == true}checked{/if} />
      </div>
    </div>
    <div data-role='fieldcontain'>
      <div class='form-actions' data-role='controlgroup'>
        {if $_REQUEST.action == 'create'}
          <input type='submit'
                 class='btn btn-primary'
                 value="{$lang.global.create.create}"
                 data-theme='b' />
        {elseif $_REQUEST.action == 'update'}
          <input type='submit'
                 class='btn btn-primary'
                 value="{$lang.global.update.update}"
                 data-theme='b' />
          <input type='button'
                 class='btn btn-danger'
                 value='{$lang.global.destroy.destroy}'
                 onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
          <input type='reset'
                 class='btn'
                 value='{$lang.global.reset}' />
        {/if}
      </div>
    </div>
  </form>
  <script type='text/javascript' src='{$_PATH.js.bootstrap}/bootstrap-typeahead{$_SYSTEM.compress_files_suffix}.js'></script>
  {if !$MOBILE}
    <!-- pluginmanager:editor -->
  {/if}
  <script type='text/javascript'>
    $('input.js-maxlength').bind('keyup', function() {
      countCharLength(this, $(this).data('maxlength'));
    });
  </script>
{/strip}
