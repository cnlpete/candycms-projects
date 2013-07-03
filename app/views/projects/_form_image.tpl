{strip}
  <form method='post' class='form-horizontal'>
  <div class='control-group{if isset($error.file)} alert alert-error{/if}'>
    <label for='input-file' class='control-label'>
      {$lang.galleries.files.label.choose} <span title="{$lang.global.required}">*</span><br />
      <small>
        {if $_SYSTEM.maximumUploadSize.raw <= 1536}
          {$_SYSTEM.maximumUploadSize.b|string_format: $lang.global.upload.maxsize}
        {elseif $_SYSTEM.maximumUploadSize.raw <= 1572864}
          {$_SYSTEM.maximumUploadSize.kb|string_format: $lang.global.upload.maxsize}
        {else}
          {$_SYSTEM.maximumUploadSize.mb|string_format: $lang.global.upload.maxsize}
        {/if}
      </small>
    </label>
    <div class='controls'>
      <input class='span4 required'
              type='file'
              name='file[]'
              id='input-file'
              multiple required />
      {if isset($error.file)}<span class='help-inline'>{$error.file}</span>{/if}
    </div>
  </div>
  <div class='control-group'>
    <label for='input-cut' class='control-label'>
      {$lang.global.cut} <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <label class='radio'>
        <input type='radio'
                value='c'
                name='{$_REQUEST.controller}[cut]'
                {if !$REQUEST.cut || ($_REQUEST.cut && 'c' == $_REQUEST.cut)}
                  checked='checked'
                {/if} />
        {$lang.galleries.files.label.cut}
      </label>
      <label class='radio'>
        <input type='radio'
                value='r'
                name='{$_REQUEST.controller}[cut]'
                {if $_REQUEST.cut && 'r' == $_REQUEST.cut}
                  checked='checked'
                {/if} />
        {$lang.galleries.files.label.resize}
      </label>
    </div>
  </div>

  <div class='control-group hide' id='js-progress'>
    <label class='control-label'>
      {$lang.global.upload.status}
    </label>
    <div class='controls'>
      <div class='progress progress-success progress-striped active'
          role='progressbar'
          aria-valuemin='0'
          aria-valuemax='100'>
        <div id='js-progress_bar' class='bar'></div>
      </div>
    </div>
  </div>
  <div class='form-actions'>
    <input type='button'
           id='js-submit_files'
           class='btn btn-primary'
           value='{$lang.projects.files.title.create}' />
  </div>
  </form>
  <script type='text/javascript' src='{$_PATH.js.core}/jquery.ui{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('#input-file').change(function() {
        checkFileSize($(this), {$_SYSTEM.maximumUploadSize.raw}, '{$_SYSTEM.maximumUploadSize.mb|string_format: $lang.error.file.size}');
        prepareForUpload();
      });

      $('#js-submit_files').click(function() {
        upload(this, '{$_REQUEST.controller}/{$_REQUEST.id}/createfile', {$_REQUEST.controller}, 'file', 'cut', true);
      });
    });
  </script>
{/strip}
