    <div class="guestbook_head">

      <!-- {if $CURRENT.GUESTBOOK_OPINION || $CURRENT.GUESTBOOK_HOMEPAGE || $CURRENT.GUESTBOOK_MAIL || $CURRENT.GUESTBOOK_HOMETOWN || ($PERMISSION==100 && $CURRENT.GUESTBOOK_IP)} -->
        <div class="guestbook_details" style="float: left;">
          <div class="guestbook_details_title">{lang id="7"} ...</div>
          <div class="guestbook_details_content">
            <!-- {if $CURRENT.GUESTBOOK_OPINION} -->
            <div class="label">
              {lang id="1"}
              {if $CURRENT.GUESTBOOK_OPINION==1}{lang id="RATE_1"}
              {elseif $CURRENT.GUESTBOOK_OPINION==2}{lang id="RATE_2"}
              {elseif $CURRENT.GUESTBOOK_OPINION==3}{lang id="RATE_3"}
              {elseif $CURRENT.GUESTBOOK_OPINION==4}{lang id="RATE_4"}
              {elseif $CURRENT.GUESTBOOK_OPINION==5}{lang id="RATE_5"}
              {else}{$CURRENT.GUESTBOOK_OPINION}{/if}
            </div>
            <!-- {/if} -->
            <!-- {if $CURRENT.GUESTBOOK_HOMEPAGE} -->
            <div>
              <img alt="" src="data/homepage.gif" style="margin-right: 5px;"/>
              <span class="label">{lang id="2"}:</span>
              <span class="guestbook_value">{$CURRENT.GUESTBOOK_HOMEPAGE}</span>
            </div>
            <!-- {/if} -->
            <!-- {if $CURRENT.GUESTBOOK_MAIL} -->
            <div>
              <img alt="" src="data/mail.gif" style="margin-right: 5px;"/>
              <span class="label">{lang id="3"}:</span>
              <span class="guestbook_value">
              {mailto address=$CURRENT.GUESTBOOK_MAIL text=$CURRENT.GUESTBOOK_MAIL encode="javascript_charcode" extra='language="JavaScript"'}
              </span>
            </div>
            <!-- {/if} -->
            <!-- {if $CURRENT.GUESTBOOK_HOMETOWN} -->
            <div>
              <img alt="" src="data/location.gif" style="margin-right: 5px;"/>
              <span class="label">{lang id="4"}:</span>
              <span class="guestbook_value">{$CURRENT.GUESTBOOK_HOMETOWN}</span>
            </div>
            <!-- {/if} -->
            <!-- {if $PERMISSION==100 && $CURRENT.GUESTBOOK_IP} -->
            <div>
              <span class="label">IP:</span>
              <span class="guestbook_value">{$CURRENT.GUESTBOOK_IP}</span>
            </div>
            <!-- {/if} -->
          </div>
        </div>

      <!-- {/if} -->

      <div class="comment" style="float: right;">
        {$CURRENT.GUESTBOOK_DATE|date}
        <!-- {if $PERMISSION > 74} -->
        <a class="buttonize guestbook_edit" href={"action=$ACTION_EDIT&target="|cat:$CURRENT.GUESTBOOK_ID|href}
           onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION_EDIT}','guestbook_form{$CURRENT.GUESTBOOK_ID}','target={$CURRENT.GUESTBOOK_ID}');document.getElementById('guestbook_entry{$CURRENT.GUESTBOOK_ID}').style.display='none';document.getElementById('guestbook_form{$CURRENT.GUESTBOOK_ID}').style.display='block';return false"
           title='{lang id="16"}'>
          <span class="icon_edit">&nbsp;</span>
        </a>
        <a class="buttonize guestbook_delete" href={"action=$ACTION_DELETE&selected_entries[]="|cat:$CURRENT.GUESTBOOK_ID|href}
           onclick="if (confirm('{lang id="prompt_delete"}')){ldelim}YanaGuestbook.prototype.guestbookRequest('{$ACTION_DELETE}','guestbook_entry{$CURRENT.GUESTBOOK_ID}','selected_entries[]={$CURRENT.GUESTBOOK_ID}');setTimeout('document.getElementById(\'guestbook_entry{$CURRENT.GUESTBOOK_ID}\').style.display=\'none\'',5000);{rdelim}return false"
           title='{lang id="17"}'>
          <span class="icon_delete">&nbsp;</span>
        </a>
        <!-- {/if} -->
      </div>
      <div class="guestbook_name">
      <!-- {if $CURRENT.GUESTBOOK_IS_REGISTERED && $GUESTBOOK_USER_LINK} -->
        <a href="{$GUESTBOOK_USER_LINK}{$CURRENT.GUESTBOOK_NAME}">{$CURRENT.GUESTBOOK_NAME}</a>
      <!-- {else} -->
        {$CURRENT.GUESTBOOK_NAME}
      <!-- {/if} -->
      </div>

    </div>

    <div class="guestbook_message" align="left">

    {$CURRENT.GUESTBOOK_MESSAGE|embeddedTags|smilies}
    </div>
<!-- {if $CURRENT.GUESTBOOK_COMMENT || $PERMISSION > 29} -->
    <div id="comment{$CURRENT.GUESTBOOK_ID}" class="guestbook_comment">
        <!-- {if $CURRENT.GUESTBOOK_COMMENT} -->
            <div class="comment" align="justify">
                <img src="data/comment.png" alt="" style="float: left; margin: 5px;"/>
                <div style="font-weight: bold">{lang id="5"}:</div>
                {$CURRENT.GUESTBOOK_COMMENT|embeddedTags|smilies}
            </div>
            <!-- {if $PERMISSION > 29} -->
              <div class="label">
                  <a onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION_COMMENT}','comment{$CURRENT.GUESTBOOK_ID}','target={$CURRENT.GUESTBOOK_ID}'); return false" title="{lang id="TITLE_UPDATE_COMMENT"}" target="_self" href={"action=$ACTION_COMMENT&target="|cat:$CURRENT.GUESTBOOK_ID|href}>{lang id="TITLE_UPDATE_COMMENT"}</a>
              </div>
            <!-- {/if} -->
        <!-- {elseif $PERMISSION > 29} -->
            <div class="label">
                <a onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION_COMMENT}','comment{$CURRENT.GUESTBOOK_ID}','target={$CURRENT.GUESTBOOK_ID}'); return false" title="{lang id="TITLE_COMMENT"}" target="_self" href={"action=$ACTION_COMMENT&target="|cat:$CURRENT.GUESTBOOK_ID|href}>{lang id="DESCR_COMMENT"}</a>
            </div>
        <!-- {/if} -->
    </div>
<!-- {/if} -->
