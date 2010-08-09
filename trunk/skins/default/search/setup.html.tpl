    <div class="config_form">
    
    <!-- BEGIN: table -->
    
      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="PROGRAM_TITLE"}{* Stichwortsuche *}</div>
      </div>
    
      <div class="help">
          <div class="help_text">
              {lang id="SEARCH_LINK"}:
              <a href={"action=SEARCH_START"|href} target="_blank">{$PHP_SELF}?action=SEARCH_START{if $ID}&amp;id={$ID}{/if}</a>
          </div>
      </div>

      <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" class="option">
        <input type="hidden" name="action" value="{if !$ID}{if $PERMISSION==100}set_config_default{/if}{else}set_config_profile{/if}"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
        <input type="hidden" name="id" value="{$ID}"/>

        <!-- BEGIN: section -->

        <div class="help">
          {lang id="HELP.0"}
          ({lang id="PREFIX"})
          <ul>
            <li>{lang id="PREFIX_EXAMPLES.0"}</li>
            <li>{lang id="PREFIX_EXAMPLES.1"}</li>
            <li>{lang id="PREFIX_EXAMPLES.2"}</li>
          </ul>
        </div>

        <div class="optionbody">
          <label class="optionitem">
            <span class="label">{lang id="PREFIX"}:</span>
            <input name="SEARCH/PREFIX" type="text" value="{$PROFILE.SEARCH.PREFIX|entities}"/>
          </label>
        </div>

        <div class="help">
          {lang id="HELP.0"}
          ({lang id="TARGET"})
          {lang id="TARGET_DESCRIPTION"}
          <ul>
            <li>{lang id="TARGET_EXAMPLES.0"}</li>
            <li>{lang id="TARGET_EXAMPLES.1"}</li>
            <li>{lang id="TARGET_EXAMPLES.2"}</li>
          </ul>
        </div>

        <div class="optionbody">
          <label class="optionitem">
            <span class="label">{lang id="TARGET"}:</span>
            <input type="text" name="SEARCH/TARGET" value="{$PROFILE.SEARCH.TARGET|entities}"/>
          </label>

         <p align="center">
            <input type="submit" value="{lang id="BUTTON_SAVE"}"/>
            <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
         </p>
       </div>

        <!-- END: section -->

      </form>

    <!-- END: table -->

    <!-- {if $PERMISSION === 100}
         BEGIN: table -->

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="INDEXER.HEADER"}</div>
      </div>

      <div class="help">
          <div class="help_text">
            {lang id="HELP.INDEX"}            
          </div>
      </div>

      <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" class="option" target="_blank">
        <input type="hidden" name="action" value="search_create_index"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
        <input type="hidden" name="id" value="{$ID}"/>

        <!-- BEGIN: section -->

        <div class="optionbody">

          <div class="help">
            {lang id="HELP.0"}
            {lang id="HELP.DIR"}
          </div>

          <label class="optionitem">
            <span class="label">{lang id="INDEXER.DIR"}:</span>
            <input type="text" name="dir" value="{$PROFILE.SEARCH.DIR|entities}" size="50"/>
          </label>

          <div class="help">
            {lang id="HELP.0"}
            {lang id="HELP.RECURSE"}            
          </div>

          <label class="optionitem">
            <span class="label">{lang id="INDEXER.RECURSE"}</span>
            <input type="checkbox" name="recurse" value="1" {if $PROFILE.SEARCH.RECURSE}checked="checked"{/if}/>
          </label>

          <div class="help">
            {lang id="HELP.0"}
            {lang id="HELP.META"}            
          </div>

          <label class="optionitem">
            <span class="label">{lang id="INDEXER.META"}</span>
            <input type="checkbox" name="meta" value="1" {if $PROFILE.SEARCH.META}checked="checked"{/if}/>
          </label>

        </div>

         <p align="center">
            <input type="submit" value="{lang id="OK"}"/>
         </p>

        <!-- END: section -->
      </form>
    
    <!-- END: table {/if} -->

    <!-- {if $PERMISSION === 100}
         BEGIN: table -->

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="UPLOAD.HEADER"}</div>
      </div>
    
      <div class="help">
          <div class="help_text">
            {lang id="HELP.UPLOAD"}
          </div>
      </div>
    
      <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" class="option">
        <input type="hidden" name="action" value="search_write_upload"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
        <input type="hidden" name="id" value="{$ID}"/>

        <!-- BEGIN: section -->

        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="UPLOAD.DOCS"} (documents.dat):</span>
            <input name="documents_dat" type="file" value=""/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="UPLOAD.KEYS"} (keywords.dat):</span>
            <input name="keywords_dat" type="file" value=""/>
          </label>

        </div>

         <p align="center">
            <input type="submit" value="{lang id="OK"}"/>
         </p>

        <!-- END: section -->
      </form>
    
    <!-- END: table {/if} -->
    </div>
