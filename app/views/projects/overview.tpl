{strip}
  {if $_SESSION.user.role >= 3}
    <p class='center'>
      <a href='/{$_REQUEST.controller}/create'>
        <i class='icon-plus'
           title='{$lang.global.create.entry}'></i>
        {$lang.projects.title.create}
      </a>
    </p>
  {/if}
  {if !$projects}
    <div class='alert alert-warning'>
      <h4>{$lang.error.missing.entries}</h4>
    </div>
  {else}
    <div class='page-header'>
    </div>
    <div>
      {foreach $projects as $p}
        <div class="project">
          {if $p.images.thumbnail}
            <a href="{$p.images.thumbnail}" class="lightbox">
              <img src="{$p.images.thumbnail}" />
            </a>
          {/if}
          <div>
            <h3 class="boxheadline">
              {if $p.published && !empty($p.url_project)}
                <a href="{$p.url_project}">{$p.title}</a>
              {else}
                {$p.title}
              {/if}
              {if $_SESSION.user.role >= 3}
                <a href='{$p.url_update}'>
                  <i class='icon-pencil js-tooltip'
                    title='{$lang.global.update.update}'></i>
                </a>
                <a href='{$p.url_createfile}'>
                  <i class='icon-upload js-tooltip'
                    title='{$lang.projects.files.title.create}'></i>
                </a>
              {/if}
            </h3>
            <ul class="links">
              <li><a href='{$p.author.url}' rel='author'>{$p.author.full_name}</a></li>
              {if $p.published}
                {if !empty($p.url_demo)}
                  <li><a href="{$p.url_demo}">{$lang.projects.demo}</a></li>
                {/if}
                {if !empty($p.url_project)}
                  <li><a href="{$p.url_project}">{$lang.projects.projectpage}</a></li>
                {/if}
              {/if}
            </ul>
            {$p.content}
            {if $p.thumbnails}
              <ul class="thumbnails">
                {foreach $p.thumbnails as $t}
                  <li>
                    <a href='{$t.url_popup}'
                        class='thumbnail fancybox-thumb'
                        rel='fancybox-thumb'
                        data-fancybox-group="{$p.id}">
                      <img src='{$t.url_32}'
                           height='32' width='32' />
                    </a>
                  </li>
                {/foreach}
              </ul>
            {/if}
          </div>
        </div>
      {/foreach}
    </div>
  {/if}
  <script type='text/javascript' src='{$_PATH.js.core}/jquery.fancybox{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript' src='{$_PATH.js.core}/jquery.fancybox-thumbs{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('.thumbnail').fancybox({
        nextEffect: 'fade',
        prevEffect: 'fade',
        helpers: {
          thumbs: {
            width:  80,
            height: 80
          }
        }
      });

      $('.thumbnail').each(function(){
        /*random number between -20 and 20 degree*/
        var r = Math.floor((Math.random() * 20) - 10);
        $(this).css('-moz-transform','rotate('+r+'deg)')
              .css('-webkit-transform','rotate('+r+'deg)')
              .css('-o-transform','rotate('+r+'deg)')
              .css('-ms-transform','rotate('+r+'deg)');
      });
    });
  </script>
{/strip}
